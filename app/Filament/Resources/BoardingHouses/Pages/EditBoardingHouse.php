<?php

namespace App\Filament\Resources\BoardingHouses\Pages;

use App\Filament\Resources\BoardingHouses\BoardingHouseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBoardingHouse extends EditRecord
{
    protected static string $resource = BoardingHouseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
