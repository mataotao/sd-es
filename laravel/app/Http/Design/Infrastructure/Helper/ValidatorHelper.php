<?php

namespace App\Http\Design\Infrastructure\Helper;


use App\Http\Controllers\Manager\LogController;

class ValidatorHelper
{
    
    public function validate($data, $rule, $msg)
    {
        $validator = \Validator::make($data, $rule, $msg);
        if ($validator->fails()) {
            $throwStr = '';
            foreach ($validator->errors()->all() as $value) {
                $throwStr = $throwStr . $value . "  ";
            }
            $status = MessageHelper::ERROR;
            $toast  = $throwStr;
            
            return MessageHelper::returnMsg($toast, $status);
        } else {
            return '';
        }
    }
    
    public function TryCatch($parameter, $class, $method, $msg, $transmutation = false)
    {
        $error = $msg->get('error');
        try {
            $data = $class->$method($parameter);
            if ($transmutation === true) {
                return $data;
            }
            
            return MessageHelper::returnMsg($data['msg'], $data['status']);
        } catch (\Exception $ex) {
            $status = MessageHelper::ERROR;
            $log    = [
                'msg'  => $ex->getMessage(),
                "操作"   => $msg->get('catch_error'),
                '路由'   => $msg->get('class') . "->" . $msg->get('function'),
                'file' => $ex->getFile(),
                'line' => $ex->getLine(),
            ];
            LogController::error($log);
            
            return MessageHelper::returnMsg($error, $status);
        }
    }
    
    
    public function assemblyParameter($param, $acquiringParam, $notFilter = [])
    {
        $sliceMap = new SliceMap();
        foreach ($acquiringParam as $parameter => $default) {
            $value = isset($param[$parameter]) ? $param[$parameter] : $default;
            if ((empty($notFilter) || !in_array($parameter, $notFilter)) && is_string($value)) {
                $value = StringHelper::htmlentites($value);
            }
            
            $sliceMap->set($parameter, $value);
        }
        
        return $sliceMap;
        
    }
}
