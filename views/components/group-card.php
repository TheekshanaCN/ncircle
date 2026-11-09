<?php
/**
 * components/group-card.php  -  one study group
 *   <?= component('group-card', ['group' => $g]) ?>
 * Needs the columns Group::search() returns (members_count, preview, is_member).
 */
$group = $group ?? [];
$members = (int) $group["members_count"];
$extra = $members - count($group["preview"]);
?>
<article class="sc-group">
    <div class="sc-group__top">
        <span class="sc-group__icon"><i class="bi bi-people-fill" aria-hidden="true"></i></span>
        <?php if (!empty($group["is_member"])): ?><span class="sc-state sc-state--lime">JOINED</span><?php endif; ?>
    </div>
    <h3 class="sc-group__title"><a class="stretched-link" href="<?= e(url("/groups/" . (int) $group["id"])) ?>"><?= e($group["name"]) ?></a></h3>
    <p class="sc-group__subject">#<?= e($group["subject"]) ?> <?= e($group["subject_name"]) ?></p>
    <p class="sc-group__text"><?= e($group["description"]) ?></p>
    <div class="sc-group__foot">
        <div class="sc-person-stack" aria-hidden="true">
            <?php foreach ($group["preview"] as $name): ?><?= component("avatar", ["name" => $name, "size" => "sm"]) ?><?php endforeach; ?>
            <?php if ($extra > 0): ?><span class="sc-person sc-person--grey sc-person--sm">+<?= $extra ?></span><?php endif; ?>
        </div>
        <span><?= $members ?>/<?= (int) $group["max_members"] ?> members</span>
    </div>
</article>
