<?php

namespace App\Filament\Resources\GatePackages\Pages;

use App\Filament\Resources\GatePackages\GatePackageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGatePackages extends ListRecords
{
    protected static string $resource = GatePackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
