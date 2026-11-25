<?php
/**
 * components/pod-card.php  -  one meeting: type, time, place or link, seats and Going / Can't go
 *   <?= component('pod-card', ['session' => $s]) ?>
 * Needs the columns StudySession returns (attendees, preview, my_status).
 * The meeting link only shows to the host and people who said Going.
 */
$session = $session ?? [];
$modes = config("app.session_modes");
$tones = ["online" => "lime", "physical" => "dark", "hybrid" => "dark"];
$start = strtotime($session["starts_at"]);
$end = !empty($session["ends_at"]) ? strtotime($session["ends_at"]) : null;
$when = date("Y-m-d", $start) === date("Y-m-d") ? "Today" : (date("Y-m-d", $start) === date("Y-m-d", strtotime("+1 day")) ? "Tomorrow" : date("M j", $start));
$attendees = (int) $session["attendees"];
$capacity = (int) $session["capacity"];
$extra = $attendees - count($session["preview"]);
$isPast = $start < time();
$user = Auth::user();
$isHost = (int) $session["host_id"] === (int) ($user["id"] ?? 0);
$status = $session["my_status"] ?? null;
$canManage = $user !== null && StudySession::canManage($session, $user);
$showLink = !empty($session["meeting_url"]) && ($isHost || $status === "going") && !$isPast;
$id = (int) $session["id"];
?>
<article class="sc-pod">
    <div class="sc-pod__top">
        <span class="sc-state sc-state--<?= e($tones[$session["mode"]] ?? "dark") ?>"><?= e(strtoupper($modes[$session["mode"]] ?? $session["mode"])) ?></span>
        <time class="sc-pod__when" datetime="<?= e(date("c", $start)) ?>"><?= e($when) ?>, <?= e(date("g:i A", $start)) ?><?= $end ? " – " . e(date("g:i A", $end)) : "" ?></time>
    </div>
    <h3 class="sc-pod__title"><?= e($session["subject"]) ?> <?= e($session["title"]) ?></h3>
    <p class="sc-pod__text"><?= e($session["description"]) ?></p>

    <ul class="sc-pod__facts">
        <?php if (!empty($session["group_name"])): ?><li><i class="bi bi-people" aria-hidden="true"></i> <a href="<?= e(url("/groups/" . (int) $session["group_id"])) ?>"><?= e($session["group_name"]) ?></a></li><?php endif; ?>
        <?php if (!empty($session["location"])): ?><li><i class="bi bi-geo-alt" aria-hidden="true"></i> <?= e($session["location"]) ?></li><?php endif; ?>
        <?php if ($showLink): ?>
            <li><i class="bi bi-camera-video" aria-hidden="true"></i> <a href="<?= e($session["meeting_url"]) ?>" target="_blank" rel="noopener noreferrer">Open meeting link</a></li>
        <?php elseif (!empty($session["meeting_url"]) && !$isPast): ?>
            <li><i class="bi bi-camera-video" aria-hidden="true"></i> Link shows when you're going</li>
        <?php endif; ?>
    </ul>

    <div class="sc-pod__foot">
        <div class="sc-person-stack" aria-hidden="true">
            <?php foreach ($session["preview"] as $person): ?><?= component("avatar", ["name" => $person["name"], "photo" => avatar_url($person["id"], $person["avatar"]), "size" => "sm"]) ?><?php endforeach; ?>
            <?php if ($extra > 0): ?><span class="sc-person sc-person--grey sc-person--sm">+<?= $extra ?></span><?php endif; ?>
        </div>
        <span class="sc-pod__seats"><?= $attendees ?>/<?= $capacity ?> going</span>
        <?php if ($isPast): ?><span class="sc-pod__ended">Ended</span><?php elseif ($isHost): ?><span class="sc-pod__ended">Hosting</span><?php endif; ?>
    </div>

    <?php if (!$isPast && (!$isHost || $canManage)): ?>
        <div class="sc-pod__actions">
            <?php if (!$isHost): ?>
                <?php if ($status === "going"): ?>
                    <span class="sc-pod__answer"><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Going</span>
                <?php elseif ($attendees >= $capacity): ?>
                    <span class="sc-pod__answer sc-pod__answer--muted">Full</span>
                <?php else: ?>
                    <form method="post" action="<?= e(url("/sessions/" . $id . "/join")) ?>"><?= csrf_field() ?><button class="btn btn-dark btn-xs" type="submit">Going</button></form>
                <?php endif; ?>
                <?php if ($status === "cant"): ?>
                    <span class="sc-pod__answer sc-pod__answer--muted">You can't go</span>
                <?php else: ?>
                    <form method="post" action="<?= e(url("/sessions/" . $id . "/leave")) ?>"><?= csrf_field() ?><button class="btn btn-outline-primary btn-xs sc-btn-quiet" type="submit">Can't go</button></form>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($canManage): ?>
                <span class="sc-pod__manage">
                    <a class="sc-text-btn" href="<?= e(url("/sessions/" . $id . "/edit")) ?>">Edit</a>
                    <form method="post" action="<?= e(url("/sessions/" . $id . "/delete")) ?>" data-confirm="Cancel this meeting for everyone?"><?= csrf_field() ?><button class="sc-text-btn sc-text-btn--danger" type="submit">Cancel</button></form>
                </span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</article>
