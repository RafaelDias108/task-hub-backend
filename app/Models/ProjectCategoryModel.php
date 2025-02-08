<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectCategoryModel extends Model
{
    protected $table            = 'tb_project_category';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_project',
        'id_category'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';

    // Validation
    protected $validationRules      = [
        'id_project' => 'required|integer',
        'id_category' => 'required|integer'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
}