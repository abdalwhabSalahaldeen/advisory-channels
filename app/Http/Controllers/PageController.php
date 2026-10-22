<?php

namespace App\Http\Controllers;

use App\Models\GatePackage;
use App\Models\JobOpening;
use App\Models\Service;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home(): View
    {
        return view('pages.home', [
            'services' => Service::query()->published()->get(),
            'packages' => GatePackage::query()->published()->get(),
            'featuredOpening' => JobOpening::query()->open()->first(),
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
        ]);
    }

    public function requestService(Request $request): View
    {
        return view('pages.request-service', [
            'service' => $request->string('service')->toString() ?: null,
        ]);
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function privacy(): View
    {
        return view('pages.privacy');
    }
}
