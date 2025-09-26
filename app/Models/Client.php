<?php
// app/Models/Client.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    protected $fillable = ['company'];

    public function departments()
    {
        return $this->hasMany(ClientDepartment::class);
    }
}
