<?php

namespace App\Livewire;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Livewire\Concerns\GuardsSubmissions;
use App\Livewire\Concerns\RemembersLocale;
use App\Models\Lead;
use App\Models\Service;
use App\Notifications\NewLeadReceived;
use App\Support\LeadInbox;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Three-step "Request a Service" wizard: service → details → contact.
 */
class RequestServiceForm extends Component
{
    use GuardsSubmissions, RemembersLocale;

    public const TOTAL_STEPS = 3;

    public const NOT_SURE = 'not-sure';

    public const TIMELINES = ['asap', '1-3_months', '3-6_months', 'exploring'];

    public int $step = 1;

    public string $service = '';

    public string $company = '';

    public string $job_title = '';

    public string $timeline = '';

    public string $message = '';

    public string $full_name = '';

    public string $email = '';

    public string $phone = '';

    public bool $consent = false;

    #[Locked]
    public string $sourceUrl = '';

    public bool $submitted = false;

    public function mount(?string $service = null): void
    {
        $this->sourceUrl = url()->current();

        if ($service && $this->services->contains('slug', $service)) {
            $this->service = $service;
        }
    }

    /**
     * @return Collection<int, Service>
     */
    #[Computed]
    public function services(): Collection
    {
        return Service::query()->published()->get();
    }

    public function next(): void
    {
        $this->validate($this->rulesForStep($this->step));

        $this->step = min($this->step + 1, self::TOTAL_STEPS);
    }

    public function back(): void
    {
        $this->step = max($this->step - 1, 1);
    }

    public function submit(LeadInbox $inbox): void
    {
        $validated = $this->validate([...$this->rulesForStep(1), ...$this->rulesForStep(2), ...$this->rulesForStep(3)]);

        if ($this->passesGuards()) {
            $lead = Lead::query()->create([
                'type' => LeadType::ServiceRequest,
                'status' => LeadStatus::New,
                'service_id' => $this->services->firstWhere('slug', $validated['service'])?->getKey(),
                ...collect($validated)->only(['company', 'job_title', 'timeline', 'message', 'full_name', 'email', 'phone'])->map(fn ($value) => $value ?: null),
                'locale' => $this->locale,
                'source_url' => $this->sourceUrl,
                'consented_at' => now(),
            ]);

            $inbox->notify(new NewLeadReceived($lead));
        }

        $this->submitted = true;
    }

    public function startOver(): void
    {
        $this->reset(['step', 'service', 'company', 'job_title', 'timeline', 'message', 'full_name', 'email', 'phone', 'consent', 'submitted']);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rulesForStep(int $step): array
    {
        return match ($step) {
            1 => [
                'service' => ['required', Rule::in([...$this->services->pluck('slug'), self::NOT_SURE])],
            ],
            2 => [
                'company' => ['required', 'string', 'max:150'],
                'job_title' => ['nullable', 'string', 'max:150'],
                'timeline' => ['nullable', Rule::in(self::TIMELINES)],
                'message' => ['required', 'string', 'max:3000'],
            ],
            3 => [
                'full_name' => ['required', 'string', 'max:120'],
                'email' => ['required', 'email', 'max:150'],
                'phone' => ['required', 'string', 'regex:/^[0-9+()\-\s]{7,20}$/'],
                'consent' => ['accepted'],
            ],
        };
    }

    public function render(): View
    {
        return view('livewire.request-service-form');
    }
}
