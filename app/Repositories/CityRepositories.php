<?php

namespace App\Repositories;

use App\Interfaces\CityRepositoryInterface;
use App\Models\City;

class CityRepositories implements CityRepositoryInterface
{
    public function getAllCities()
    {
        return City::all();
    }
}
