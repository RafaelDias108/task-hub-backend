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
        'time_task',
        'is_completed'
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
        'title_task' => 'required|alpha_numeric_punct|min_length[5]|max_length[50]'
    ];
    protected $validationMessages   = [
        'fk_id_project' => [
            'required' => "O campo fk_id_project é obrigatório",
            'integer' => "O campo fk_id_project tem que ser valor inteiro"
        ],
        'title_task' => [
            'required' => "O campo title_task é obrigatório",
            'alpha_numeric_punct' => "O campo title_task exige valores alfanuméricos",
            'min_length[5]' => "O campo title_task é permitido no mínimo 5 caracteres",
            'max_length[50]' => "O campo title_task é permitido no máximo 50 caracteres"
        ]
    ];
    protected $skipValidation       = false;
}