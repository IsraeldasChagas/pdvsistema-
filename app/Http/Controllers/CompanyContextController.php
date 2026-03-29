<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompanyContextController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        if (! $request->user()?->isSuperAdmin()) {
            abort(403);
        }

        $request->validate([
            'company_id' => ['required', 'integer', 'exists:companies,id'],
        ]);

        session(['current_company_id' => (int) $request->input('company_id')]);

        return back();
    }
}
