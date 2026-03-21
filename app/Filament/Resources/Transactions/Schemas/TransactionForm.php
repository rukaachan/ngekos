<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make("code")->required(),
            Select::make("boarding_house_id")
                ->relationship("boardingHouse", "name")
                ->required(),
            Select::make("room_id")->relationship("room", "name")->required(),
            TextInput::make("name")->required(),
            TextInput::make("email")->required(),
            TextInput::make("phone")->numeric()->required(),
            Select::make("payment_method")
                ->options([
                    "down_payment" => "Down Payment",
                    "full_payment" => "Full Payment",
                ])
                ->required(),
            Select::make("payment_status")
                ->options([
                    "pending" => "Pending",
                    "paid" => "Paid",
                ])
                ->required(),
            DatePicker::make("start_date")->required(),
            TextInput::make("duration")->numeric()->required(),
            TextInput::make("total_amount")
                ->numeric()
                ->prefix("IDR")
                ->required(),
            DatePicker::make("transaction_date")->required(),
        ]);
    }
}
