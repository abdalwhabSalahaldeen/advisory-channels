<?php

namespace App\Filament\Resources\FaqCategories;

use App\Filament\NavigationGroup;
use App\Filament\Resources\FaqCategories\Pages\ManageFaqCategories;
use App\Filament\Support\Translations;
use App\Models\FaqCategory;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class FaqCategoryResource extends Resource
{
    protected static ?string $model = FaqCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolder;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Content;

    protected static ?int $navigationSort = 8;

    public static function getModelLabel(): string
    {
        return __('admin.resources.faq_category.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.faq_category.plural');
    }

    public static function getRecordTitle(?Model $record): ?string
    {
        return $record?->tr('name');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Translations::tabs(fn (string $locale) => [
                    Translations::sluggedTitle($locale, 'name'),
                ]),
                TextInput::make('slug')
                    ->label(__('admin.fields.slug'))
                    ->required()
                    ->alphaDash()
                    ->maxLength(255)
                    ->unique()
                    ->extraInputAttributes(['dir' => 'ltr']),
                TextInput::make('sort_order')
                    ->label(__('admin.fields.sort_order'))
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount('faqs'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('admin.fields.name'))
                    ->state(fn (FaqCategory $record): ?string => $record->tr('name'))
                    ->searchable(query: Translations::search('name')),
                TextColumn::make('slug')
                    ->label(__('admin.fields.slug'))
                    ->color('gray'),
                TextColumn::make('faqs_count')
                    ->label(__('admin.fields.faqs_count'))
                    ->badge()
                    ->color('primary'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageFaqCategories::route('/'),
        ];
    }
}
