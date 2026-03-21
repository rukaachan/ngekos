<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make("photo")
                ->image()
                ->directory("testimonials")
                ->required()
                ->columnSpan(2),
            Select::make("boarding_house_id")
                ->relationship("boardingHouse", "name")
                ->columnSpan(2)
                ->required(),
            Textarea::make("content")->required(),
            TextInput::make("name")->required(),
            TextInput::make("rating")
                ->minValue(1)
                ->maxValue(5)
                ->numeric()
                ->required(),
        ]);
    }
}
