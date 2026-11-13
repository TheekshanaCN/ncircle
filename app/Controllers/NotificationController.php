<?php
/** app/Controllers/NotificationController.php */
class NotificationController extends Controller
{
    public function index(): void
    {
        $this->view("notifications/index", [
            "title" => "Notifications | Ncircle",
            "pageCss" => "profile",
            "result" => Notification::forUser(Auth::id(), $this->page()),
            "page" => $this->page(),
        ], "app");
    }

    /** Marks one as read, then goes to the page it points at. */
    public function open(int $id): void
    {
        $notification = $this->orNotFound(Notification::findForUser($id, Auth::id()));
        Notification::markRead($id, Auth::id());
        Response::redirect($notification["link"] ?: "/notifications");
    }

    public function readAll(): void
    {
        Notification::markAllRead(Auth::id());
        Response::redirect("/notifications");
    }
}
