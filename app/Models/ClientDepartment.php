<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientDepartment extends Model
{
    use HasFactory;

    protected $fillable = ['department', 'email', 'personnel', 'position'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
