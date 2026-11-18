<?php
/** app/Controllers/FlagController.php  -  students report a note, thread, answer or comment */
class FlagController extends Controller
{
    public function store(): void
    {
        $data = $this->validate(["reason" => "required|min:5|max:255"], ["reason" => "Reason"]);
        $type = (string) Request::input("target_type");
        $targetId = (int) Request::input("target_id");
        $target = in_array($type, Report::TYPES, true) ? Report::target($type, $targetId) : null;
        // Find Members requests have no faculty, and are open to everyone.
        if ($target === null || ($target['faculty'] !== null && !Auth::canSeeFaculty($target['faculty']))) {
            Response::error(404);
        }
        Report::create(Auth::id(), $type, $targetId, $data["reason"]);
        Flash::add("success", "Thanks. A moderator will take a look.");
        Response::back();
    }
}
