<?php

namespace App\Filament\Resources\Docs;

use App\Filament\NavigationGroup;
use App\Filament\Resources\Docs\Pages\EditDoc;
use App\Filament\Resources\Docs\Pages\ListDocs;
use App\Filament\Resources\Docs\Schemas\DocForm;
use App\Filament\Resources\Docs\Tables\DocsTable;
use App\Models\Doc;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/**
 * The legal pages are fixed (the site links to them by slug), so they can only be edited.
 */
class DocResource extends Resource
{
    protected static ?string $model = Doc::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Content;

    protected static ?int $navigationSort = 8;

    public static function getModelLabel(): string
    {
        return __('admin.resources.doc.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.doc.plural');
    }

    public static function getRecordTitle(?Model $record): ?string
    {
        return $record?->tr('title');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return DocForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DocsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDocs::route('/'),
            'edit' => EditDoc::route('/{record}/edit'),
        ];
    }
}
