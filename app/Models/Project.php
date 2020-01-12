<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'projects';
    protected $guarded = [];

    public function gits()
    {
        return $this->hasMany(ProjectGit::class, 'project_id', 'id');
    }

    public function getByGitRepo($repoName)
    {

    }
}
