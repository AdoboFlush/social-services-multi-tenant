<?php

use Illuminate\Support\Facades\Route;

// Include guest routes - they will work on all guest domains
// The TenantResolver middleware handles routing to the correct database
require __DIR__ . "/guest.php";

// Include admin routes - they will work on all admin domains
require __DIR__ . "/admin.php";

Route::get('/robots.txt', function () {
    return response()->file(public_path('robots.txt'));
});
