<?php

namespace App\Controllers\Api;

use App\Models\CategoryModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class Category extends ResourceController
{
    use ResponseTrait;

    protected $categoryModel;
    protected $format = 'json';

    public function __construct()
    {
        $this->categoryModel  = new CategoryModel();
    }

    public function index()
    {
        //
    }
}