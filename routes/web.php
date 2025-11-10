<?php

use Illuminate\Support\Facades\Route;

Route::macro("domain", function(array $domains, \Closure $definition) {
    foreach ($domains as $domain) {
        Route::group(['domain' => $domain], $definition);
    }
});

Route::domain([
    env("GUEST_URL"), 
    env("DB1_GUEST_URL"), 
    env("DB2_GUEST_URL"), 
    env("DB3_GUEST_URL")], 
function () {
    require __DIR__ . "/guest.php";
});

Route::domain([
    env("APP_URL"), 
    env("TAGGER_URL"), 
    env("PAYMASTER_URL"), 
    env("DB1_APP_URL"), 
    env("DB2_APP_URL"), 
    env("DB3_APP_URL"), 
    env("DB1_TAGGER_URL"), 
    env("DB2_TAGGER_URL"), 
    env("DB3_TAGGER_URL"), 
    env("DB1_PAYMASTER_URL"), 
    env("DB2_PAYMASTER_URL"), 
    env("DB3_PAYMASTER_URL")
], function () {
    require __DIR__ . "/admin.php";
});

Route::get('/robots.txt', function () {
    return response()->file(public_path('robots.txt'));
});
