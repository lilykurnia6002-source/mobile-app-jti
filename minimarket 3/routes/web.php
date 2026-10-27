<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;

Route::get('/', function () {
    $posts = Product::all();
    return view('welcome', compact('posts'));
});