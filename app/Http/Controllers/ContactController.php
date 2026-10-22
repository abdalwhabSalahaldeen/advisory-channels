<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Pages that host the enquiry forms. Submissions are handled by the Livewire components.
 */
class ContactController extends Controller
{
    public function index(): View
    {
        return view('pages.contact');
    }

    public function requestService(Request $request): View
    {
        return view('pages.request-service', [
            'service' => $request->string('service')->toString() ?: null,
        ]);
    }
}
