<?php

namespace App\Filament\Resources\BoardingHouses;

use App\Filament\Resources\BoardingHouses\Pages\CreateBoardingHouse;
use App\Filament\Resources\BoardingHouses\Pages\EditBoardingHouse;
use App\Filament\Resources\BoardingHouses\Pages\ListBoardingHouses;
use App\Filament\Resources\BoardingHouses\Schemas\BoardingHouseForm;
use App\Filament\Resources\BoardingHouses\Tables\BoardingHousesTable;
use App\Models\BoardingHouse;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BoardingHouseResource extends Resource
{
    protected static ?string $model = BoardingHouse::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return BoardingHouseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BoardingHousesTable::configure($table);
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
            'index' => ListBoardingHouses::route('/'),
            'create' => CreateBoardingHouse::route('/create'),
            'edit' => EditBoardingHouse::route('/{record}/edit'),
        ];
    }
}
