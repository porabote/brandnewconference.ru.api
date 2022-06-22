<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Porabote\FullRestApi\Server\ApiTrait;
use App\Http\Middleware\Auth;
use App\Models\DepartmentsLegacy;
use App\Models\Departments;

class DepartmentsController extends Controller
{
    use ApiTrait;
    static $authAllows;

    function __construct()
    {
        self::$authAllows = [
            'migrations'
        ];
    }

    function migrations()
    {
        $user = new \stdClass();
        $user->account_alias = 'Thyssen'; //Solikamsk  Thyssen
        \Porabote\Auth\Auth::setUser($user);

        $departments = Departments::where('account_id', 4)->get()->toArray();
        foreach ($departments as $department) {
            $departmentsList[$department['local_id']] = $department['id'];
        }
//debug($departmentsList);



//        $bills = \App\Models\Bills::get();
//        foreach ($bills as $bill) {
//            if (!$bill['object_id']) continue;
//            $bill['object_id'] = $departmentsList[$bill['object_id']];
//          //  debug($bill['object_id']);
//          //  $bill->update();
//        }


//        $payments = \App\Models\Payments::get();
//        foreach ($payments as $payment) {
//            if (!$payment['object_id']) continue;
//
//            $payment['object_id'] = $departmentsList[$payment['object_id']];
//          //  debug($payment['object_id']);
//            //  $payment->update();
//        }

//        $purchasesRequests = \App\Models\PurchaseRequest::get();
//        foreach ($purchasesRequests as $purchasesRequest) {//debug($purchasesRequest);
//            //$purchasesRequest['department_id'] = $departmentsList[$purchasesRequest['department_id']];
//           // debug($departmentsList[$purchasesRequest['object_id']]);
//            if (!$purchasesRequest['department_now_id']) continue;
//            $purchasesRequest['department_now_id'] = $departmentsList[$purchasesRequest['department_now_id']];
//
//         //   $purchasesRequest['department_id'] = $departmentsList[$purchasesRequest['department_id']];
//           // $purchasesRequest->update();
//        }

//        $departmentsOld = DepartmentsLegacy::get()->toArray();
//        $compareList = [];
     //   debug($departmentsList);


//        foreach ($departments as $department) {
//            $departmentNew = [
//                'name' => $department['name'],
//                'company_id' => '1',
//                'label' => $department['label'],
//                'code' => $department['code'],
//                'account_id' => '3',
//                'local_id' => $department['id']
//            ];
//            //Departments::create($departmentNew );
//        }
    }
}
