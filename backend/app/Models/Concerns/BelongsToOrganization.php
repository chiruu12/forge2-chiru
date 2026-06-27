<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait BelongsToOrganization
{
    protected static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope('organization', function (Builder $builder) {
            $user = Auth::user();

            if ($user !== null && $user->organization_id !== null) {
                $builder->where($builder->getModel()->getTable() . '.organization_id', $user->organization_id);
            }
        });

        static::creating(function (Model $model): void {
            $user = Auth::user();

            if ($user !== null && $model->getAttribute('organization_id') === null) {
                $model->setAttribute('organization_id', $user->organization_id);
            }
        });
    }
}
