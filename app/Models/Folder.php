<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Folder extends Model
{
    
    use HasFactory;

    protected $fillable = ['name', 'icon', 'parent_id', 'user_id'];

    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
