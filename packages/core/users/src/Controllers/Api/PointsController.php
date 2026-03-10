<?php

namespace Core\Users\Controllers\Api;


use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Core\Users\DataResources\Api\PointsResource;
use Core\Users\Models\Fav;
use Core\Users\Models\Point;
use Core\Users\Requests\Api\FavRequest;
use Core\Users\Services\FavsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class PointsController extends Controller
{
    use ApiResponse;

    public function __construct(protected FavsService $favsService ){}

    public function index()
    {
        try {
            $user = auth('api')->user();
            $points = Point::where('user_id',$user->id)->paginate();
            return PointsResource::collection($points)
                ->additional([
                        'status'                    =>  'success',
                        'message'                   =>  '',
                        'points_balance'            =>  (int)$user->points_balance,
                        'earned_referral_points'    =>  (int)$user->earned_referral_points,
                        'earned_referral_riyals'    =>  (int)$user->earned_referral_riyals
                ]);

        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
}
