<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class VendorScope implements Scope
{
    /**
     * Prevent infinite recursion when the scope itself needs to fetch the user.
     */
    protected static $applying = false;

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        // 1. Prevent recursion
        if (static::$applying) {
            return;
        }

        static::$applying = true;

        try {
            // 2. If user is not logged in, do nothing
            if (!Auth::check()) {
                return;
            }

            $user = Auth::user();

            // 3. If user is Super Admin, they can see everything
            // Laratrust hasRole can trigger queries, recursion guard covers this.
            if ($user->hasRole('super_admin')) {
                return;
            }

            // 4. Apply vendor filter
            if ($user->vendor_id) {
                $builder->where($model->getTable() . '.vendor_id', $user->vendor_id);
            } else {
                // For users without vendor_id (Super Admin or legacy data), 
                // show records where vendor_id is NULL.
                $builder->whereNull($model->getTable() . '.vendor_id');
            }
        } finally {
            static::$applying = false;
        }
    }
}
