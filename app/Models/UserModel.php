<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'tb_user';
    protected $primaryKey       = 'id_user';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uuid_user',
        'firstname_user',
        'lastname_user',
        'email_user',
        'password_user'
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
        'firstname_user' => 'required|alpha_space|min_length[4]|max_length[100]',
        'lastname_user' => 'required|alpha_space|min_length[4]|max_length[100]',
        'email_user' => 'required|valid_email',
        'password_user' => 'required|min_length[8]|max_length[100]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
}