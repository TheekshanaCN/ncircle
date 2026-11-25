<?php
/** app/Services/NotificationService.php  -  one place for every notification the site sends */
class NotificationService
{
    public static function noteApproved(array $resource): void
    {
        Notification::create((int) $resource['user_id'], 'patch-check', 'Your note was approved', $resource['title'], '/resources/' . $resource['id']);
    }

    public static function noteRejected(array $resource, string $reason): void
    {
        Notification::create((int) $resource['user_id'], 'x-circle', 'Your note was not approved', $reason, '/resources/' . $resource['id']);
    }

    public static function noteCommented(array $resource, int $actorId, string $actorName): void
    {
        if ((int) $resource['user_id'] !== $actorId) {
            Notification::create((int) $resource['user_id'], 'chat-dots', $actorName . ' commented on your note', $resource['title'], '/resources/' . $resource['id']);
        }
    }

    public static function questionAnswered(array $question, int $actorId, string $actorName): void
    {
        if ((int) $question['user_id'] !== $actorId) {
            Notification::create((int) $question['user_id'], 'chat-left-text', $actorName . ' answered your question', $question['title'], '/forum/' . $question['id']);
        }
    }

    public static function answerAccepted(array $question, array $answer): void
    {
        if ((int) $answer['user_id'] !== (int) $question['user_id']) {
            Notification::create((int) $answer['user_id'], 'check-circle', 'Your answer was accepted', $question['title'], '/forum/' . $question['id']);
        }
    }

    public static function groupJoined(array $group, int $actorId, string $actorName): void
    {
        if ((int) $group['owner_id'] !== $actorId) {
            Notification::create((int) $group['owner_id'], 'people', $actorName . ' joined your group', $group['name'], '/groups/' . $group['id']);
        }
    }

    public static function sessionJoined(array $session, int $actorId, string $actorName): void
    {
        if ((int) $session['host_id'] !== $actorId) {
            Notification::create((int) $session['host_id'], 'calendar-check', $actorName . ' is going to your meeting', $session['title'], self::meetingLink($session));
        }
    }

    public static function meetingScheduled(array $session, array $userIds, int $actorId, string $groupName): void
    {
        self::tellMeetingPeople($userIds, $actorId, 'calendar-plus', 'New meeting in ' . $groupName, $session);
    }

    public static function meetingChanged(array $session, array $userIds, int $actorId): void
    {
        self::tellMeetingPeople($userIds, $actorId, 'calendar-event', 'A meeting was updated', $session);
    }

    public static function sessionCancelled(array $session, array $userIds, int $actorId): void
    {
        self::tellMeetingPeople($userIds, $actorId, 'calendar-x', 'A meeting was cancelled', $session);
    }

    private static function tellMeetingPeople(array $userIds, int $actorId, string $icon, string $title, array $session): void
    {
        foreach ($userIds as $userId) {
            if ($userId !== $actorId) {
                Notification::create($userId, $icon, $title, $session['title'], self::meetingLink($session));
            }
        }
    }

    private static function meetingLink(array $session): string
    {
        return !empty($session['group_id']) ? '/groups/' . (int) $session['group_id'] : '/sessions';
    }

    public static function helpInterest(array $request, string $actorName): void
    {
        Notification::create((int) $request['user_id'], 'person-raised-hand', $actorName . ' is interested in your request', $request['title'], '/members/requests/' . $request['id']);
    }

    public static function helpAccepted(array $request, int $userId): void
    {
        Notification::create($userId, 'person-check', 'Your offer to help was accepted', $request['title'], '/members/requests/' . $request['id']);
    }

    public static function helpDeclined(array $request, int $userId): void
    {
        Notification::create($userId, 'person-x', 'Your offer to help was declined', $request['title'], '/members/requests/' . $request['id']);
    }
}
