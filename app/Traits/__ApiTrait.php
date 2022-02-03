<?php
namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Files;
use App\Http\Responses\RestDataItem;

trait ApiTrait
{
    private $modeName;

    function get(Request $request, $id = null)
    {

        $Response = new \App\Http\Responses\RestResponse($request);

        $className = '\App\Models\\' . $this->getModelName();

        if(class_exists($className)) {
            $model = new $className();
            $query = DB::connection($model->getConnectionName())->table($this->camelToSnake($model->getTable()));
        } else {
            $query = DB::connection('api_mysql')->table(strtolower($this->getModelName()));
        }

        if ($id) {
            $modelAlias = '\App\Models\\' . $this->getModelName();
            $model = $modelAlias::find($id);

            $data = $model;

            $item = new RestDataItem($data->toArray(), strtolower($this->getModelName()), '/report/get');

            $item->setRelationships($this->getRelationships($request->query(), $model));
            $Response->data = $item;

        } else {

            $this->setWhere($query, $request->query(), $id);
            $this->setWhereIn($query, $request->query());
            $data = $query->limit(1000)->orderBy('id', 'desc')->get()->toArray();

            foreach ($data as $datum) {
                $item = new RestDataItem( (array) $datum, strtolower($this->getModelName()), '/' . strtolower($this->getModelName()) . '/get/' . $datum->id);

                $modelAlias = '\App\Models\\' . $this->getModelName();
                $model = $modelAlias::find($datum->id);
                $item->setRelationships($this->getRelationships($request->query(), $model));

                $Response->setData($item);
            };
        }

        if(method_exists($this, 'getHandle')) {
            $Response = $this->getHandle($Response);
        }

        if (!$Response) $Response = [];

        return response()->json($Response);
    }

    function _get()
    {

    }

    function _getAll()
    {

    }

    function camelToSnake($input) {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    function getRelationships($query, $model)
    {
        $relationships = [];

        if (isset($query['include']) && is_array($query['include'])) {
            foreach ($query['include'] as $relatedModelName) {

                if (
                    is_object($model->$relatedModelName)
                    && get_class($model->$relatedModelName) == 'Illuminate\Database\Eloquent\Collection'
                ) {
                    $relationships[$relatedModelName]['data'] = $model->$relatedModelName->map(function ($data) use ($relatedModelName) {

                        return new RestDataItem(
                            $data->toArray(),
                            $relatedModelName,
                            '/' . $relatedModelName . '/get'
                        );
                    });
                } else {

                    if ($model->$relatedModelName) {
                        $relationships[$relatedModelName] = new RestDataItem(
                            $model->$relatedModelName->toArray(),
                            $relatedModelName,
                            '/' . $relatedModelName . '/get'
                        );
                    }
                }
            }
        }

        return $relationships;
    }

    function getRelationship($id, $assocAlias)
    {
        $modelAlias = '\App\Models\\' . $this->getModelName();

        if (isset($this->request->query()['include'])) {

            $record = $modelAlias::with($this->request->query()['include']);
            $record = $record->find($id)->toArray();
        } else {
            $record = $modelAlias::find($id)->toArray();
        }

        $Response = new \App\Http\Responses\RestResponse($this->request);

        foreach ($record[$assocAlias] as $item) {
            $link = '/api/' . strtolower($this->getModelName()) . '/' . $item['id'] . '/relationships/comments?' . http_build_query($this->request->query());
            $item = new RestDataItem($item, $assocAlias, $link);
            $Response->setData($item);
        }

        return response()->json($Response);
    }

    function getModelName()
    {
        preg_match('/([a-z]+)Controller$/i', get_class(), $modelName);
        return $modelName[1];
    }

    function delete($id)
    {
        $modelAlias = '\App\Models\\' . $this->getModelName();
        $modelAlias::destroy($id);

        return response()->json([]);
    }

    function setWhere($query, $filterData, $id = null)
    {

        if (isset($filterData['where']) && is_array($filterData['where'])) {
            foreach ($filterData['where'] as $operand => $params) {
                foreach ($params as $paramName => $value) {
                    if(!empty($value)) {
                        $query->where($paramName, $operand, $value);
                    }
                }
            }
        }
    }

    function setWhereIn($query, $filterData)
    {
        if (isset($filterData['whereIn']) && is_array($filterData['whereIn'])) {
            foreach ($filterData['whereIn'] as $paramName => $value) {
                $query->whereIn($paramName, $value);
            }
        }
    }

    function setInclude($query, $filterData)
    {
        if (isset($filterData['include']) && is_array($filterData['include'])) {
            foreach ($filterData['include'] as $assocModelName) {
                //debug($assocModelName);
            }
        }
        //exit();
    }
}


?>