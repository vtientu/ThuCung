<?php
$config = [
    'enabled' => filter_var(getenv('MOCHI_MAIL_ENABLED') ?: 'false', FILTER_VALIDATE_BOOLEAN),
    'host' => getenv('MOCHI_SMTP_HOST') ?: 'smtp.gmail.com',
    'port' => (int) (getenv('MOCHI_SMTP_PORT') ?: 587),
    'encryption' => getenv('MOCHI_SMTP_ENCRYPTION') ?: 'tls',
    'username' => getenv('MOCHI_SMTP_USERNAME') ?: '',
    'password' => getenv('MOCHI_SMTP_PASSWORD') ?: '',
    'from_address' => getenv('MOCHI_MAIL_FROM') ?: '',
    'from_name' => 'Mochi Pet',
];
if (is_file(__DIR__ . '/mail.local.php')) {
    $config = array_replace($config, require __DIR__ . '/mail.local.php');
}
return $config;
