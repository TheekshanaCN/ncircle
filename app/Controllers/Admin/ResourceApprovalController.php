<?php
/** app/Controllers/Admin/ResourceApprovalController.php  -  review queue for uploaded notes */
namespace Admin;

use Auth, Controller, Flash, NotificationService, Request, Resource, Response;

class ResourceApprovalController extends Controller
{
    public function index(): void
    {
        $this->view("admin/approvals", [
            "title" => "Note approvals | Ncircle",
            "pageCss" => "admin",
            "pending" => Resource::pending(),
        ], "admin");
    }

    public function approve(int $id): void
    {
        $resource = $this->orNotFound(Resource::find($id));
        Resource::setStatus($id, "approved", Auth::id());
        NotificationService::noteApproved($resource);
        Flash::add("success", "Approved: " . $resource["title"]);
        Response::redirect("/admin/approvals");
    }

    public function reject(int $id): void
    {
        $resource = $this->orNotFound(Resource::find($id));
        $reason = trim((string) Request::input("reason"));
        if (mb_strlen($reason) < 5 || mb_strlen($reason) > 255) {
            Flash::add("danger", "Give the uploader a reason (5 to 255 characters).");
            Response::redirect("/admin/approvals");
        }
        Resource::setStatus($id, "rejected", Auth::id(), $reason);
        NotificationService::noteRejected($resource, $reason);
        Flash::add("success", "Rejected: " . $resource["title"]);
        Response::redirect("/admin/approvals");
    }
}
