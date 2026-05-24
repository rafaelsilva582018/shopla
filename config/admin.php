<?php

$emails = array_filter(array_map(
    fn (string $email): string => strtolower(trim($email)),
    explode(',', env('ADMIN_EMAILS', ''))
));

return [
    'emails' => array_values($emails),
];
