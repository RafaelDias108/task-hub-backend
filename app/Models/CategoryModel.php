<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table            = 'tb_category';
    protected $primaryKey       = 'id_category';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uuid_category',
        'name_category',
        'id_user'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'name_category' => 'required|alpha_numeric_punct|min_length[5]|max_length[50]',
        'id_user' => 'required|integer'
    ];

    protected $validationMessages   = [
        'name_category' => [
            'required' => "O campo name_category é requerido",
            'alpha_numeric_punct' => "O campo name_category exige valores alfanuméricos",
            'min_length[5]' => "O campo name_category é permitido no mínimo 5 caracteres",
            'max_length[50]' => "O campo name_category é permitido no máximo 50 caracteres"
        ]
    ];
    protected $skipValidation       = false;
}