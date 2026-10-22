<?php

namespace App\Http\Controllers;

use App\Models\CaseStudy;
use App\Models\GatePackage;
use App\Models\Insight;
use App\Models\Service;
use Illuminate\Contracts\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        return view('services.index', [
            'services' => Service::query()->published()->get(),
            'packages' => GatePackage::query()->published()->get(),
        ]);
    }

    public function show(Service $service): View
    {
        abort_unless($service->is_active, 404);

        return view('services.show', [
            'service' => $service,
            'otherServices' => Service::query()->published()->whereKeyNot($service->getKey())->get(),
            'insights' => Insight::query()->published()->whereBelongsTo($service)->limit(3)->get(),
            'caseStudies' => CaseStudy::query()->published()->whereBelongsTo($service)->limit(2)->get(),
        ]);
    }
}
