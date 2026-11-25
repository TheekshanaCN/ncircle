<?php
/** app/Controllers/ResourceController.php  -  browse, upload, view and download notes */
class ResourceController extends Controller
{
    private const MIN_RATINGS = ["4.5", "4"];

    public function index(): void
    {
        $filters = $this->filters();
        $page = $this->page();
        $result = Resource::search($filters, Auth::id(), $page);

        // "Load more" asks for just the next batch of cards.
        if (Request::query("partial") === "1") {
            foreach ($result["rows"] as $resource) {
                echo component("note-card", ["resource" => $resource]);
            }
            return;
        }

        $this->view("resources/index", [
            "title" => "Browse notes | Ncircle",
            "pageCss" => "resources",
            "filters" => $filters,
            "result" => $result,
            "subjects" => Subject::active(Auth::facultyScope()),
            "categories" => Category::all(),
            "categoryCounts" => Resource::categoryCounts($filters),
            "subjectCounts" => Resource::subjectCounts($filters),
            "verified" => Resource::search(["faculty" => Auth::facultyScope() ?? ""], Auth::id(), 1, 1)["total"],
        ], "app");
    }

    /** Reads the query string, ignoring anything that is not a known value. */
    private function filters(): array
    {
        $subjects = $_GET["subject"] ?? [];
        $subjects = is_array($subjects) ? array_values(array_filter(array_map("intval", $subjects))) : [];
        $category = (string) Request::query("category");
        $year = (int) Request::query("year");
        $format = (string) Request::query("format");
        $minRating = (string) Request::query("min_rating");
        $sort = (string) Request::query("sort", "relevance");
        $faculty = (string) Request::query("faculty");

        return [
            "q" => mb_substr((string) Request::query("q"), 0, 100),
            "subjects" => $subjects,
            "category" => in_array($category, array_column(Category::all(), "slug"), true) ? $category : "",
            "year" => $year >= 1 && $year <= 4 ? $year : 0,
            // Students are always limited to their own faculty. Only admins can choose.
            "faculty" => Auth::facultyScope() ?? (isset(config("app.faculties")[$faculty]) ? $faculty : ""),
            "format" => isset(config("upload.types")[$format]) ? $format : "",
            "min_rating" => in_array($minRating, self::MIN_RATINGS, true) ? $minRating : "",
            "sort" => isset(Resource::SORTS[$sort]) ? $sort : "relevance",
        ];
    }

    public function create(): void
    {
        $this->view("resources/create", [
            "title" => "Share a note | Ncircle",
            "pageCss" => "resources",
            "subjects" => Subject::options(Auth::facultyScope()),
            "categories" => Category::options(),
            "resource" => null,
        ], "app");
    }

    public function store(): void
    {
        $data = $this->validatedNote();
        $stored = UploadService::store($_FILES["file"] ?? null);
        if (isset($stored["error"])) {
            $this->failValidation(["file" => $stored["error"]], $data);
        }

        $id = Resource::create(array_merge($stored, [
            "user_id" => Auth::id(),
            "subject_id" => (int) $data["subject_id"],
            "category_id" => (int) $data["category_id"],
            "title" => $data["title"],
            "description" => $data["description"],
            "pages" => $data["pages"],
        ]));
        Flash::add("success", "Uploaded. An admin will review it soon.");
        Response::redirect("/resources/" . $id);
    }

    public function edit(int $id): void
    {
        $resource = $this->loadOwn($id);
        $this->view("resources/create", [
            "title" => "Edit note | Ncircle",
            "pageCss" => "resources",
            "subjects" => Subject::optionsWith($resource, Auth::facultyScope()),
            "categories" => Category::options(),
            "resource" => $resource,
        ], "app");
    }

    /** Every edit goes back to review, so approved text cannot be swapped for something unchecked. */
    public function update(int $id): void
    {
        $resource = $this->loadOwn($id);
        Resource::update($id, $this->validatedNote((int) $resource["subject_id"]));
        Flash::add("success", "Changes saved. An admin will review them soon.");
        Response::redirect("/resources/" . $id);
    }

    private function loadOwn(int $id): array
    {
        $user = Auth::user();
        $resource = $this->orNotFound(Resource::find($id));
        if (!Resource::canView($resource, $user)) {
            Response::error(404);
        }
        if ((int) $resource["user_id"] !== (int) $user["id"]) {
            Response::error(403);
        }
        return $resource;
    }

    /** $currentSubject stays allowed when editing, even if it was switched off later. */
    private function validatedNote(int $currentSubject = 0): array
    {
        $data = $this->validate([
            "title" => "required|min:5|max:160",
            "subject_id" => "required|int",
            "category_id" => "required|int",
            "description" => "required|min:10|max:2000",
            "pages" => "between:1,5000",
        ], ["subject_id" => "Subject", "category_id" => "Category"]);

        $errors = [];
        $subjectId = (int) $data["subject_id"];
        if ($subjectId !== $currentSubject && !Subject::isActive($subjectId, Auth::facultyScope())) {
            $errors["subject_id"] = "Choose a valid subject.";
        }
        if (!Category::exists((int) $data["category_id"])) {
            $errors["category_id"] = "Choose a valid category.";
        }
        if ($errors) {
            $this->failValidation($errors, $data);
        }
        $data["pages"] = ($data["pages"] ?? "") !== "" ? (int) $data["pages"] : null;
        return $data;
    }

    public function show(int $id): void
    {
        $user = Auth::user();
        $resource = $this->orNotFound(Resource::find($id, (int) $user["id"]));
        if (!Resource::canView($resource, $user)) {
            Response::error(404);
        }
        $this->view("resources/show", [
            "title" => $resource["title"] . " | Ncircle",
            "pageCss" => "resources",
            "resource" => $resource,
            "comments" => Comment::forResource($id),
            "myRating" => Rating::forUser($id, (int) $user["id"]),
            "related" => Resource::related($resource),
            "isOwner" => (int) $resource["user_id"] === (int) $user["id"],
            "isAdmin" => $user["role"] === "admin",
        ], "app");
    }

    public function download(int $id): void
    {
        $user = Auth::user();
        $resource = $this->orNotFound(Resource::find($id));
        if (!Resource::canView($resource, $user)) {
            Response::error(404);
        }
        $path = UploadService::path($resource["file_path"]);
        if (!is_file($path)) {
            Flash::add("danger", "This file is no longer available.");
            Response::redirect("/resources/" . $id);
        }
        if ($resource["status"] === "approved") {
            Download::record($id, (int) $user["id"]);
        }
        $name = preg_replace('/[^\w.\- ]+/u', "_", $resource["original_name"]);
        header("Content-Type: application/octet-stream");
        header("X-Content-Type-Options: nosniff");
        header('Content-Disposition: attachment; filename="' . $name . '"');
        header("Content-Length: " . filesize($path));
        readfile($path);
    }

    public function destroy(int $id): void
    {
        $user = Auth::user();
        $resource = $this->orNotFound(Resource::find($id));
        if ((int) $resource["user_id"] !== (int) $user["id"] && $user["role"] !== "admin") {
            Response::error(403);
        }
        Resource::delete($id);
        UploadService::delete($resource["file_path"]);
        Flash::add("success", "Note deleted.");
        Response::redirect("/space");
    }

    public function bookmark(int $id): void
    {
        $user = Auth::user();
        $resource = $this->orNotFound(Resource::find($id));
        if ($resource["status"] !== "approved" || !Resource::canView($resource, $user)) {
            Response::error(404);
        }
        $saved = Resource::toggleBookmark((int) $user["id"], $id);
        if (Request::wantsJson()) {
            Response::json(["success" => true, "message" => $saved ? "Saved to your study space." : "Removed from your study space.", "data" => ["saved" => $saved]]);
        }
        Response::back("/resources/" . $id);
    }

    public function comment(int $id): void
    {
        $user = Auth::user();
        $resource = $this->orNotFound(Resource::find($id));
        if ($resource["status"] !== "approved" || !Resource::canView($resource, $user)) {
            Response::error(404);
        }
        $data = $this->validate(["body" => "required|min:2|max:1000"], ["body" => "Comment"]);
        Comment::create($id, (int) $user["id"], $data["body"]);
        NotificationService::noteCommented($resource, (int) $user["id"], $user["name"]);
        Response::redirect("/resources/" . $id . "#comments");
    }
}
