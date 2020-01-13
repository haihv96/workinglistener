<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectGit extends Model
{
    protected $table = 'project_gits';
    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
}
