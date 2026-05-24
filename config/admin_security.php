<?php

return [
    'allowed_emails' => array_values(array_filter(array_map(
        static fn (string $email): string => strtolower(trim($email)),
        explode(',', (string) env('ADMIN_EMAILS', env('ADMIN_EMAIL', 'admin@personal-website.local')))
    ))),

    'idle_timeout_minutes' => (int) env('ADMIN_IDLE_TIMEOUT_MINUTES', 45),
];
