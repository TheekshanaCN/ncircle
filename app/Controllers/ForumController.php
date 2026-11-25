<?php
/** app/Controllers/ForumController.php  -  questions and answers */
class ForumController extends Controller
{
    /** The question, or a 404 when it is hidden or belongs to another faculty. The asker can always see their own. */
    private function loadVisible(int $id): array
    {
        $question = $this->orNotFound(Question::find($id));
        $user = Auth::user();
        $mine = (int) $question["user_id"] === (int) $user["id"];
        if (!$mine && !Auth::canSeeFaculty($question["subject_faculty"])) {
            Response::error(404);
        }
        if ($question["is_hidden"] && $user["role"] !== "admin") {
            Response::error(404);
        }
        return $question;
    }

    public function index(): void
    {
        $status = (string) Request::query("status");
        $filters = [
            "q" => mb_substr((string) Request::query("q"), 0, 100),
            "subject" => (int) Request::query("subject"),
            "faculty" => Auth::facultyScope(),
            "status" => in_array($status, ["solved", "active", "unanswered"], true) ? $status : "",
        ];
        $this->view("forum/index", [
            "title" => "Discussion forum | Ncircle",
            "pageCss" => "forum",
            "filters" => $filters,
            "result" => Question::search($filters, $this->page()),
            "page" => $this->page(),
            "subjects" => Subject::options(Auth::facultyScope()),
        ], "app");
    }

    public function create(): void
    {
        $this->view("forum/create", [
            "title" => "Ask a question | Ncircle",
            "pageCss" => "forum",
            "subjects" => Subject::options(Auth::facultyScope()),
            "question" => null,
        ], "app");
    }

    /** $currentSubject stays allowed when editing, even if it was switched off later. */
    private function validatedQuestion(int $currentSubject = 0): array
    {
        $data = $this->validate([
            "title" => "required|min:10|max:200",
            "subject_id" => "required|int",
            "body" => "required|min:10|max:5000",
        ], ["subject_id" => "Subject", "body" => "Details"]);
        $subjectId = (int) $data["subject_id"];
        if ($subjectId !== $currentSubject && !Subject::isActive($subjectId, Auth::facultyScope())) {
            $this->failValidation(["subject_id" => "Choose a valid subject."], $data);
        }
        return $data;
    }

    public function store(): void
    {
        $data = $this->validatedQuestion();
        $media = $this->prepareMedia($data);
        $id = Question::create(Auth::id(), (int) $data["subject_id"], $data["title"], $data["body"]);
        AttachmentService::save("question", $id, Auth::id(), $media);
        Flash::add("success", "Your question is live.");
        Response::redirect("/forum/" . $id);
    }

    public function show(int $id): void
    {
        $user = Auth::user();
        $question = $this->loadVisible($id);
        // Count one view per question per session, so a refresh does not inflate it.
        $seen = Session::get("seen_questions", []);
        if (!in_array($id, $seen, true) && (int) $question["user_id"] !== (int) $user["id"]) {
            Question::addView($id);
            $question["views"]++;
            $seen[] = $id;
            Session::set("seen_questions", $seen);
        }
        $this->view("forum/show", [
            "title" => $question["title"] . " | Ncircle",
            "pageCss" => "forum",
            "question" => $question,
            "attachments" => Attachment::forOwners("question", [$id])[$id] ?? [],
            "answers" => Attachment::attachTo(Answer::forQuestion($id, (int) $user["id"]), "answer"),
            "isOwner" => (int) $question["user_id"] === (int) $user["id"],
            "isAdmin" => $user["role"] === "admin",
        ], "app");
    }

    /** Only the person who asked can edit the question. */
    private function loadOwnQuestion(int $id): array
    {
        $question = $this->loadVisible($id);
        if ((int) $question["user_id"] !== Auth::id()) {
            Response::error(403);
        }
        return $question;
    }

    public function edit(int $id): void
    {
        $question = $this->loadOwnQuestion($id);
        $this->view("forum/create", [
            "title" => "Edit question | Ncircle",
            "pageCss" => "forum",
            "subjects" => Subject::optionsWith($question, Auth::facultyScope()),
            "question" => $question,
        ], "app");
    }

    public function update(int $id): void
    {
        $question = $this->loadOwnQuestion($id);
        $data = $this->validatedQuestion((int) $question["subject_id"]);
        Question::update($id, (int) $data["subject_id"], $data["title"], $data["body"]);
        Flash::add("success", "Question updated.");
        Response::redirect("/forum/" . $id);
    }

    public function destroy(int $id): void
    {
        $user = Auth::user();
        $question = $this->orNotFound(Question::find($id));
        if ((int) $question["user_id"] !== (int) $user["id"] && $user["role"] !== "admin") {
            Response::error(403);
        }
        Question::delete($id);
        Flash::add("success", "Question deleted.");
        Response::redirect("/forum");
    }

    public function answer(int $id): void
    {
        $user = Auth::user();
        $question = $this->loadVisible($id);
        if ($question["is_hidden"]) {
            Response::error(404);
        }
        $data = $this->validate(["body" => "max:5000"], ["body" => "Answer"]);
        $media = $this->prepareMedia($data);
        if (($data["body"] ?? "") === "" && !AttachmentService::hasContent($media)) {
            $this->failValidation(["body" => "Write an answer, or add an image, file or GIF."], $data);
        }
        $answerId = Answer::create($id, (int) $user["id"], $data["body"] ?? "");
        AttachmentService::save("answer", $answerId, (int) $user["id"], $media);
        NotificationService::questionAnswered($question, (int) $user["id"], $user["name"]);
        Response::redirect("/forum/" . $id . "#answers");
    }

    /** Only its author can edit an answer, and not after a moderator hides it. */
    private function loadOwnAnswer(int $id): array
    {
        $answer = $this->orNotFound(Answer::find($id));
        $this->loadVisible((int) $answer["question_id"]);
        if ($answer["is_hidden"]) {
            Response::error(404);
        }
        if ((int) $answer["user_id"] !== Auth::id()) {
            Response::error(403);
        }
        return $answer;
    }

    public function editAnswer(int $id): void
    {
        $answer = $this->loadOwnAnswer($id);
        $this->view("forum/edit-answer", [
            "title" => "Edit answer | Ncircle",
            "pageCss" => "forum",
            "answer" => $answer,
        ], "app");
    }

    public function updateAnswer(int $id): void
    {
        $answer = $this->loadOwnAnswer($id);
        $data = $this->validate(["body" => "max:5000"], ["body" => "Answer"]);
        $body = (string) ($data["body"] ?? "");
        if ($body === "" && !Attachment::forOwners("answer", [$id])) {
            $this->failValidation(["body" => "Write an answer."], $data);
        }
        Answer::update($id, $body);
        Flash::add("success", "Answer updated.");
        Response::redirect("/forum/" . $answer["question_id"] . "#answer-" . $id);
    }

    /** Its author or an admin can delete an answer. */
    public function destroyAnswer(int $id): void
    {
        $answer = $this->orNotFound(Answer::find($id));
        $this->loadVisible((int) $answer["question_id"]);
        if ((int) $answer["user_id"] !== Auth::id() && !Auth::isAdmin()) {
            Response::error(403);
        }
        Answer::delete($answer);
        Flash::add("success", "Answer deleted.");
        Response::redirect("/forum/" . $answer["question_id"] . "#answers");
    }

    /** Checks uploads and the GIF before anything is saved, so a bad file saves nothing. */
    private function prepareMedia(array $data): array
    {
        $media = AttachmentService::prepare($_FILES["attachments"] ?? null, (string) Request::input("gif_id"));
        if (isset($media["error"])) {
            $this->failValidation(["attachments" => $media["error"]], $data);
        }
        return $media;
    }

    /** Only the person who asked can accept an answer. */
    public function accept(int $id): void
    {
        $answer = $this->orNotFound(Answer::find($id));
        $question = $this->loadVisible((int) $answer["question_id"]);
        if ((int) $question["user_id"] !== Auth::id()) {
            Response::error(403);
        }
        Answer::accept($answer);
        NotificationService::answerAccepted($question, $answer);
        Flash::add("success", "Marked as the accepted answer.");
        Response::redirect("/forum/" . $question["id"]);
    }

    public function vote(int $id): void
    {
        $answer = $this->orNotFound(Answer::find($id));
        $this->loadVisible((int) $answer["question_id"]);
        if ((int) $answer["user_id"] !== Auth::id()) {
            Answer::toggleVote($id, Auth::id());
        } else {
            Flash::add("info", "You cannot upvote your own answer.");
        }
        Response::redirect("/forum/" . $answer["question_id"] . "#answer-" . $id);
    }
}
