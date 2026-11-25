<?php
/** app/Controllers/RatingController.php  -  AJAX star rating */
class RatingController extends Controller
{
    public function rate(int $id): void
    {
        $resource = Resource::find($id);
        if ($resource === null || $resource["status"] !== "approved" || !Resource::canView($resource, Auth::user())) {
            Response::json(["success" => false, "message" => "That note is not available.", "data" => (object) []], 404);
        }
        if ((int) $resource["user_id"] === Auth::id()) {
            Response::json(["success" => false, "message" => "You cannot rate your own note.", "data" => (object) []], 403);
        }
        $value = filter_var(Request::input("value"), FILTER_VALIDATE_INT, ["options" => ["min_range" => 1, "max_range" => 5]]);
        if ($value === false) {
            Response::json(["success" => false, "message" => "Choose between 1 and 5 stars.", "data" => (object) []], 422);
        }
        Rating::save($id, Auth::id(), $value);
        Response::json(["success" => true, "message" => "Thanks for rating this note.", "data" => Rating::summary($id)]);
    }
}
