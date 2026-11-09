<?php /** views/members/_tabs.php  -  page header and tabs shared by requests and directory */ ?>
<?= component("page-header", [
    "title" => "Find Members",
    "titleTag" => "All faculties",
    "subtitle" => "Find people from any faculty for research, projects and other help.",
    "actions" => component("button", ["label" => "Post a request", "icon" => "plus-lg", "variant" => "dark", "size" => "sm", "href" => url("/members/requests/create")]),
]) ?>
<nav class="browse-tabs" aria-label="Find Members sections">
    <a class="browse-tabs__tab<?= current_path() === "/members" && !($filters["mine"] ?? false) ? " is-active" : "" ?>" href="<?= e(url("/members")) ?>">Requests</a>
    <a class="browse-tabs__tab<?= is_path("/members/directory") ? " is-active" : "" ?>" href="<?= e(url("/members/directory")) ?>">Member directory</a>
    <a class="browse-tabs__tab<?= !empty($filters["mine"]) ? " is-active" : "" ?>" href="<?= e(url("/members?mine=1")) ?>">My requests</a>
</nav>
