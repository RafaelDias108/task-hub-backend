<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectModel extends Model
{
    protected $table            = 'tb_project';
    protected $primaryKey       = 'id_project';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uuid_project',
        'fk_id_login',
        'name_project',
        'date_project',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // protected array $casts = [];
    // protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'fk_id_login' => 'required|integer',
        'name_project' => 'required|alpha_numeric_punct|min_length[5]|max_length[255]',
        'date_project' => 'permit_empty'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
}