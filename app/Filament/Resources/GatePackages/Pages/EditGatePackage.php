<?php

namespace App\Filament\Resources\GatePackages\Pages;

use App\Filament\Resources\GatePackages\GatePackageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGatePackage extends EditRecord
{
    protected static string $resource = GatePackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
