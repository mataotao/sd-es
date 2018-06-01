<?php

namespace App\Http\Design\Domain;


use App\Http\Helpers\Instance;

class Domain
{
    
    public static function addWhere($model, $wheres)
    {
        foreach ($wheres as $key => $where) {
            $model = self::addArrayWhereNew($model, $where);
        }
        
        return $model;
    }
    
    private static function addArrayWhereNew($model, $where)
    {
        if ($where[1] == 'in' ) {
            return $model->whereIn($where[0], $where[2]);
        } else if($where[1] == 'order' ){
            return $model->orderBy($where[0], $where[2]);
        }else{
            return $model->where($where[0], $where[1], $where[2]);
        }
    }
    
    
    public static function addQueue($tasks)
    {
        $queue = new \SplQueue();
        foreach ($tasks as $task) {
            $queue->enqueue($task);
        }
        
        return $queue;
    }
    
    public static function addStack($tasks)
    {
        $stack = new \SplStack();
        foreach ($tasks as $task) {
            $stack->push($task);
        }
        
        return $stack;
    }
    
    public static function queue()
    {
        $instance = Instance::getInstance();
        
        return $instance->queue();
    }
    
    public static function stack()
    {
        $instance = Instance::getInstance();
        
        return $instance->stack();
    }
    
    
    public static function totalAndToast($model, $func, $toast,$data=[])
    {
        $parameter = [
            'model'   => $model,
            'medthod' => $func,
            'toast'   => $toast,
            'param'   => $data,
        ];
        
        return $parameter;
    }
    
    public static function limitData($model, $page, $pageSize)
    {
        $start = ($page - 1) * $pageSize;
        $model = $model->offset($start)->limit($pageSize);
        
        return $model;
    }
}
