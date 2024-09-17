<?php

namespace App\Database\Seeds;

use App\Models\UserModel;
use CodeIgniter\Database\Seeder;
use Faker\Factory;

class UserTest extends Seeder
{
    private $userModel;

    public function __construct()
    {
        helper('uuid');
        $this->userModel = new UserModel();
    }

    public function run()
    {
        for ($i = 0; $i < 3; $i++) {
            $fake = Factory::create('pt_BR');

            $data = [
                'uuid_user' => GenerateUUID(),
                'firstname_user' => 'Admin',
                'lastname_user' => 'Master',
                'email_user' => 'admin@admin.com',
                'password_user' => password_hash('12345678', PASSWORD_BCRYPT)
            ];

            $this->userModel->insert($data);
        }
    }
}