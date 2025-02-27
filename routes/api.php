<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UrlShortenerController;

/**
 * URL Shortener API Routes
 */

 // Endpoint to encode a URL into a short URL
Route::post('/encode', [UrlShortenerController::class, 'encode']);

// Endpoint to decode a short URL back to the original URL
Route::post('/decode', [UrlShortenerController::class, 'decode']);
