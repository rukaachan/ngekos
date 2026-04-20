<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function check(Request $request)
    {
        return view("pages.booking");
    }
}
