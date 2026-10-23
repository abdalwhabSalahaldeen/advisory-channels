<?php

namespace App\Livewire;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Livewire\Concerns\GuardsSubmissions;
use App\Livewire\Concerns\RemembersLocale;
use App\Models\GatePackage;
use App\Models\Lead;
use App\Notifications\NewLeadReceived;
use App\Support\LeadInbox;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class SaudiGateForm extends Component
{
    use GuardsSubmissions, RemembersLocale;

    /**
     * @var list<string>
     */
    public array $packages = [];

    public string $full_name = '';

    public string $company = '';

    public string $email = '';

    public string $phone = '';

    public string $country = '';

    public string $message = '';

    public bool $consent = false;

    #[Locked]
    public string $sourceUrl = '';

    public bool $submitted = false;

    public function mount(): void
    {
        $this->sourceUrl = url()->current();
    }

    /**
     * @return Collection<int, GatePackage>
     */
    #[Computed]
    public function gatePackages(): Collection
    {
        return GatePackage::query()->published()->get();
    }

    public function submit(LeadInbox $inbox): void
    {
        $validated = $this->validate([
            'packages' => ['required', 'array', 'min:1'],
            'packages.*' => [Rule::in($this->gatePackages->pluck('slug'))],
            'full_name' => ['required', 'string', 'max:120'],
            'company' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'regex:/^[0-9+()\-\s]{7,20}$/'],
            'country' => ['nullable', 'string', 'max:80'],
            'message' => ['nullable', 'string', 'max:3000'],
            'consent' => ['accepted'],
        ]);

        if ($this->passesGuards()) {
            $lead = Lead::query()->create([
                'type' => LeadType::SaudiGate,
                'status' => LeadStatus::New,
                ...collect($validated)->except('consent')->map(fn ($value) => $value ?: null),
                'locale' => $this->locale,
                'source_url' => $this->sourceUrl,
                'consented_at' => now(),
            ]);

            $inbox->notify(new NewLeadReceived($lead));
        }

        $this->submitted = true;

        $this->dispatch('track', name: 'generate_lead', params: ['form' => 'saudi_gate']);
    }

    public function render(): View
    {
        return view('livewire.saudi-gate-form');
    }
}
