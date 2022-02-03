<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Porabote\FullRestApi\Server\ApiTrait;
use App\Http\Middleware\Auth;

class DepartmentsController extends Controller
{
    use ApiTrait;
}
