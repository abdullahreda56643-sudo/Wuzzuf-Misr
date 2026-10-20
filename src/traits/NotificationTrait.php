<?php

trait NotificationTrait
{
    protected function sendNotification(int $userId, string $title, string $message, string $type = 'system', ?string $actionUrl = null, ?string $relatedType = null, ?int $relatedId = null): bool
    {
        if ($userId <= 0) {
            return false;
        }

        $notification = new Notification();

        $data = [
            'user_id' => $userId,
            'title' => trim($title),
            'message' => trim($message),
            'type' => $type,
            'related_type' => $relatedType,
            'related_id' => $relatedId,
            'is_read' => 0,
            'read_at' => null,
            'action_url' => $actionUrl,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        try {
            $notification->create($data);
            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    protected function sendBulkNotifications(array $userIds, string $title, string $message, string $type = 'system', ?string $actionUrl = null): int
    {
        $count = 0;

        foreach ($userIds as $userId) {
            if ($this->sendNotification((int) $userId, $title, $message, $type, $actionUrl)) {
                $count++;
            }
        }

        return $count;
    }

    protected function markNotificationsReadForUser(int $userId): bool
    {
        $notification = new Notification();

        try {
            return $notification->markAllRead($userId);
        } catch (Throwable $e) {
            return false;
        }
    }
}
