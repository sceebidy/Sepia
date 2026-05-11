<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * URI yang dikecualikan dari validasi CSRF.
     * Route callback n8n dikecualikan karena dipanggil
     * dari server eksternal (n8n) bukan dari browser.
     * Keamanan dijaga via N8N_CALLBACK_SECRET di .env
     */
    protected $except = [
        '/n8n/callback',
        '/n8n/test-callback',
        '/analisis/callback',
        '/distribusi/callback',
    ];
}