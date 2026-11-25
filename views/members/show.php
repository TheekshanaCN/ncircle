<?php
/** views/members/show.php  -  one request. Emails only appear between the two people on an accepted response. */
$id = (int) $request["id"];
$types = config("app.help_types");
$faculties = $request["faculties"] ?: [];
$isOpen = $request["status"] === "open";
?>
<nav class="sc-crumb" aria-label="Breadcrumb"><a href="<?= e(url("/members")) ?>"><i class="bi bi-arrow-left" aria-hidden="true"></i> Find Members</a></nav>

<?php if ($request["is_hidden"]): ?><?= component("alert", ["type" => "warning", "message" => "A moderator removed this request. Only you and admins can see it."]) ?><?php endif; ?>

<div class="note-detail">
    <div>
        <article class="sc-panel">
            <div class="sc-thread__top">
                <span class="sc-state sc-state--dark"><?= e(strtoupper($types[$request["type"]] ?? $request["type"])) ?></span>
                <?php if (!$isOpen): ?><span class="sc-state sc-state--muted">CLOSED</span><?php endif; ?>
                <span class="sc-thread__time"><?= e(time_ago($request["created_at"])) ?></span>
            </div>
            <h1 class="thread-title"><?= e($request["title"]) ?></h1>
            <p class="sc-post__text thread-body"><?= nl2br(e($request["description"])) ?></p>
            <div class="sc-request__needs">
                <span class="sc-request__label">Looking in:</span>
                <?php if ($faculties): ?>
                    <?php foreach ($faculties as $faculty): ?><span class="sc-chip"><?= e($faculty) ?></span><?php endforeach; ?>
                <?php else: ?>
                    <span class="sc-chip">All faculties</span>
                <?php endif; ?>
            </div>
            <?php if (!empty($request["skills"])): ?><?= component("skill-list", ["skills" => $request["skills"]]) ?><?php endif; ?>
            <div class="thread-foot mt-3">
                <?= component("avatar", ["name" => $request["author"], "photo" => avatar_url($request["user_id"] ?? 0, $request["author_avatar"] ?? null), "size" => "sm"]) ?>
                <a href="<?= e(url("/profile/" . (int) $request["user_id"])) ?>"><?= e($request["author"]) ?></a> <span class="sc-thread__meta">&middot; <?= e($request["author_faculty"]) ?></span>
                <div class="thread-foot__actions">
                    <?php if ($isOwner && $isOpen): ?>
                        <form method="post" action="<?= e(url("/members/requests/" . $id . "/close")) ?>" data-confirm="Close this request? Nobody new can respond after this.">
                            <?= csrf_field() ?>
                            <button class="sc-text-btn" type="submit"><i class="bi bi-check2-circle" aria-hidden="true"></i> Close request</button>
                        </form>
                    <?php elseif (!$isOwner): ?>
                        <button class="sc-text-btn" type="button" data-bs-toggle="modal" data-bs-target="#flagModal" data-flag-type="help_request" data-flag-id="<?= $id ?>"><i class="bi bi-flag" aria-hidden="true"></i> Report</button>
                    <?php endif; ?>
                </div>
            </div>
        </article>

        <?php if ($isOwner || Auth::isAdmin()): ?>
            <section class="sc-panel" aria-labelledby="responses-title">
                <h2 class="sc-panel__title" id="responses-title">Responses (<?= count($responses) ?>)</h2>
                <?php foreach ($responses as $response): ?>
                    <div class="sc-post">
                        <?= component("avatar", ["name" => $response["name"], "photo" => avatar_url($response["user_id"], $response["avatar"]), "size" => "sm"]) ?>
                        <div class="sc-post__body">
                            <p class="sc-post__meta"><a href="<?= e(url("/profile/" . (int) $response["user_id"])) ?>"><strong><?= e($response["name"]) ?></strong></a> <span><?= e($response["faculty"]) ?> &middot; Year <?= (int) $response["academic_year"] ?> &middot; <?= e(time_ago($response["created_at"])) ?></span></p>
                            <p class="sc-post__text"><?= nl2br(e($response["message"])) ?></p>
                            <?php if ($response["status"] === "pending" && $isOwner): ?>
                                <div class="members-decide">
                                    <form method="post" action="<?= e(url("/members/responses/" . (int) $response["id"] . "/accept")) ?>" data-confirm="Accept? You will both see each other's university email."><?= csrf_field() ?><button class="btn btn-dark btn-xs" type="submit">Accept</button></form>
                                    <form method="post" action="<?= e(url("/members/responses/" . (int) $response["id"] . "/decline")) ?>"><?= csrf_field() ?><button class="btn btn-outline-primary btn-xs sc-btn-quiet" type="submit">Decline</button></form>
                                </div>
                            <?php elseif ($response["status"] === "accepted"): ?>
                                <p class="members-result"><span class="sc-state sc-state--lime">ACCEPTED</span><?php if ($response["email"]): ?> <a href="mailto:<?= e($response["email"]) ?>"><i class="bi bi-envelope" aria-hidden="true"></i> <?= e($response["email"]) ?></a><?php endif; ?></p>
                            <?php elseif ($response["status"] === "declined"): ?>
                                <p class="members-result"><span class="sc-state sc-state--muted">DECLINED</span></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (!$responses): ?><p class="text-muted-sc mt-3">No responses yet. You'll get a notification when someone responds.</p><?php endif; ?>
            </section>
        <?php endif; ?>
    </div>

    <aside>
        <?php if (!$isOwner): ?>
            <section class="sc-panel sc-panel--tight" aria-labelledby="respond-title">
                <?php if ($myResponse !== null): ?>
                    <h2 class="sc-panel__title" id="respond-title">Your response</h2>
                    <p class="sc-post__text mt-2"><?= nl2br(e($myResponse["message"])) ?></p>
                    <?php if ($myResponse["status"] === "pending"): ?>
                        <p class="members-result"><span class="sc-state sc-state--muted">WAITING FOR AN ANSWER</span></p>
                    <?php elseif ($myResponse["status"] === "accepted"): ?>
                        <p class="members-result"><span class="sc-state sc-state--lime">ACCEPTED</span></p>
                        <p class="sc-post__text">Contact <?= e($request["author"]) ?> at <a href="mailto:<?= e($ownerEmail) ?>"><?= e($ownerEmail) ?></a></p>
                    <?php else: ?>
                        <p class="members-result"><span class="sc-state sc-state--muted">DECLINED</span></p>
                    <?php endif; ?>
                <?php elseif ($isOpen): ?>
                    <h2 class="sc-panel__title" id="respond-title">I'm interested</h2>
                    <form method="post" action="<?= e(url("/members/requests/" . $id . "/respond")) ?>" class="mt-3">
                        <?= csrf_field() ?>
                        <?= component("form-field", ["name" => "message", "label" => "Why are you a good fit?", "type" => "textarea", "rows" => 4, "required" => true, "help" => "Your email stays private unless they accept.", "attrs" => ["maxlength" => 500]]) ?>
                        <?= component("button", ["label" => "Send", "type" => "submit", "variant" => "dark", "size" => "sm"]) ?>
                    </form>
                <?php else: ?>
                    <h2 class="sc-panel__title" id="respond-title">Request closed</h2>
                    <p class="text-muted-sc mt-2">This request is no longer taking responses.</p>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </aside>
</div>
<?= component("flag-modal") ?>
