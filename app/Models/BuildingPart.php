<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BuildingPart extends Model
{
    use HasFactory;
 use SoftDeletes;

    protected $fillable = [
        'project_id',
        'name',
        'building_part_type',
        'material_type',
        'supplier_name',
    ];

    /* Relasi ke Project */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
