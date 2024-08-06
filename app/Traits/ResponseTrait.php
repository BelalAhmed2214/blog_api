<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait ResponseTrait
{
    public function returnSuccessMessage($msg = "")
    {
        return response()->json([
            "result" => "true",
            "message" => $msg,
            "data" => (object) [],
        ], Response::HTTP_OK);
    }
    public function returnError($msg, $status = Response::HTTP_NOT_FOUND)
    {
        return response()->json([
            'result' => false,
            'status' => $status,
            'message' => $msg,
            'data' => (object) [],
        ], $status);
    }
    public function returnData($key, $value, $msg = "")
    {
        return response()->json([
            'result' => true,
            'message' => $msg,
            $key => $value,
        ], Response::HTTP_OK);
    }
    public function returnValidationError()
    {

    }

}
