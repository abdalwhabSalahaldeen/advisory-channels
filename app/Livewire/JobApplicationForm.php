<?php

namespace App\Livewire;

use App\Enums\ApplicationStatus;
use App\Livewire\Concerns\GuardsSubmissions;
use App\Livewire\Concerns\RemembersLocale;
use App\Models\JobApplication;
use App\Models\JobOpening;
use App\Notifications\NewJobApplicationReceived;
use App\Support\LeadInbox;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class JobApplicationForm extends Component
{
    use GuardsSubmissions, RemembersLocale, WithFileUploads;

    /**
     * Null for a general (open) application.
     */
    #[Locked]
    public ?JobOpening $jobOpening = null;

    public string $full_name = '';

    public string $email = '';

    public string $phone = '';

    public string $linkedin_url = '';

    public ?TemporaryUploadedFile $cv = null;

    public string $cover_letter = '';

    public bool $consent = false;

    public bool $submitted = false;

    public function updatedCv(): void
    {
        $this->validateOnly('cv', ['cv' => $this->cvRules()]);
    }

    public function submit(LeadInbox $inbox): void
    {
        $validated = $this->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'regex:/^[0-9+()\-\s]{7,20}$/'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'cv' => $this->cvRules(),
            'cover_letter' => ['nullable', 'string', 'max:3000'],
            'consent' => ['accepted'],
        ]);

        abort_if($this->jobOpening && ! $this->jobOpening->isOpen(), 403);

        if ($this->passesGuards()) {
            $application = JobApplication::query()->create([
                'job_opening_id' => $this->jobOpening?->getKey(),
                'status' => ApplicationStatus::New,
                ...collect($validated)->only(['full_name', 'email', 'phone', 'linkedin_url', 'cover_letter'])->map(fn ($value) => $value ?: null),
                'cv_path' => $this->cv->store('cvs', JobApplication::CV_DISK),
                'locale' => $this->locale,
                'consented_at' => now(),
            ]);

            $inbox->notify(new NewJobApplicationReceived($application));
        }

        $this->reset('cv');
        $this->submitted = true;
    }

    /**
     * @return list<string>
     */
    protected function cvRules(): array
    {
        return ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'];
    }

    public function render(): View
    {
        return view('livewire.job-application-form');
    }
}
