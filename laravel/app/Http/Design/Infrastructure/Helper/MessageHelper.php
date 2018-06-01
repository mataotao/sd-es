<?php
namespace App\Http\Design\Infrastructure\Helper;


class MessageHelper
{
    const SUCCESS = 1;
    const ERROR   = -1;
    
    public static function returnMsg($msg, $status = self::SUCCESS)
    {
        return response()->json(compact('status', 'msg'));
    }
    
    public static function returnData($successData,$errorMsg){
        if($successData){
            $status = self::SUCCESS;
            $msg = $successData;
        }else{
            $status = self::ERROR;
            $msg = $errorMsg;
        }
        return self::returnMsg($msg,$status);
    }
}
