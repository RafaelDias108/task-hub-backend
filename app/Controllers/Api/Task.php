<?php

namespace App\Controllers\Api;

use App\Models\ProjectModel;
use App\Models\TaskModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\RESTful\ResourceController;
use UUID;

class Task extends ResourceController
{
    use ResponseTrait;

    protected $taskModel;

    public function __construct()
    {
        helper('uuid_helper');
        $this->taskModel = new TaskModel();
    }

    public function index($uuid = null)
    {

        try {

            if (!is_null($uuid)) {
                $task = $this->taskModel->where('uuid_task', $uuid)->find();
                if (empty($task)) {
                    return $this->respond([
                        'status'   => 'success',
                        'message' => 'Tarefa não encontrada',
                        'data' => []
                    ], 404);
                }

                return $this->respond([
                    'status'   => 'success',
                    'data' => $task
                ], 200);
            } else {
                $tasks = $this->taskModel->findAll();
                if (empty($tasks)) {
                    return $this->respond([
                        'status'   => 'success',
                        'message' => 'Tarefas não encontradas',
                        'data' => []
                    ], 404);
                }

                return $this->respond([
                    'status'   => 'success',
                    'data' => $tasks
                ], 200);
            }
        } catch (\Exception $exception) {

            return $this->respond([
                'status'   => 'error',
                'message' => "Erro ao buscar as tarefas: " . $exception->getMessage(),
                'data' => []
            ], 400);
        }
    }

    public function Newtask()
    {
        $taskData = $this->request->getJSON();
        if (!isset($taskData->uuid_project) || empty($taskData->uuid_project)) return $this->respond([
            'status' => 'error',
            'message' => "Não foi possível criar a tarefa: uuid do projeto inválido, informe o uuid do projeto que deseja criar a tarefa.",
            'data' => []
        ], 400);

        $projectModel = new ProjectModel();
        $project = $projectModel->where('uuid_project', $taskData->uuid_project)->first();
        if (empty($project)) {
            return $this->respond([
                'status' => 'error',
                'message' => "Não foi possível criar a tarefa: Projeto não encontrado.",
                'data' => []
            ], 404);
        }

        $taskData->fk_id_project = intval($project->id_project);
        $taskData->uuid_task = GenerateUUID();
        unset($taskData->uuid_project);

        try {
            if ($this->taskModel->insert($taskData)) {
                $task = $this->taskModel->find($this->taskModel->getInsertID());
                return $this->respond([
                    'status' => 'success',
                    'message' => "tarefa criada com sucesso",
                    'data' => $task
                ], 201);
            }
            // dd($taskData);
        } catch (\Exception $error) {
            return $this->respond([
                'status' => 'error',
                'message' => "Não foi possível criar a tarefa: " . $error->getMessage(),
            ], 400);
        } catch (DatabaseException $error) {
            return $this->respond([
                'status' => 'error',
                'message' => "Não foi possível criar a tarefa: " . $error->getMessage(),
            ], 400);
        } catch (\Throwable $error) {
            return $this->respond([
                'status' => 'error',
                'message' => "Não foi possível criar a tarefa: " . $error->getMessage(),
            ], 400);
        }
    }

    public function UpdateTask($uuid = null)
    {
        if (is_null($uuid)) {
            return $this->respond([
                'status' => "error",
                'message' => "UUID inválido",
            ], 400);
        }

        try {
            $task = $this->taskModel->where('uuid_task', $uuid)->first();
            if (is_null($task)) {
                return $this->respond([
                    'status' => "success",
                    'message' => "Tarefa não encontrada",
                    'data' => []
                ], 404);
            }

            $data = $this->request->getJSON();
            $isUpdated = $this->taskModel->update($task->id_task, $data);
            if ($isUpdated) {
                $taskUpdated = $this->taskModel->find($task->id_task);
                return $this->respond([
                    'status' => "success",
                    'message' => "Tarefa atualizada",
                    'data' => $taskUpdated
                ], 200);
            }
        } catch (\Exception $exception) {
            return $this->respond([
                'status' => "error",
                'message' => "Não foi possível atualizar a tarefa: " . $exception->getMessage()
            ], 400);
        }
    }

    public function DeleteTask($uuid = null)
    {
        if (is_null($uuid)) {
            return $this->respond([
                'status' => "error",
                'message' => "UUID inválido"
            ], 400);
        }

        try {

            $task = $this->taskModel->where('uuid_task', $uuid)->first();

            if (is_null($task)) {
                return $this->respond([
                    'status' => "success",
                    'message' => "Projeto não encontrado",
                    'data' => []
                ], 404);
            }

            if ($this->taskModel->delete($task->id_task)) {
                return $this->respond([
                    'status' => "success",
                    'message' => "tarefa excluída com sucesso",
                    'data' => $task
                ], 200);
            }
        } catch (\Exception $error) {
            return $this->respond([
                'status' => "error",
                'message' => "Não foi possível excluir a tarefa: " . $error->getMessage(),
                'data' => []
            ], 400);
        }
    }
}