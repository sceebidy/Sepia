<?php

// config/services.php
// Tambahkan bagian 'n8n' ke dalam array yang sudah ada

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // ── N8n Integration ───────────────────────────────────────
    'n8n' => [
        // URL Webhook n8n — diisi setelah n8n jalan
        // Contoh lokal  : http://localhost:5678/webhook/sepia-process
        // Contoh VPS    : https://n8n.yourdomain.com/webhook/sepia-process
        // Contoh n8n Cloud: https://app.n8n.cloud/webhook/xxx
        'webhook_url' => env('N8N_WEBHOOK_URL', 'http://localhost:5678/webhook/sepia-process'),

        // Secret key untuk validasi callback dari n8n
        // Isi juga di n8n HTTP Request node sebagai body field "secret"
        'callback_secret' => env('N8N_CALLBACK_SECRET', null),
    ],

];