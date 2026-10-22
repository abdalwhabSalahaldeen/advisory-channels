<?php

namespace App\Http\Controllers;

use App\Models\CaseStudy;
use App\Models\Insight;
use App\Models\JobOpening;
use App\Models\Service;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $pages = collect(['home', 'about', 'services.index', 'saudi-gate', 'careers.index', 'insights.index', 'faqs', 'request-service', 'contact', 'privacy-policy', 'terms-and-conditions'])
            ->map(fn (string $route) => ['route' => $route, 'parameters' => []]);

        $services = Service::query()->published()->get()
            ->map(fn (Service $service) => ['route' => 'services.show', 'parameters' => ['service' => $service], 'updated' => $service->updated_at]);

        $openings = JobOpening::query()->open()->get()
            ->map(fn (JobOpening $opening) => ['route' => 'careers.show', 'parameters' => ['jobOpening' => $opening], 'updated' => $opening->updated_at]);

        $insights = Insight::query()->published()->get()
            ->map(fn (Insight $insight) => ['route' => 'insights.show', 'parameters' => ['insight' => $insight], 'updated' => $insight->updated_at]);

        $caseStudies = CaseStudy::query()->published()->get()
            ->map(fn (CaseStudy $caseStudy) => ['route' => 'impact.show', 'parameters' => ['caseStudy' => $caseStudy], 'updated' => $caseStudy->updated_at]);

        if ($caseStudies->isNotEmpty()) {
            $pages->push(['route' => 'impact.index', 'parameters' => []]);
        }

        return response()
            ->view('sitemap', [
                'entries' => $pages->concat($services)->concat($openings)->concat($insights)->concat($caseStudies),
                'locales' => array_keys(config('site.locales')),
            ])
            ->header('Content-Type', 'application/xml');
    }
}
