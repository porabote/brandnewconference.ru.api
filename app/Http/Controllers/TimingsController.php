<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Timings;
use App\Models\File;
use Porabote\FullRestApi\Server\ApiTrait;

class TimingsController extends Controller
{
    use ApiTrait;

    function create(Request $request)
    {
        $data = $request->all();

        $this->setDatetimesRange($data);

        $record = Timings::create($data);

        return response()->json([
            'data' => $record,
            'meta' => []
        ]);
    }

    function edit($request)
    {
        $data = $request->all();

        $this->setDatetimesRange($data);

        $record = Timings::find($data['id']);

        foreach ($data as $field => $value) {
            if (array_key_exists($field, $record->getAttributes())) $record->$field = $value;
        }

        $record->update();

        return response()->json([
            'data' => $record,
            'meta' => []
        ]);
    }

    function setDatetimesRange(&$data)
    {
        $timeRangeParts = explode('-', $data['time_range']);
        $timeFrom = explode(':', $timeRangeParts[0]);
        $timeTo = explode(':', $timeRangeParts[1]);

        $dateFrom = new \DateTime($data['date']);
        $dateFrom->setTime($timeFrom[0], $timeFrom[1]);
        $data['datetime_from'] = $dateFrom;

        $dateTo = new \DateTime($data['date']);
        $dateTo->setTime($timeTo[0], $timeTo[1]);
        $data['datetime_to'] = $dateTo;
    }
}