<?php
/** app/Controllers/SearchController.php  -  the top bar search across notes, threads, groups and pods */
class SearchController extends Controller
{
    public function index(): void
    {
        $q = (string) Request::query("q");
        $userId = Auth::id();
        $scope = Auth::facultyScope();
        $this->view("search/index", [
            "title" => "Search | Ncircle",
            "pageCss" => "resources",
            "q" => $q,
            "notes" => $q !== "" ? Resource::search(["q" => $q, "faculty" => $scope ?? ""], $userId, 1, 4) : null,
            "threads" => $q !== "" ? Question::search(["q" => $q, "faculty" => $scope], 1, 4) : null,
            "groups" => $q !== "" ? Group::search(["q" => $q, "faculty" => $scope], $userId, 1, 3) : null,
            "sessions" => $q !== "" ? StudySession::search(["q" => $q, "faculty" => $scope], $userId, 1, 3) : null,
        ], "app");
    }
}
