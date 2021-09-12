<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiTrait;
use App\Http\Middleware\Auth;

class DepartmentsController extends Controller
{
    use ApiTrait;
}
