<?php /** views/admin/users.php  -  accounts */ ?>
<?= component("page-header", ["title" => "Users", "subtitle" => number_format($result["total"]) . " accounts"]) ?>

<form class="browse-bar" method="get" action="<?= e(url("/admin/users")) ?>">
    <div class="browse-bar__search">
        <i class="bi bi-search" aria-hidden="true"></i>
        <label class="visually-hidden" for="userQ">Search users</label>
        <input id="userQ" type="search" name="q" value="<?= e($q) ?>" placeholder="Search by name, email or student ID">
    </div>
</form>

<div class="sc-table-wrap">
    <table class="table sc-table align-middle">
        <thead><tr><th scope="col">Name</th><th scope="col">Student ID</th><th scope="col">Faculty</th><th scope="col">Role</th><th scope="col">Status</th><th scope="col" class="text-end">Actions</th></tr></thead>
        <tbody>
        <?php foreach ($result["rows"] as $row): $isSelf = (int) $row["id"] === Auth::id(); ?>
            <tr>
                <td><a href="<?= e(url("/profile/" . (int) $row["id"])) ?>"><?= e($row["name"]) ?></a><small class="d-block sc-table__sub"><?= e($row["email"]) ?></small></td>
                <td><?= e($row["student_id"]) ?></td>
                <td>
                    <form method="post" action="<?= e(url("/admin/users/" . (int) $row["id"] . "/faculty")) ?>" data-confirm="Move this person to another faculty? They will lose access to their current faculty's content.">
                        <?= csrf_field() ?>
                        <label class="visually-hidden" for="faculty-<?= (int) $row["id"] ?>">Faculty of <?= e($row["name"]) ?></label>
                        <select class="form-select form-select-sm sc-table__select" id="faculty-<?= (int) $row["id"] ?>" name="faculty" data-autosubmit>
                            <?php foreach (array_keys(config("app.faculties")) as $faculty): ?>
                                <option value="<?= e($faculty) ?>"<?= $row["faculty"] === $faculty ? " selected" : "" ?>><?= e($faculty) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </td>
                <td><?= e(ucfirst($row["role"])) ?></td>
                <td><?= component("status-badge", ["status" => $row["status"]]) ?></td>
                <td class="text-end">
                    <?php if ($isSelf): ?>
                        <span class="sc-table__sub">You</span>
                    <?php else: ?>
                        <div class="d-inline-flex gap-2">
                            <form method="post" action="<?= e(url("/admin/users/" . (int) $row["id"] . "/role")) ?>" data-confirm="Change this person's role?">
                                <?= csrf_field() ?>
                                <button class="sc-text-btn" type="submit"><?= $row["role"] === "admin" ? "Make student" : "Make admin" ?></button>
                            </form>
                            <form method="post" action="<?= e(url("/admin/users/" . (int) $row["id"] . "/status")) ?>" data-confirm="<?= $row["status"] === "active" ? "Block this account?" : "Unblock this account?" ?>">
                                <?= csrf_field() ?>
                                <button class="sc-text-btn<?= $row["status"] === "active" ? " sc-text-btn--danger" : "" ?>" type="submit"><?= $row["status"] === "active" ? "Block" : "Unblock" ?></button>
                            </form>
                        </div>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="mt-4"><?= component("pagination", ["page" => $page, "pages" => $result["pages"], "baseUrl" => url("/admin/users"), "query" => array_filter(["q" => $q])]) ?></div>
