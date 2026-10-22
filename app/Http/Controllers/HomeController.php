<?php

namespace App\Http\Controllers;

use App\Models\Doc;
use App\Models\Faq;
use App\Models\GatePackage;
use App\Models\JobOpening;
use App\Models\Partner;
use App\Models\Service;
use App\Models\Testimonial;
use Illuminate\Contracts\View\View;

/**
 * The public marketing pages (home, about, Saudi Gate) and the legal docs.
 */
class HomeController extends Controller
{
    public function index(): View
    {
        return view('index', [
            'services' => Service::query()->published()->get(),
            'packages' => GatePackage::query()->published()->get(),
            'featuredOpening' => JobOpening::query()->open()->first(),
            'testimonials' => Testimonial::query()->published()->get(),
            'partners' => Partner::query()->published()->get(),
            'faqs' => Faq::query()->published()->limit(4)->get(),
        ]);
    }

    public function about(): View
    {
        return view('pages.about');
    }

    public function saudiGate(): View
    {
        return view('pages.saudi-gate', [
            'packages' => GatePackage::query()->published()->get(),
            'faqs' => Faq::query()->published()
                ->whereHas('category', fn ($query) => $query->where('slug', 'saudi-gate'))
                ->get(),
        ]);
    }

    public function privacyPolicy(): View
    {
        return $this->doc(Doc::PRIVACY_POLICY);
    }

    public function termsAndConditions(): View
    {
        return $this->doc(Doc::TERMS);
    }

    protected function doc(string $slug): View
    {
        return view('pages.doc', [
            'doc' => Doc::query()->where('slug', $slug)->firstOrFail(),
        ]);
    }
}
