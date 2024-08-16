<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateInitialTableStructure extends Migration
{
    public function up()
    {
        $this->CreateTableUser();
        $this->CreateTableProject();
        $this->CreateTableTask();
        $this->CreateTableCategory();
    }

    public function down()
    {
        $this->forge->dropTable('tb_user', true, true);
        $this->forge->dropTable('tb_project', true, true);
        $this->forge->dropTable('tb_task', true, true);
        $this->forge->dropTable('tb_category', true, true);
    }

    private function CreateTableUser()
    {

        $this->forge->addField([
            'id_user' => [
                'type'           => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
                'null'           => false
            ],
            'uuid_user' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'  => false
            ],
            'firstname_user' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
            ],
            'lastname_user' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
            ],
            'email_user' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
            ],
            'password_user' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'default'   => new RawSql('CURRENT_TIMESTAMP')
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true
            ],
            'deleted_at' => [
                'type' => 'TIMESTAMP',
                'null' => true
            ],
        ]);

        $this->forge->addPrimaryKey('id_user');
        $this->forge->addUniqueKey('uuid_user', 'uuid_user_UNIQUE');
        $this->forge->addUniqueKey('email_user', 'email_user_UNIQUE');

        $this->forge->createTable('tb_user', true, ['ENGINE' => 'InnoDB']);
    }
    private function CreateTableProject()
    {
        $this->forge->addField([
            'id_project' => [
                'type'           => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
                'null'           => false
            ],
            'uuid_project' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'  => false
            ],
            'fk_id_user' => [
                'type' => 'INT',
                'null' => false,
            ],
            'name_project' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
            ],
            'date_project' => [
                'type' => 'DATE',
                'null' => true,
                'default' => NULL
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'default'   => new RawSql('CURRENT_TIMESTAMP')
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true
            ],
            'deleted_at' => [
                'type' => 'TIMESTAMP',
                'null' => true
            ],
        ]);

        $this->forge->addPrimaryKey('id_project');
        $this->forge->addUniqueKey('uuid_project', 'uuid_project_UNIQUE');
        $this->forge->addForeignKey('fk_id_user', 'tb_user', 'id_user', 'NO ACTION', 'CASCADE', 'fk_user_project');

        $this->forge->createTable('tb_project', true, ['ENGINE' => 'InnoDB']);
    }
    private function CreateTableTask()
    {

        $this->forge->addField([
            'id_task' => [
                'type'           => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
                'null'           => false
            ],
            'uuid_task' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'  => false
            ],
            'fk_id_project' => [
                'type' => 'INT',
                'null' => false,
            ],
            'title_task' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => false,
            ],
            'description_task' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'default' => NULL
            ],
            'date_task' => [
                'type' => 'DATE',
                'null' => true,
                'default' => NULL
            ],
            'time_task' => [
                'type' => 'TIME',
                'null' => true,
                'default' => NULL
            ],
            'is_completed' => [
                'type' => 'TINYINT',
                'null' => false,
                'default' => false
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'default'   => new RawSql('CURRENT_TIMESTAMP')
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true
            ],
            'deleted_at' => [
                'type' => 'TIMESTAMP',
                'null' => true
            ],
        ]);

        $this->forge->addPrimaryKey('id_task');
        $this->forge->addUniqueKey('uuid_task', 'uuid_task_UNIQUE');
        $this->forge->addForeignKey('fk_id_project', 'tb_project', 'id_project', 'NO ACTION', 'CASCADE', 'fk_project_task');

        $this->forge->createTable('tb_task', true, ['ENGINE' => 'InnoDB']);
    }
    private function CreateTableCategory()
    {

        $this->forge->addField([
            'id_category' => [
                'type'           => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
                'null'           => false
            ],
            'uuid_category' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'  => false
            ],
            'fk_id_project' => [
                'type' => 'INT',
                'null' => true,
                'default'   => NULL
            ],
            'name_category' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => false,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'default'   => new RawSql('CURRENT_TIMESTAMP')
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true
            ],
            'deleted_at' => [
                'type' => 'TIMESTAMP',
                'null' => true
            ],
        ]);

        $this->forge->addPrimaryKey('id_category');
        $this->forge->addUniqueKey('uuid_category', 'uuid_category_UNIQUE');
        $this->forge->addForeignKey('fk_id_project', 'tb_project', 'id_project', 'CASCADE', 'SET NULL', 'fk_project_category');

        $this->forge->createTable('tb_category', true, ['ENGINE' => 'InnoDB']);
    }
}