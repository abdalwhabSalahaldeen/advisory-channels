<?php

namespace App\Filament\Support;

use Closure;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Form building blocks for the {"ar": "...", "en": "..."} JSON columns.
 */
class Translations
{
    /**
     * One tab per site locale. $fields receives the locale key and returns the fields for it,
     * bound with dot notation to the JSON column, e.g. TextInput::make("title.{$locale}").
     *
     * @param  Closure(string $locale): array<Component>  $fields
     */
    public static function tabs(Closure $fields): Tabs
    {
        return Tabs::make()
            ->tabs(collect(config('site.locales'))
                ->map(fn (array $locale, string $key) => Tab::make($locale['name'])
                    ->extraAttributes(['dir' => $locale['dir']])
                    ->schema($fields($key)))
                ->values()
                ->all())
            ->columnSpanFull();
    }

    /**
     * A required "{attribute}.{locale}" input. The English one fills the slug while the record is being created.
     */
    public static function sluggedTitle(string $locale, string $attribute = 'title'): TextInput
    {
        $input = TextInput::make("{$attribute}.{$locale}")
            ->label(__("admin.fields.{$attribute}"))
            ->required()
            ->maxLength(255);

        if ($locale !== 'en') {
            return $input;
        }

        return $input
            ->live(onBlur: true)
            ->afterStateUpdated(function (Set $set, ?string $state, string $operation): void {
                if ($operation === 'create') {
                    $set('slug', Str::slug((string) $state));
                }
            });
    }

    /**
     * A repeater of short bilingual items stored as [{"ar": "...", "en": "..."}, ...].
     */
    public static function list(string $name): Repeater
    {
        return Repeater::make($name)
            ->schema(collect(config('site.locales'))
                ->map(fn (array $locale, string $key) => TextInput::make($key)
                    ->label($locale['name'])
                    ->extraInputAttributes(['dir' => $locale['dir']])
                    ->required())
                ->values()
                ->all())
            ->columns(2)
            ->defaultItems(0)
            ->addActionLabel(__('admin.add_item'))
            ->itemLabel(fn (array $state): ?string => $state[app()->getLocale()] ?? null)
            ->collapsible()
            ->columnSpanFull();
    }

    /**
     * Table search callback that matches a JSON column in any locale.
     *
     * @return Closure(Builder $query, string $search): Builder
     */
    public static function search(string $column): Closure
    {
        return fn (Builder $query, string $search): Builder => $query->where(function (Builder $query) use ($column, $search) {
            foreach (array_keys(config('site.locales')) as $locale) {
                $query->orWhereLike("{$column}->{$locale}", "%{$search}%");
            }
        });
    }
}
