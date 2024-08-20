<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'tb_user';
    protected $primaryKey       = 'id_user';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
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
        'email_user' => 'required|valid_email|is_unique[tb_user.email_user]',
        'password_user' => 'required|min_length[8]|max_length[100]'
    ];
    protected $validationMessages   = [
        'firstname_user' => [
            'required' => "O campo firstname_user é obrigatório",
            'alpha_space' => "O campo firstname_user exige valores alfabéticos",
            'min_length' => "O campo firstname_user é permitido no mínimo 4 caracteres",
            'max_length' => "O campo firstname_user é permitido no máximo 100 caracteres"
        ],
        'lastname_user' => [
            'required' => "O campo lastname_user é obrigatório",
            'alpha_space' => "O campo lastname_user exige valores alfabéticos",
            'min_length' => "O campo lastname_user é permitido no mínimo 4 caracteres",
            'max_length' => "O campo lastname_user é permitido no máximo 100 caracteres"
        ],
        'email_user' => [
            'required' => "O campo email_user é obrigatório",
            'valid_email' => "E-mail inválido",
            'is_unique' => "E-mail inválido ou já cadastrado. Verifique se digitou o e-mail corretamente ou use outro e-mail."
        ],
        'password_user' => [
            'required' => "O campo password_user é obrigatório",
            'min_length' => "O campo password_user é permitido no mínimo 8 caracteres",
            'max_length' => "O campo password_user é permitido no máximo 100 caracteres",
        ]
    ];
    protected $skipValidation       = false;
}