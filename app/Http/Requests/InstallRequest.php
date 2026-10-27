<?php

namespace App\Http\Requests;

use App\Support\Installer;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class InstallRequest extends FormRequest
{
    /**
     * Only someone holding INSTALL_TOKEN may run the installer, and only once.
     */
    public function authorize(Installer $installer): bool
    {
        return $installer->acceptsToken($this->input('token'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'password' => ['required', 'confirmed', Password::min(12)->letters()->numbers()],
        ];
    }

    protected function failedAuthorization(): never
    {
        abort(404);
    }
}
