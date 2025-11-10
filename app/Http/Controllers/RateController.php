<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ExchangeRate;

class RateController extends Controller
{
    public function index()
    {
        $exchangeRates = ExchangeRate::where('active', 1)->get();
        return view('backend.rates.list',compact('exchangeRates'));
    }   
}
