<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskModel extends Model
{
    protected $table            = 'tb_task';
    protected $primaryKey       = 'id_task';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uuid_task',
        'fk_id_project',
        'title_task',
        'description_task',
        'date_task',
        'time_task'
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
        'fk_id_project' => 'required|integer',
        'title_task' => 'required|alpha_numeric_punct|min_length[5]|max_length[50]',
        'description_task' => 'permit_empty|alpha_numeric_punct|min_length[5]|max_length[100]',
        'date_task' => 'permit_empty',
        'time_task' => 'permit_empty'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
}