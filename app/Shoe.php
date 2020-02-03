<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Shoe extends Authenticatable
{
    use SoftDeletes, HasApiTokens, Notifiable;

    protected $dates = ['deleted_at'];

    public function scopeSearch($query, $target)
    {
        if ($target != '') {
            return $query->where('name', 'like', "%$target%")
                ->orWhere('price', 'like', "%$target%")
                ->orWhere('color', 'like', "%$target%")
                ->orWhere('size', 'like', "%$target%")
                ->orWhere('description', 'like', "%$target%");
        }

    }
}
