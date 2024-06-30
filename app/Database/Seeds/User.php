<?php

namespace App\Database\Seeds;

use App\Models\UserModel;
use CodeIgniter\Database\Seeder;
use Faker\Factory;

class User extends Seeder
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function run()
    {
        for ($i = 0; $i <= 2; $i++) {
            $fake = Factory::create('pt_BR');

            $data = [
                'uuid_user' => GenerateUUID(),
                'firstname_user' => $fake->firstName(),
                'lastname_user' => $fake->lastName(),
                'email_user' => $fake->email(),
                'password_user' => password_hash('123', PASSWORD_BCRYPT)
            ];

            $this->userModel->insert($data);
        }
    }
}