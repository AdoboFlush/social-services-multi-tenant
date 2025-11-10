<?php

use Illuminate\Support\Facades\Route;

Route::macro("domain", function(array $domains, \Closure $definition) {
    foreach ($domains as $domain) {
        Route::group(['domain' => $domain], $definition);
    }
});

Route::domain([env("GUEST_URL")], function () {
    require __DIR__ . "/guest.php";
});

Route::domain([env("APP_URL"), env("TAGGER_URL"), env("PAYMASTER_URL")], function () {
    require __DIR__ . "/admin.php";
});

Route::get('/robots.txt', function () {
    return response()->file(public_path('robots.txt'));
});
