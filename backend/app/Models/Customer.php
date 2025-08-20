<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    // Define the fillable properties to allow mass assignment
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'contact_number',
    ];
}
