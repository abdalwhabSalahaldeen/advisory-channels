<?php

namespace App\Filament\Resources\GatePackages;

use App\Filament\NavigationGroup;
use App\Filament\Resources\GatePackages\Pages\CreateGatePackage;
use App\Filament\Resources\GatePackages\Pages\EditGatePackage;
use App\Filament\Resources\GatePackages\Pages\ListGatePackages;
use App\Filament\Resources\GatePackages\Schemas\GatePackageForm;
use App\Filament\Resources\GatePackages\Tables\GatePackagesTable;
use App\Models\GatePackage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class GatePackageResource extends Resource
{
    protected static ?string $model = GatePackage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Content;

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('admin.resources.gate_package.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.gate_package.plural');
    }

    public static function getRecordTitle(?Model $record): ?string
    {
        return $record?->tr('title');
    }

    public static function form(Schema $schema): Schema
    {
        return GatePackageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GatePackagesTable::configure($table);
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
            'index' => ListGatePackages::route('/'),
            'create' => CreateGatePackage::route('/create'),
            'edit' => EditGatePackage::route('/{record}/edit'),
        ];
    }
}
