<?php

namespace App\Support;

use App\Models\Company;
use App\Models\User;

final class CurrentCompany
{
    public static function id(): ?int
    {
        $user = auth()->user();
        if (! $user instanceof User) {
            return null;
        }

        if ($user->isSuperAdmin()) {
            $sid = session('current_company_id');
            if ($sid !== null) {
                return (int) $sid;
            }

            return Company::query()->orderBy('id')->value('id');
        }

        return $user->company_id;
    }

    public static function model(): ?Company
    {
        $id = self::id();

        return $id ? Company::query()->find($id) : null;
    }
}
