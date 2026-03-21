<?php

namespace App\Filament\Resources\Cities\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;

class CityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make("image")
                ->image()
                ->directory("cities")
                ->required()
                ->columnSpan(2),
            TextInput::make("name")
                ->required()
                ->debounce(500)
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    $set("slug", Str::slug($state));
                }),
            TextInput::make("slug")->required(),
        ]);
    }
}
