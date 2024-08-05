<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait ResponseTrait{
    public function returnSuccessMessage($msg=""){       
        return response()->json([
            "result"=>"true",
            "message"=>$msg,
            "data"=>(object)[]
        ],Response::HTTP_OK);
    }
    public function returnError($msg){
      return response()->json([
        'result' => false,
        'message' => $msg,
        'data'   => (object)[],
      ], Response::HTTP_NOT_FOUND);
    }
    public function returnData($key, $value, $msg = ""){
      return response()->json([
        'result' => true,
        'message' => $msg,
        $key => $value
      ],Response::HTTP_OK);
    }

}