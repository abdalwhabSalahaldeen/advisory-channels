<?php

namespace App\Support;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as Notifier;

/**
 * The mailbox that is alerted about new leads and job applications.
 */
class LeadInbox
{
    public function __construct(private SiteSettings $settings) {}

    public function address(): string
    {
        return $this->settings->get('notification_email') ?: config('site.notify_email');
    }

    public function notify(Notification $notification): void
    {
        Notifier::route('mail', $this->address())->notify($notification);
    }
}
