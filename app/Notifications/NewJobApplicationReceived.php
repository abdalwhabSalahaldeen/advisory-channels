<?php

namespace App\Notifications;

use App\Filament\Resources\JobApplications\JobApplicationResource;
use App\Models\JobApplication;
use App\Support\Localization;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewJobApplicationReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public JobApplication $application)
    {
        // The team reads alerts in the site's default language, whatever language the visitor used.
        $this->locale(Localization::defaultLocale());
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $application = $this->application;
        $opening = $application->jobOpening?->tr('title') ?? __('careers.general_application');

        return (new MailMessage)
            ->subject(__('notifications.application.subject', ['opening' => $opening, 'name' => $application->full_name]))
            ->greeting(__('notifications.application.greeting', ['opening' => $opening]))
            ->line(__('notifications.fields.name').': '.$application->full_name)
            ->line(__('notifications.fields.email').': '.$application->email)
            ->line(__('notifications.fields.phone').': '.$application->phone)
            ->lineIf(filled($application->linkedin_url), 'LinkedIn: '.$application->linkedin_url)
            ->replyTo($application->email, $application->full_name)
            ->action(__('notifications.application.action'), JobApplicationResource::getUrl('view', ['record' => $application], panel: 'admin'));
    }
}
