<?php

$default_db = env("DB_DATABASE", "social_services_default");

// Camsur District Scope

$tenant_1 = [
    "tenant_id" => 1,
    "database" => env("DB1_DATABASE", $default_db),
    "username" => env("DB1_USERNAME", "root"),
    "password" => env("DB1_PASSWORD", ""),
    "name" => env("DB1_NAME", ""),
    "role" => env("DB1_ROLE", ""),
    "parent_id" => env("DB1_PARENT_ID", 0),
    "domains" => [
        env("DB1_APP_URL", "/"),
        env("DB1_TAGGER_URL",  "/"),
        env("DB1_PAYMASTER_URL",  "/"),
        env("DB1_GUEST_URL", "/"),
    ],
];

$tenant_2 = [
    "tenant_id" => 2,
    "database" => env("DB2_DATABASE", $default_db),
    "username" => env("DB2_USERNAME", "root"),
    "password" => env("DB2_PASSWORD", ""),
    "name" => env("DB2_NAME", ""),
    "role" => env("DB2_ROLE", ""),
    "parent_id" => env("DB2_PARENT_ID", 0),
    "domains" => [
        env("DB2_APP_URL", "/"),
        env("DB2_TAGGER_URL",  "/"),
        env("DB2_PAYMASTER_URL",  "/"),
        env("DB2_GUEST_URL", "/"),
    ],
];

$tenant_3 = [
    "tenant_id" => 3,
    "database" => env("DB3_DATABASE", $default_db),
    "username" => env("DB3_USERNAME", "root"),
    "password" => env("DB3_PASSWORD", ""),
    "name" => env("DB3_NAME", ""),
    "role" => env("DB3_ROLE", ""),
    "parent_id" => env("DB3_PARENT_ID", 0),
    "domains" => [
        env("DB3_APP_URL", "/"),
        env("DB3_TAGGER_URL",  "/"),
        env("DB3_PAYMASTER_URL",  "/"),
        env("DB3_GUEST_URL", "/"),
    ],
];


// QC Mayoral Scope

// main tenant account
$tenant_4 = [
    "tenant_id" => 4,
    "database" => env("DB4_DATABASE", $default_db),
    "username" => env("DB4_USERNAME", "root"),
    "password" => env("DB4_PASSWORD", ""),
    "name" => env("DB4_NAME", ""),
    "role" => env("DB4_ROLE", ""),
    "parent_id" => env("DB4_PARENT_ID", 0),
    "domains" => [
        env("DB4_APP_URL", "/"),
        env("DB4_TAGGER_URL",  "/"),
        env("DB4_PAYMASTER_URL",  "/"),
        env("DB4_GUEST_URL", "/"),
    ],
];

$tenant_5 = [
    "tenant_id" => 5,
    "database" => env("DB5_DATABASE", $default_db),
    "username" => env("DB5_USERNAME", "root"),
    "password" => env("DB5_PASSWORD", ""),
    "name" => env("DB5_NAME", ""),
    "role" => env("DB5_ROLE", ""),
    "parent_id" => env("DB5_PARENT_ID", 4),
    "domains" => [
        env("DB5_APP_URL", "/"),
        env("DB5_TAGGER_URL",  "/"),
        env("DB5_PAYMASTER_URL",  "/"),
        env("DB5_GUEST_URL", "/"),
    ],
];

$tenant_6 = [
    "tenant_id" => 6,
    "database" => env("DB6_DATABASE", $default_db),
    "username" => env("DB6_USERNAME", "root"),
    "password" => env("DB6_PASSWORD", ""),
    "name" => env("DB6_NAME", ""),
    "role" => env("DB6_ROLE", ""),
    "parent_id" => env("DB6_PARENT_ID", 4),
    "domains" => [
        env("DB6_APP_URL", "/"),
        env("DB6_TAGGER_URL",  "/"),
        env("DB6_PAYMASTER_URL",  "/"),
        env("DB6_GUEST_URL", "/"),
    ],
];

$tenant_7 = [
    "tenant_id" => 7,
    "database" => env("DB7_DATABASE", $default_db),
    "username" => env("DB7_USERNAME", "root"),
    "password" => env("DB7_PASSWORD", ""),
    "name" => env("DB7_NAME", ""),
    "role" => env("DB7_ROLE", ""),
    "parent_id" => env("DB7_PARENT_ID", 4),
    "domains" => [
        env("DB7_APP_URL", "/"),
        env("DB7_TAGGER_URL",  "/"),
        env("DB7_PAYMASTER_URL",  "/"),
        env("DB7_GUEST_URL", "/"),
    ],
];

$tenant_8 = [
    "tenant_id" => 8,
    "database" => env("DB8_DATABASE", $default_db),
    "username" => env("DB8_USERNAME", "root"),
    "password" => env("DB8_PASSWORD", ""),
    "name" => env("DB8_NAME", ""),
    "role" => env("DB8_ROLE", ""),
    "parent_id" => env("DB8_PARENT_ID", 4),
    "domains" => [
        env("DB8_APP_URL", "/"),
        env("DB8_TAGGER_URL",  "/"),
        env("DB8_PAYMASTER_URL",  "/"),
        env("DB8_GUEST_URL", "/"),
    ],
];

$tenant_9 = [
    "tenant_id" => 9,
    "database" => env("DB9_DATABASE", $default_db),
    "username" => env("DB9_USERNAME", "root"),
    "password" => env("DB9_PASSWORD", ""),
    "name" => env("DB9_NAME", ""),
    "role" => env("DB9_ROLE", ""),
    "parent_id" => env("DB9_PARENT_ID", 4),
    "domains" => [
        env("DB9_APP_URL", "/"),
        env("DB9_TAGGER_URL",  "/"),
        env("DB9_PAYMASTER_URL",  "/"),
        env("DB9_GUEST_URL", "/"),
    ],
];

$tenant_10 = [
    "tenant_id" => 10,
    "database" => env("DB10_DATABASE", $default_db),
    "username" => env("DB10_USERNAME", "root"),
    "password" => env("DB10_PASSWORD", ""),
    "name" => env("DB10_NAME", ""),
    "role" => env("DB10_ROLE", ""),
    "parent_id" => env("DB10_PARENT_ID", 4),
    "domains" => [
        env("DB10_APP_URL", "/"),
        env("DB10_TAGGER_URL",  "/"),
        env("DB10_PAYMASTER_URL",  "/"),
        env("DB10_GUEST_URL", "/"),
    ],
];
    
return [
    $tenant_1,
    $tenant_2,
    $tenant_3,
    $tenant_4,
    $tenant_5,
    $tenant_6,
    $tenant_7,
    $tenant_8,
    $tenant_9,
    $tenant_10,
];