<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory, SoftDeletes; // Enables model factories and soft delete functionality

    // Mass assignable attributes
    protected $fillable = [
        'user_id',
        'name',
        'description',
    ];

    // Booted method to handle model events like deleting and restoring
    protected static function booted()
    {
        // Event triggered when a project is being deleted
        static::deleting(function ($project) {
            if ($project->isForceDeleting()) {
                // If project is permanently deleted, also force delete all related building parts
                $project->buildingParts()->forceDelete();
            } else {
                // If project is soft-deleted, soft delete all related building parts
                $project->buildingParts()->delete();
            }
        });

        // Event triggered when a soft-deleted project is being restored
        static::restoring(function ($project) {
            // Restore all related soft-deleted building parts
            $project->buildingParts()->withTrashed()->get()->each->restore();
        });
    }

    /**
     * Define the relationship: A project belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define the relationship: A project has many building parts
     */
    public function buildingParts()
    {
        return $this->hasMany(BuildingPart::class);
    }
}
