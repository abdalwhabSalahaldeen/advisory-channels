<?php

namespace App\Livewire;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Livewire\Concerns\GuardsSubmissions;
use App\Livewire\Concerns\RemembersLocale;
use App\Models\Lead;
use App\Notifications\NewLeadReceived;
use App\Support\LeadInbox;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ContactForm extends Component
{
    use GuardsSubmissions, RemembersLocale;

    public string $full_name = '';

    public string $email = '';

    public string $phone = '';

    public string $company = '';

    public string $message = '';

    public bool $consent = false;

    #[Locked]
    public string $sourceUrl = '';

    public bool $submitted = false;

    public function mount(): void
    {
        $this->sourceUrl = url()->current();
    }

    public function submit(LeadInbox $inbox): void
    {
        $validated = $this->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'regex:/^[0-9+()\-\s]{7,20}$/'],
            'company' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:3000'],
            'consent' => ['accepted'],
        ]);

        if ($this->passesGuards()) {
            $lead = Lead::query()->create([
                'type' => LeadType::Contact,
                'status' => LeadStatus::New,
                ...collect($validated)->except('consent')->map(fn ($value) => $value ?: null),
                'locale' => $this->locale,
                'source_url' => $this->sourceUrl,
                'consented_at' => now(),
            ]);

            $inbox->notify(new NewLeadReceived($lead));
        }

        $this->submitted = true;
    }

    public function render(): View
    {
        return view('livewire.contact-form');
    }
}
