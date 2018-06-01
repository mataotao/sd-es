<?php

namespace App\Http\Design\Entities;

use App\Http\Controllers\Manager\LogController;
use Illuminate\Database\Eloquent\Model;//model基类

class GlobalModel extends Model
{
    
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public static function obtainPage($model, $select,$start,$page = 10, $array = true)
    {
        if (empty($select)) {
            throw  new \Exception('请填写字段');
        }
        if ($array === true) {
            return $model->select($select)->offset($start)->limit($page)->get()->toArray();
        } else {
            return $model->select($select)->offset($start)->limit($page)->get();
        }
        
    }
    
    public static function obtainGet($model, $select, $array = true)
    {
        if (empty($select)) {
            throw  new \Exception('请填写字段');
        }
        $result = $model->select($select)->get();
        if ($array === true && !empty($result)) {
            return $result->toArray();
        } else {
            return $result;
        }
        
    }
    
    public static function obtainFirst($model, $select, $array = true)
    {
        if (empty($select)) {
            throw  new \Exception('请填写字段');
        }
        $result = $model->select($select)->first();
        if ($array === true && !empty($result)) {
            return $result->toArray();
        } else {
            return $result;
        }
    }
    
    
    public static function execSql($data)
    {
        $medthod = $data['medthod'];
        $model   = $data['model'];
        if ($medthod == 'delete') {
            $res = $model->$medthod();
        } else {
            $res = $model->$medthod($data['param']);
        }
        $log = [
            '操作'    => $data['medthod'],
            '参数'    => $data['param'],
            'toast' => $data['toast'],
            '结果'    => $res,
        ];
        LogController::log($log);
        
        return $res;
    }
    
    public static function aggregationFunction($data)
    {
        $medthod = $data['medthod'];
        $model   = $data['model'];
        if($medthod=='count'){
            $res     = $model->$medthod();
        }else{
            $res     = $model->$medthod($data['param']);
        }
        
        return $res;
    }
    
    
}
