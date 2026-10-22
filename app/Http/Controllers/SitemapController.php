<?php

namespace App\Http\Controllers;

use App\Models\JobOpening;
use App\Models\Service;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $pages = collect(['home', 'about', 'services.index', 'saudi-gate', 'careers.index', 'faqs', 'request-service', 'contact', 'privacy-policy', 'terms-and-conditions'])
            ->map(fn (string $route) => ['route' => $route, 'parameters' => []]);

        $services = Service::query()->published()->get()
            ->map(fn (Service $service) => ['route' => 'services.show', 'parameters' => ['service' => $service], 'updated' => $service->updated_at]);

        $openings = JobOpening::query()->open()->get()
            ->map(fn (JobOpening $opening) => ['route' => 'careers.show', 'parameters' => ['jobOpening' => $opening], 'updated' => $opening->updated_at]);

        return response()
            ->view('sitemap', [
                'entries' => $pages->concat($services)->concat($openings),
                'locales' => array_keys(config('site.locales')),
            ])
            ->header('Content-Type', 'application/xml');
    }
}
