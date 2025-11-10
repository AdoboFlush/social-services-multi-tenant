<?php

$tenant_1 = [
    "tenant_id" => 1,
    "database" => env("DB1_DATABASE", "social_services_default"),
    "username" => env("DB1_USERNAME", "root"),
    "password" => env("DB1_PASSWORD", ""),
];

$tenant_2 = [
    "tenant_id" => 2,
    "database" => env("DB2_DATABASE", "social_services_default"),
    "username" => env("DB2_USERNAME", "root"),
    "password" => env("DB2_PASSWORD", ""),
];

$tenant_3 = [
    "tenant_id" => 3,
    "database" => env("DB3_DATABASE", "social_services_default"),
    "username" => env("DB3_USERNAME", "root"),
    "password" => env("DB3_PASSWORD", ""),
];

// set database credentials for each tenant by domain
return [
    env("DB1_APP_URL", "ss-tenant-1.com") => $tenant_1,
    env("DB1_TAGGER_URL",  "ss-tenant-1.com") => $tenant_1,
    env("DB1_PAYMASTER_URL",  "ss-tenant-1.com") => $tenant_1,
    env("DB1_GUEST_URL", "ss-tenant-1.com") => $tenant_1,

    env("DB2_APP_URL", "ss-tenant-2.com") => $tenant_2,
    env("DB2_TAGGER_URL",  "ss-tenant-2.com") => $tenant_2,
    env("DB2_PAYMASTER_URL",  "ss-tenant-2.com") => $tenant_2,
    env("DB2_GUEST_URL", "ss-tenant-2.com") => $tenant_2,

    env("DB3_APP_URL", "ss-tenant-3.com") => $tenant_3,
    env("DB3_TAGGER_URL",  "ss-tenant-3.com") => $tenant_3,
    env("DB3_PAYMASTER_URL",  "ss-tenant-3.com") => $tenant_3,
    env("DB3_GUEST_URL", "ss-tenant-3.com") => $tenant_3,
];