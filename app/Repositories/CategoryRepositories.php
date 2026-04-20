<?php

namespace App\Repositories;

use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;

class CategoryRepositories implements CategoryRepositoryInterface
{
    public function getAllCategories()
    {
        return Category::all();
    }
}
