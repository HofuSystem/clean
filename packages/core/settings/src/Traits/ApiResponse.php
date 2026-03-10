<?php

namespace Core\Settings\Traits;

trait ApiResponse
{

    public function returnErrorMessage($msg,$errors=[],$data= [],$statusCode = null)
    {
        return response()->json(array_merge([
            'status' => request()->is('api/*') ? "fail": false,
            'message' => ucfirst(trans(trim($msg))),
            'errors' => $errors
        ],$data),
        $statusCode ?? 422);
    }

    public function returnSuccessMessage($msg = "",$statusCode = null)
    {
        return response()->json([
            'status' => request()->is('api/*') ? "success": true,
            'message' => ucfirst(trans(trim($msg)))
        ],
        $statusCode ?? 200);
    }

    public function returnData($msg = "",$data,$statusCode = null)
    {
        if(is_array($data)){
            $data = array_merge([
             'status' => request()->is('api/*') ? "success" : true,
             'message' => ucfirst(trans(trim($msg))),
            ],$data);
       }else{
            $data = [
                'status'    => request()->is('api/*') ? "success" : true,
                'message'   => ucfirst(trans(trim($msg))),
                'data'      => $data
            ];
        }
        return response()->json($data,$statusCode ?? 200);
    }

    public function returnPagination($msg = "",$data,$statusCode =null)
    {
        return response()->json(array_merge([
            'status'   => request()->is('api/*') ? "success": true,
            'message'  => ucfirst(trans(trim($msg)))],
            $data
        ),$statusCode ?? 200);
    }

    public function returnValidationError($validator)
    {
        return $this->returnErrorMessage(ucfirst($validator->errors()->first()),$validator->errors(),[],422);
    }


}
