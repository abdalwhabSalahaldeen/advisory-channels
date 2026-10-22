<?php

namespace App\Filament\Support;

use App\Models\Service;
use App\Models\TeamMember;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;

/**
 * Relationship selects whose options are labelled with a translated JSON column.
 */
class RelationSelects
{
    public static function service(): Select
    {
        return Select::make('service_id')
            ->label(__('admin.fields.service'))
            ->relationship('service', 'slug', fn (Builder $query) => $query->orderBy('sort_order'))
            ->getOptionLabelFromRecordUsing(fn (Service $record): ?string => $record->tr('title'))
            ->preload();
    }

    public static function author(): Select
    {
        return Select::make('author_id')
            ->label(__('admin.fields.author'))
            ->relationship('author', 'id', fn (Builder $query) => $query->orderBy('sort_order'))
            ->getOptionLabelFromRecordUsing(fn (TeamMember $record): ?string => $record->tr('name'))
            ->preload();
    }
}
