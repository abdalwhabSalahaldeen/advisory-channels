<?php

namespace App\Http\Controllers;

use App\Models\FaqCategory;
use Illuminate\Contracts\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        return view('pages.faqs', [
            'categories' => FaqCategory::query()
                ->with(['faqs' => fn ($query) => $query->published()])
                ->whereHas('faqs', fn ($query) => $query->where('is_active', true))
                ->orderBy('sort_order')
                ->get(),
        ]);
    }
}
