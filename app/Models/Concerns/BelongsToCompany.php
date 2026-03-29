<?php

namespace App\Models\Concerns;

use App\Support\CurrentCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait BelongsToCompany
{
    public static function bootBelongsToCompany(): void
    {
        static::addGlobalScope('company', function (Builder $builder): void {
            $id = CurrentCompany::id();
            if ($id !== null) {
                $builder->where($builder->getModel()->getTable().'.company_id', $id);
            }
        });

        static::creating(function (Model $model): void {
            if (! auth()->check()) {
                return;
            }
            $id = CurrentCompany::id();
            if ($id !== null && $model->getAttribute('company_id') === null) {
                $model->setAttribute('company_id', $id);
            }
        });
    }
}
