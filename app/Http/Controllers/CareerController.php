<?php

namespace App\Http\Controllers;

use App\Models\JobOpening;
use Illuminate\Contracts\View\View;

class CareerController extends Controller
{
    public function index(): View
    {
        return view('careers.index', [
            'openings' => JobOpening::query()->open()->get(),
        ]);
    }

    public function show(JobOpening $jobOpening): View
    {
        abort_unless($jobOpening->is_active, 404);

        return view('careers.show', ['opening' => $jobOpening]);
    }

    public function apply(): View
    {
        return view('careers.apply');
    }
}
