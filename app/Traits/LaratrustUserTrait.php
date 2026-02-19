<?php

namespace App\Traits;

use Illuminate\Support\Facades\Config;

trait LaratrustUserTrait
{
    /**
     * Check if the user has a role.
     *
     * @param string|array $role
     * @return bool
     */
    public function hasRole($role)
    {
        if (is_array($role)) {
            foreach ($role as $r) {
                if ($this->roles->contains('name', $r)) {
                    return true;
                }
            }
        } else {
            return $this->roles->contains('name', $role);
        }

        return false;
    }

    /**
     * Check if the user has a permission.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('name', $permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Assign a role to the user.
     *
     * @param string $role
     * @return void
     */
    public function attachRole($role)
    {
        $roleModel = Config::get('laratrust.models.role');
        $roleInstance = $roleModel::where('name', $role)->first();

        if ($roleInstance) {
            $this->roles()->attach($roleInstance->id, ['user_type' => get_class($this)]);
        }
    }

    /**
     * Remove a role from the user.
     *
     * @param string $role
     * @return void
     */
    public function detachRole($role)
    {
        $roleModel = Config::get('laratrust.models.role');
        $roleInstance = $roleModel::where('name', $role)->first();

        if ($roleInstance) {
            $this->roles()->detach($roleInstance);
        }
    }

    /**
     * Sync the user's roles.
     *
     * @param array $roles
     * @return void
     */
    public function syncRoles($roles)
    {
        $roleModel = Config::get('laratrust.models.role');
        $roleInstances = $roleModel::whereIn('name', $roles)->get();

        $this->roles()->sync($roleInstances->pluck('id')->mapWithKeys(function ($id) {
            return [$id => ['user_type' => get_class($this)]];
        }));
    }

    /**
     * Define the relationship between the user and roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Config::get('laratrust.models.role'))
            ->withPivot('user_type')
            ->wherePivot('user_type', get_class($this));
    }
}
