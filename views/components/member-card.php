<?php
/**
 * components/member-card.php  -  one student in the Find Members directory. Never shows an email.
 *   <?= component('member-card', ['member' => $row]) ?>
 */
$member = $member ?? [];
?>
<article class="sc-member">
    <div class="sc-member__top">
        <?= component("avatar", ["name" => $member["name"], "size" => "lg"]) ?>
        <div class="sc-member__who">
            <h3><a class="stretched-link" href="<?= e(url("/profile/" . (int) $member["id"])) ?>"><?= e($member["name"]) ?></a></h3>
            <p><?= e(config("app.faculties")[$member["faculty"]] ?? $member["faculty"]) ?> &middot; Year <?= (int) $member["academic_year"] ?></p>
        </div>
    </div>
    <?php if (!empty($member["bio"])): ?><p class="sc-member__bio"><?= e($member["bio"]) ?></p><?php endif; ?>
    <?= component("skill-list", ["skills" => $member["skills"], "open" => (bool) $member["open_to_help"]]) ?>
</article>
