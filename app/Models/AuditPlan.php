<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditPlan extends Model
{
    use HasFactory;

    // Menentukan nama tabel
    protected $table = 'audit_plans';

    // Menentukan kolom-kolom yang dapat diisi
    protected $fillable = [
        'lecture_id',
        'email',
        'no_phone',
        'date_start',
        'date_end',
        'audit_status_id',
        'location_id',
        'department_id',
        'auditor_id',
        'doc_path',
        'link',
        'remark_docs',
        'standard_categories_id',
<<<<<<< HEAD
        'standard_criterias_id',
        'periode',
        'user_standard'
        ];
=======
        'standard_criterias_id'
    ];

    // Casting kolom JSON ke array
    protected $casts = [
        'standard_categories_id' => 'array',
        'standard_criterias_id' => 'array',
    ];
>>>>>>> ec0194cc9eba5468fe597b2d54c8e9bb033e4d1c

    // Relasi ke model lain (opsional, jika diperlukan)
    public function lecture()
    {
        return $this->belongsTo(User::class, 'lecture_id');
    }

    public function auditStatus()
    {
        return $this->belongsTo(AuditStatus::class, 'audit_status_id');
    }

    public function departments()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

<<<<<<< HEAD
=======
    public function category()
    {
        return $this->belongsTo(StandardCategory::class, 'description', 'audit_status_id');
    }

    public function categoryid()
    {
        return $this->belongsTo(StandardCategory::class, 'standard_categories_id');
    }

>>>>>>> ec0194cc9eba5468fe597b2d54c8e9bb033e4d1c
    public function criterias()
    {
        return $this->belongsTo(StandardCriteria::class, 'standard_criterias_id');
    }

<<<<<<< HEAD
    public function auditor(){
        return $this->belongsTo(UserStandard::class, 'auditor_id');
    }

    public function category(){
        return $this->belongsTo(StandardAmi::class, 'standard_categories_id');
=======
    public function indicator()
    {
        return $this->hasMany(Indicator::class, 'indicator_id');
    }

    public function sub_indicator()
    {
        return $this->hasMany(SubIndicator::class, 'sub_indicator_id');
>>>>>>> ec0194cc9eba5468fe597b2d54c8e9bb033e4d1c
    }
}
