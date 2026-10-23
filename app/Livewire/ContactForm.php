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
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

/**
 * The contact form asks why the visitor is writing first (as the big firms do)
 * and accepts an RFP document for tenders.
 */
class ContactForm extends Component
{
    use GuardsSubmissions, RemembersLocale, WithFileUploads;

    public const PURPOSES = ['engage', 'rfp', 'saudi_gate', 'partnership', 'other'];

    public string $purpose = '';

    public string $full_name = '';

    public string $email = '';

    public string $phone = '';

    public string $company = '';

    public string $message = '';

    public ?TemporaryUploadedFile $attachment = null;

    public bool $consent = false;

    #[Locked]
    public string $sourceUrl = '';

    public bool $submitted = false;

    public function mount(): void
    {
        $this->sourceUrl = url()->current();
    }

    public function updatedAttachment(): void
    {
        $this->validateOnly('attachment', ['attachment' => $this->attachmentRules()]);
    }

    public function submit(LeadInbox $inbox): void
    {
        $validated = $this->validate([
            'purpose' => ['required', Rule::in(self::PURPOSES)],
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'regex:/^[0-9+()\-\s]{7,20}$/'],
            'company' => [$this->purpose === 'rfp' ? 'required' : 'nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:3000'],
            'attachment' => $this->attachmentRules(),
            'consent' => ['accepted'],
        ]);

        if ($this->passesGuards()) {
            $lead = Lead::query()->create([
                'type' => match ($this->purpose) {
                    'engage', 'rfp' => LeadType::ServiceRequest,
                    'saudi_gate' => LeadType::SaudiGate,
                    default => LeadType::Contact,
                },
                'status' => LeadStatus::New,
                ...collect($validated)->only(['purpose', 'full_name', 'email', 'phone', 'company', 'message'])->map(fn ($value) => $value ?: null),
                'attachment_path' => $this->purpose === 'rfp' ? $this->attachment?->store('rfps', Lead::ATTACHMENT_DISK) : null,
                'locale' => $this->locale,
                'source_url' => $this->sourceUrl,
                'consented_at' => now(),
            ]);

            $inbox->notify(new NewLeadReceived($lead));
        }

        $this->reset('attachment');
        $this->submitted = true;
        $this->dispatch('track', name: 'generate_lead', params: ['form' => 'contact']);
    }

    /**
     * @return list<string>
     */
    protected function attachmentRules(): array
    {
        return ['nullable', 'file', 'mimes:pdf,doc,docx,zip', 'max:10240'];
    }

    public function render(): View
    {
        return view('livewire.contact-form');
    }
}
