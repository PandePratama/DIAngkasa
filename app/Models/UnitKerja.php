<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    use HasFactory;

    protected $table = 'unit_kerja'; // Nama tabel custom
    protected $guarded = ['id'];
    protected $fillable = ['unit_name'];


    public function users()
    {
        return $this->hasMany(User::class, 'id_unit_kerja');
    }
}
