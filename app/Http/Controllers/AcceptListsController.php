<?php
namespace App\Http\Controllers;

use App\Models\AcceptLists;
use App\Models\AcceptListsSteps;
use App\Models\AcceptListsStepsDefault;
use App\Models\AcceptListsAcceptors;
use Porabote\Auth\Auth;
use Carbon\Carbon;

class AcceptListsController extends Controller {

    static $authAllows;

    function __construct()
    {
        self::$authAllows = [
            'getStepsBydefault',
            'getSteps',
            'deleteStep',
            'changeAcceptor'
        ];
    }

    static function _addAcceptor($data)
    {
        $data['account_id'] = Auth::$user->account_id;
        AcceptListsAcceptors::create($data);
    }

    function addStepsByDefault($listId, $foreignKey, $model)
    {
        self::_addStepsByDefault($listId, $foreignKey, $model);
    }

    static function _addStepsByDefault($listId, $foreignKey, $model)
    {
        $steps = AcceptListsStepsDefault::where(['accept_list_id' => $listId])
            ->get()
            ->toArray();

        $order = 1;
        foreach($steps as $step) {
            self::_addStep([
                'foreign_key' => $foreignKey,
                'model' => $model,
                'step_default_id' => $step['id'],
                'lft' => $order
            ]);
            $order++;
        }

        return $steps;
    }


    public function setAcceptors($request)
    {
        $data = $request->all();

        foreach($data['steps'] as $step) {
            // Сохраняем акцепторов только для дефолтных шагов
            if ($step['step_default_id'] && $step['user_id']) {
                AcceptListsAcceptors::create([
                    'user_id' => $step['user_id'],
                    'step_id' => $step['id'],
                    'account_id' => Auth::$user->account_id,
                ]);
            }
        }

        return response()->json([
            'data' => $data['steps'],
            'meta' => []
        ]);
    }

    public function addStep($request)
    {
        $requestData = $request->all();

        if ($requestData) {

            $lft = $requestData['lft'] + 1;

            $step = self::_addStep([
                'foreign_key' => $requestData['foreignKey'],
                'model' => $requestData['model'],
                'step_default_id' => null,
                'lft' => $lft
            ]);

            self::_reorderSteps($step, $lft);

            if ($step) {
                self::_addAcceptor([
                    'step_id' => $step['id'],
                    'user_id' => $requestData['user_id']
                ]);
            }

            return response()->json([
                'data' => $step,
                'meta' => []
            ]);
        }
    }


    static function _addStep($data)
    {
        $data['account_id'] = Auth::$user->account_id;

        return AcceptListsSteps::create($data);
    }

    static function _reorderSteps($currentStep, $lft)
    {
        $steps = self::_getSteps($currentStep['foreign_key'], $currentStep['model']);
        foreach ($steps as $step) {
            if ($step['lft'] < $lft || $currentStep['id'] == $step['id']) {
                continue;
            } elseif ($step['lft'] >= $lft) {
                $stepRecord = AcceptListsSteps::find($step['id']);
                $stepRecord->lft += 1;
                $stepRecord->update();
            }
        }
    }


    function getSteps($request)
    {
        $steps = self::_getSteps(
            $request->query('foreignKey'),
            $request->query('model')
        );

        return response()->json([
            'data' => $steps,
            'meta' => []
        ]);
    }

    static function _getSteps($foreignKey, $model)
    {
        return AcceptListsSteps::orderBy('lft', 'asc')
            ->where('foreign_key', $foreignKey)
            ->where('model', $model)
            ->where('active', 1)
            ->with('acceptor.api_user')
            ->with('default_step.default_users.api_user')
            ->get()
            ->toArray();
    }

    public function deleteStep($request)
    {
        $data = $request;
        $step = self::_deleteStep($request['stepId']);

        return response()->json([
            'data' => $step,
            'meta' => []
        ]);
    }

    static function _deleteStep($stepId)
    {
        $step = AcceptListsSteps::with('acceptor')->find($stepId);

        $step->acceptor()->delete();
        $step->active = 0;
        $step->update();

        return $step;
    }

    public function acceptStep($request)
    {
        $query = $request->all();
        $step = AcceptListsSteps::with('acceptor')->find($query['stepId']);

        $step->acceptor->accepted_at = Carbon::now();
        $step->acceptor->update();

        return response()->json([
            'data' => $step->toArray(),
            'meta' => []
        ]);
    }

    function declineStep($request)
    {
        $query = $request->all();
        $step = AcceptListsSteps::with('acceptor')->find($query['stepId'])->toArray();

        $steps = AcceptListsSteps::with('acceptor')
            ->where('model', $step['model'])
            ->where('foreign_key', $step['foreign_key'])
            ->get();

        foreach ($steps as $step) {
            $step->acceptor->accepted_at = null;
            $step->acceptor->update();
        }

        return response()->json([
            'data' => $steps->toArray(),
            'meta' => []
        ]);
    }

    function changeAcceptor($request)
    {
        $data = $request->all();
        $step = AcceptListsSteps::with('acceptor.api_user')->find($data['stepId']);
        $step->acceptor->user_id = $data['user_id'];
        $step->acceptor->update();

        $step = AcceptListsSteps::with('acceptor.api_user')->find($data['stepId']);
        return response()->json([
            'data' => $step->toArray(),
            'meta' => []
        ]);
    }

}

?>