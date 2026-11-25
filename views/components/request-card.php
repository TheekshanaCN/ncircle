<?php
/**
 * components/request-card.php  -  one Find Members request
 *   <?= component('request-card', ['request' => $row]) ?>
 */
$request = $request ?? [];
$types = config("app.help_types");
$count = (int) $request["responses_count"];
$faculties = $request["faculties"] ?: [];
?>
<article class="sc-request">
    <div class="sc-thread__top">
        <span class="sc-state sc-state--dark"><?= e(strtoupper($types[$request["type"]] ?? $request["type"])) ?></span>
        <?php if ($request["status"] === "closed"): ?><span class="sc-state sc-state--muted">CLOSED</span><?php endif; ?>
        <span class="sc-thread__time"><?= e(time_ago($request["created_at"])) ?></span>
    </div>
    <h3 class="sc-request__title"><a class="stretched-link" href="<?= e(url("/members/requests/" . (int) $request["id"])) ?>"><?= e($request["title"]) ?></a></h3>
    <p class="sc-request__text"><?= e($request["description"]) ?></p>
    <div class="sc-request__needs">
        <span class="sc-request__label">Looking in:</span>
        <?php if ($faculties): ?>
            <?php foreach ($faculties as $faculty): ?><span class="sc-chip"><?= e($faculty) ?></span><?php endforeach; ?>
        <?php else: ?>
            <span class="sc-chip">All faculties</span>
        <?php endif; ?>
    </div>
    <?php if (!empty($request["skills"])): ?><?= component("skill-list", ["skills" => $request["skills"]]) ?><?php endif; ?>
    <div class="sc-thread__foot sc-request__foot">
        <?= component("avatar", ["name" => $request["author"], "photo" => avatar_url($request["user_id"] ?? 0, $request["author_avatar"] ?? null), "size" => "sm"]) ?>
        <span class="sc-thread__meta"><?= e($request["author"]) ?> &middot; <?= e($request["author_faculty"]) ?></span>
        <strong class="sc-thread__count"><?= $count ?> <?= $count === 1 ? "response" : "responses" ?></strong>
    </div>
</article>
