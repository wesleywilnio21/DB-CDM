<?php

declare(strict_types=1);

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Pastikan user yang mengakses adalah Super Admin.
     * Digunakan sebagai pengganti duplikasi method di setiap controller.
     */
    protected function authorizeSuperAdmin(): void
    {
        if (! auth()->user()?->isSuperAdmin()) {
            abort(403, 'Unauthorized access.');
        }
    }
}
