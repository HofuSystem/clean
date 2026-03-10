<?php

namespace Core\MediaCenter\Controllers\Dashboard;


use App\Http\Controllers\Controller;
use Core\Entities\Helpers\CoreValidator;
use Core\Entities\Helpers\FelidsLoader;
use Core\Entities\Helpers\FormBuilderHelper;
use Core\MediaCenter\Helpers\MediaCenterHelper;
use Core\Entities\Helpers\MigrationBuilder;
use Core\Entities\Helpers\ModelBuilder;
use Core\Entities\Models\Entity;
use Core\Entities\Models\Package;
use Core\MediaCenter\Requests\Api\AddMediaCenterRequest;
use Core\MediaCenter\Requests\Api\MediaCentersRequest;
use Illuminate\Http\Request;

class MediaCenterController extends Controller
{
    public function listAll(Request $request)  
    {
        $media = MediaCenterHelper::getMediaList($request->type);
        return response()->json(['data' => $media]);
    }
    public function addNew(AddMediaCenterRequest $request)  {
        $media = MediaCenterHelper::saveMedias($request->file('files'),$request->type);
        return response()->json(['data' => $media]);
    }
    public function delete(Request $request)  {
        return MediaCenterHelper::deleteFile($request->file_name);
    }
}
