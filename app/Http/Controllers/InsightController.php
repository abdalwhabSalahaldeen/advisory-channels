<?php

namespace App\Http\Controllers;

use App\Enums\InsightType;
use App\Models\Insight;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class InsightController extends Controller
{
    public function index(Request $request): View
    {
        $type = InsightType::tryFrom($request->string('type')->toString());

        $featured = $type ? null : Insight::query()->published()->where('is_featured', true)->first();

        return view('insights.index', [
            'type' => $type,
            'featured' => $featured,
            'insights' => Insight::query()
                ->published()
                ->with('author')
                ->when($type, fn ($query) => $query->where('type', $type))
                ->when($featured, fn ($query) => $query->whereKeyNot($featured->getKey()))
                ->paginate(9)
                ->withQueryString(),
            'types' => Insight::query()->published()->distinct()->pluck('type'),
        ]);
    }

    public function show(Insight $insight): View
    {
        abort_unless($insight->isPublished(), 404);

        return view('insights.show', [
            'insight' => $insight->load('author', 'service'),
            'related' => Insight::query()
                ->published()
                ->whereKeyNot($insight->getKey())
                ->reorder()
                ->orderByRaw('type = ? desc', [$insight->type->value])
                ->latest('published_at')
                ->limit(3)
                ->get(),
        ]);
    }
}
