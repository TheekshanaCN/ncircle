<?php
/**
 * components/thread-card.php  -  one forum thread (dashboard and forum list)
 *   <?= component('thread-card', ['question' => $q]) ?>
 */
$question = $question ?? [];
$badge = Question::statusBadge($question);
$replies = (int) $question["replies"];
$lead = $question["lead_answerer"] ?? null;
?>
<article class="sc-thread">
    <div class="sc-thread__top">
        <span class="sc-state sc-state--<?= e($badge["tone"]) ?>"><?= e($badge["label"]) ?></span>
        <span class="sc-thread__subject">#<?= e($question["subject"]) ?> <?= e($question["subject_name"]) ?></span>
        <span class="sc-thread__time"><?= e(time_ago($question["created_at"])) ?></span>
    </div>
    <h3 class="sc-thread__title"><a href="<?= e(url("/forum/" . (int) $question["id"])) ?>"><?= e($question["title"]) ?></a></h3>
    <div class="sc-thread__foot">
        <?= component("avatar", ["name" => $lead ?: $question["author"], "size" => "sm"]) ?>
        <span class="sc-thread__meta">
            <?php if ($lead): ?>
                <?= e(strtok($lead, " ")) ?> answered &bull; <?= (int) $question["upvotes"] ?> peer upvotes
            <?php else: ?>
                <?= e(strtok($question["author"], " ")) ?> asking &bull; <?= (int) $question["views"] ?> views
            <?php endif; ?>
        </span>
        <?php if ($replies > 0): ?>
            <strong class="sc-thread__count"><?= $replies ?> <?= $replies === 1 ? "reply" : "replies" ?></strong>
        <?php else: ?>
            <a class="sc-thread__first" href="<?= e(url("/forum/" . (int) $question["id"] . "#reply")) ?>">Be first</a>
        <?php endif; ?>
    </div>
</article>
