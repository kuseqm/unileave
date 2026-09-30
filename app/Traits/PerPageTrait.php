<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait PerPageTrait
{
    protected function getPerPage(Request $request, $default = 10)
    {
        $perPage = $request->get('per_page', $default);

        // Validate per_page is within allowed limits
        $allowed = [10, 25, 50, 100];
        if (! in_array($perPage, $allowed)) {
            $perPage = $default;
        }

        return $perPage;
    }
}
