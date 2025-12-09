<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
<<<<<<< HEAD
        'login',
    'register',
    'cart/*',
    'checkout',
    'place-order',
    'cart/add',
    'cart/update',
    'cart/remove',
=======
        'admin/*',
>>>>>>> 1e8a500c1c4cfc926ff7ea9d7a119c317d93851f
    ];
}
