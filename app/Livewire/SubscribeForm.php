<?php

namespace App\Livewire;

use App\Livewire\Concerns\GuardsSubmissions;
use App\Livewire\Concerns\RemembersLocale;
use App\Models\Subscriber;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * Newsletter sign-up in the footer.
 */
class SubscribeForm extends Component
{
    use GuardsSubmissions, RemembersLocale;

    public string $email = '';

    public bool $submitted = false;

    public function submit(): void
    {
        $this->validate(['email' => ['required', 'email', 'max:150']]);

        if ($this->passesGuards()) {
            // Re-subscribing is harmless, so an existing address is simply kept.
            Subscriber::query()->firstOrCreate(
                ['email' => mb_strtolower($this->email)],
                ['locale' => $this->locale, 'consented_at' => now()],
            );
        }

        $this->submitted = true;

        $this->dispatch('track', name: 'sign_up', params: ['method' => 'newsletter']);
    }

    public function render(): View
    {
        return view('livewire.subscribe-form');
    }
}
