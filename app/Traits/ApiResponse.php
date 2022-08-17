<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Arr;

trait ApiResponse{


    protected function successResponse($data, $message = null, $code = 200)
	{
		return response()->json([
			'status_code'=> $code,
			'message' => $message,
			'data' => $data
		],Response::HTTP_OK);
	}

	protected function errorResponse($message = null, $code)
	{
		return response()->json([
			'status_code'=>$code,
			'message' => $message,
			'data' => null
		],Response::HTTP_OK);
	}

    public function validationFailed($errors){
        $errors = $errors->toArray();
        $errors = array_values($errors);
        $errors = call_user_func_array('array_merge', $errors);
        $message=Arr::first($errors);
        return $this->errorResponse($message,Response::HTTP_BAD_REQUEST);
    }

}
