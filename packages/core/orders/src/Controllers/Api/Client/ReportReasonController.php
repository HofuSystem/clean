<?php

namespace Core\Orders\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Core\Orders\DataResources\Api\Client\Order\ReportReasonsResource;
use Core\Orders\Models\ReportReason;
use Core\Orders\Requests\Api\CartsRequest;
use Core\Orders\Services\ReportReasonsService;
use Core\Settings\Traits\ApiResponse;

class ReportReasonController extends Controller
{
    use ApiResponse;
    public function __construct(protected ReportReasonsService $reportReasonsService){}

    public function list(){
       $reportReason = ReportReason::with('translations')->get();
       return $this->returnData(trans('Report Reasons'),[
        'status'    => 'success',
        'data'      => ReportReasonsResource::collection($reportReason),
       ]);
    }



}
