<?php

namespace App\Filament\Resources\BoardingHouses\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Str;

class BoardingHouseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make("Tabs")
                ->tabs([
                    Tab::make("Informasi Umum")->schema([
                        FileUpload::make("thumbnail")
                            ->image()
                            ->directory("boarding_house")
                            ->required(),
                        TextInput::make("name")
                            ->required()
                            ->debounce(200)
                            ->reactive()
                            ->afterStateUpdated(function (
                                $state,
                                callable $set,
                            ) {
                                $set("slug", Str::slug($state));
                            }),
                        TextInput::make("slug")->required(),
                        Select::make("city_id")
                            ->relationship("city", "name")
                            ->required(),
                        Select::make("category_id")
                            ->relationship("category", "name")
                            ->required(),
                        RichEditor::make("description")->required(),
                        TextInput::make("price")
                            ->prefix("IDR")
                            ->numeric()
                            ->required(),
                        Textarea::make("address")->required(),
                    ]),
                    Tab::make("Bonus Ngekos")->schema([
                        Repeater::make("bonuses")
                            ->relationship("bonuses")
                            ->schema([
                                FileUpload::make("image")
                                    ->image()
                                    ->directory("bonuses")
                                    ->required(),
                                TextInput::make("name")->required(),
                                TextInput::make("description")->required(),
                            ]),
                    ]),
                    Tab::make("Kamar")->schema([
                        Repeater::make("rooms")
                            ->relationship("rooms")
                            ->schema([
                                TextInput::make("name")->required(),
                                TextInput::make("room_type")->required(),
                                TextInput::make("square_feet")
                                    ->numeric()
                                    ->required(),
                                TextInput::make("capacity")
                                    ->numeric()
                                    ->required(),
                                TextInput::make("price_per_month")
                                    ->numeric()
                                    ->prefix("IDR")
                                    ->required(),
                                Toggle::make("is_avaible")->required(),
                                Repeater::make("images")
                                    ->relationship("images")
                                    ->schema([
                                        FileUpload::make("image")
                                            ->image()
                                            ->directory("images")
                                            ->required(),
                                    ]),
                            ]),
                    ]),
                ])
                ->columnSpan(2),
        ]);
    }
}
