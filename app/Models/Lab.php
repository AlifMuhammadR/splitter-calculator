<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lab extends Model
{
    protected $table = 'labs';
    protected $fillable = ['name', 'slug', 'author', 'description', 'lab_group_id'];

    public function group()
    {
        return $this->belongsTo(LabGroup::class, 'lab_group_id');
    }
    public function topologies()
    {
        return $this->hasMany(\App\Models\Topology::class);
    }
}
