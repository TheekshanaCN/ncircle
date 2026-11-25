<?php
/** views/forum/show.php  -  one thread with its answers */
$badge = Question::statusBadge($question);
$qid = (int) $question["id"];
?>
<nav class="sc-crumb" aria-label="Breadcrumb"><a href="<?= e(url("/forum")) ?>"><i class="bi bi-arrow-left" aria-hidden="true"></i> Discussion forum</a></nav>

<article class="sc-panel">
    <div class="sc-thread__top">
        <span class="sc-state sc-state--<?= e($badge["tone"]) ?>"><?= e($badge["label"]) ?></span>
        <span class="sc-thread__subject">#<?= e($question["subject"]) ?> <?= e($question["subject_name"]) ?></span>
        <span class="sc-thread__time"><?= (int) $question["views"] ?> views &middot; <?= e(time_ago($question["created_at"])) ?></span>
    </div>
    <h1 class="thread-title"><?= e($question["title"]) ?></h1>
    <p class="sc-post__text thread-body"><?= nl2br(e($question["body"])) ?></p>
    <?= component("attachment-list", ["attachments" => $attachments]) ?>
    <div class="thread-foot">
        <?= component("avatar", ["name" => $question["author"], "photo" => avatar_url($question["user_id"], $question["author_avatar"]), "size" => "sm"]) ?>
        <a href="<?= e(url("/profile/" . (int) $question["user_id"])) ?>"><?= e($question["author"]) ?></a>
        <div class="thread-foot__actions">
            <?php if ($isOwner): ?><a class="sc-text-btn" href="<?= e(url("/forum/" . $qid . "/edit")) ?>"><i class="bi bi-pencil" aria-hidden="true"></i> Edit</a><?php endif; ?>
            <?php if ($isOwner || $isAdmin): ?>
                <button class="sc-text-btn sc-text-btn--danger" type="button" data-bs-toggle="modal" data-bs-target="#deleteQuestion"><i class="bi bi-trash" aria-hidden="true"></i> Delete</button>
            <?php else: ?>
                <button class="sc-text-btn" type="button" data-bs-toggle="modal" data-bs-target="#flagModal" data-flag-type="question" data-flag-id="<?= $qid ?>"><i class="bi bi-flag" aria-hidden="true"></i> Report</button>
            <?php endif; ?>
        </div>
    </div>
</article>

<section id="answers" aria-labelledby="answers-title">
    <h2 class="sc-panel__title answers-title" id="answers-title"><?= count($answers) ?> <?= count($answers) === 1 ? "Answer" : "Answers" ?></h2>
    <?php foreach ($answers as $answer): $mine = (int) $answer["user_id"] === Auth::id(); ?>
        <article class="sc-panel sc-panel--tight answer<?= $answer["is_accepted"] ? " answer--accepted" : "" ?>" id="answer-<?= (int) $answer["id"] ?>">
            <form class="answer__vote" method="post" action="<?= e(url("/answers/" . (int) $answer["id"] . "/vote")) ?>">
                <?= csrf_field() ?>
                <button type="submit" class="sc-round-btn<?= $answer["has_voted"] ? " is-on" : "" ?>" aria-pressed="<?= $answer["has_voted"] ? "true" : "false" ?>" aria-label="Upvote this answer"<?= $mine ? " disabled" : "" ?>><i class="bi bi-arrow-up" aria-hidden="true"></i></button>
                <strong><?= (int) $answer["votes"] ?></strong>
            </form>
            <div class="answer__main">
                <p class="sc-post__meta">
                    <a href="<?= e(url("/profile/" . (int) $answer["user_id"])) ?>"><strong><?= e($answer["author"]) ?></strong></a> <span><?= e(time_ago($answer["created_at"])) ?></span>
                    <?php if ($answer["is_accepted"]): ?><span class="sc-state sc-state--lime"><i class="bi bi-check-lg" aria-hidden="true"></i> ACCEPTED</span><?php endif; ?>
                </p>
                <?php if ($answer["body"] !== ""): ?><p class="sc-post__text"><?= nl2br(e($answer["body"])) ?></p><?php endif; ?>
                <?= component("attachment-list", ["attachments" => $answer["attachments"]]) ?>
                <div class="answer__actions">
                    <?php if ($isOwner && !$answer["is_accepted"]): ?>
                        <form method="post" action="<?= e(url("/answers/" . (int) $answer["id"] . "/accept")) ?>">
                            <?= csrf_field() ?>
                            <button class="sc-text-btn" type="submit"><i class="bi bi-check-circle" aria-hidden="true"></i> Accept answer</button>
                        </form>
                    <?php endif; ?>
                    <?php if ($mine): ?><a class="sc-text-btn" href="<?= e(url("/answers/" . (int) $answer["id"] . "/edit")) ?>"><i class="bi bi-pencil" aria-hidden="true"></i> Edit</a><?php endif; ?>
                    <?php if ($mine || $isAdmin): ?>
                        <form method="post" action="<?= e(url("/answers/" . (int) $answer["id"] . "/delete")) ?>" data-confirm="Delete this answer?">
                            <?= csrf_field() ?>
                            <button class="sc-text-btn sc-text-btn--danger" type="submit"><i class="bi bi-trash" aria-hidden="true"></i> Delete</button>
                        </form>
                    <?php endif; ?>
                    <?php if (!$mine): ?>
                        <button class="sc-text-btn" type="button" data-bs-toggle="modal" data-bs-target="#flagModal" data-flag-type="answer" data-flag-id="<?= (int) $answer["id"] ?>"><i class="bi bi-flag" aria-hidden="true"></i> Report</button>
                    <?php endif; ?>
                </div>
            </div>
        </article>
    <?php endforeach; ?>
</section>

<section class="sc-panel" id="reply" aria-labelledby="reply-title">
    <h2 class="sc-panel__title" id="reply-title">Your answer</h2>
    <form method="post" action="<?= e(url("/forum/" . $qid . "/answers")) ?>" class="mt-3" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <?= component("composer", ["name" => "body", "label" => "Answer", "rows" => 5, "maxlength" => 5000]) ?>
        <?= component("button", ["label" => "Post answer", "type" => "submit", "variant" => "dark", "size" => "sm"]) ?>
    </form>
</section>

<?= component("flag-modal") ?>
<?php if ($isOwner || $isAdmin): ?>
    <?= component("confirm-modal", ["id" => "deleteQuestion", "title" => "Delete this question?", "message" => "Its answers are deleted too. This cannot be undone.", "action" => url("/forum/" . $qid . "/delete"), "confirmLabel" => "Delete"]) ?>
<?php endif; ?>
