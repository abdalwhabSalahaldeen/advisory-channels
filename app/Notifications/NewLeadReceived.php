<?php

namespace App\Notifications;

use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use App\Support\Localization;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewLeadReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Lead $lead)
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
        $lead = $this->lead;

        $message = (new MailMessage)
            ->subject(__('notifications.lead.subject', ['type' => $lead->type->getLabel(), 'name' => $lead->full_name]))
            ->greeting(__('notifications.lead.greeting', ['type' => $lead->type->getLabel()]))
            ->line(__('notifications.fields.name').': '.$lead->full_name)
            ->line(__('notifications.fields.email').': '.$lead->email);

        foreach ([
            'phone' => $lead->phone,
            'company' => $lead->company,
            'job_title' => $lead->job_title,
            'country' => $lead->country,
            'service' => $lead->service?->tr('title'),
            'packages' => $lead->selectedPackages()->map->tr('title')->implode('، '),
            'timeline' => $lead->timeline ? __("forms.timelines.{$lead->timeline}") : null,
            'message' => $lead->message,
        ] as $field => $value) {
            if (filled($value)) {
                $message->line(__("notifications.fields.{$field}").': '.$value);
            }
        }

        return $message
            ->replyTo($lead->email, $lead->full_name)
            ->action(__('notifications.lead.action'), LeadResource::getUrl('view', ['record' => $lead], panel: 'admin'));
    }
}
