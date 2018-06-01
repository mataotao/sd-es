<?php

namespace App\Http\Design\Infrastructure\Helper;

class SliceMap
{
    private $arr;
    private $size  = 16;
    private $len   = 0;
    private $power = 4;
    
    public function __construct()
    {
        $this->arr = new \SplFixedArray($this->size);
    }
    
    private function simpleHash($key)
    {
        $hash = 0;
        $len  = strlen($key);
        for ($i = 0; $i < $len; $i++) {
            $hash += 33 * $hash + ord($key[$i]);
        }
        
        return $hash & ($this->size - 1);
    }
    
    public function set($key, $value)
    {
        $this->len++;
        if ($this->len > $this->size) {
            $this->init();
        }
        $hash = $this->simpleHash($key);
        if (isset($this->arr[$hash])) {
            $newNode = new SliceNode($key, $value, $this->arr[$hash]);
        } else {
            $newNode = new SliceNode($key, $value, null);
        }
        $this->arr[$hash] = $newNode;
        
        return true;
    }
    
    public function get($key)
    {
        $hash    = $this->simpleHash($key);
        $current = $this->arr[$hash];
        while (!empty($current)) {
            if ($current->key == $key) {
                return $current->value;
            }
            $current = $current->nextNode;
        }
        
        return null;
    }
    
    public function getList()
    {
        return $this->arr;
    }
    
    public function editSize($size)
    {
        
        //log(16,2);这个是浮点数
        $log   = new log2();
        $power = $log->is_power($size);
        if ($power === false) {
            return false;
        }
        $this->power = $power;
        $this->size  = pow(2, $this->power);
        $this->arr->setSize($size);
    }
    
    public function getLen()
    {
        return $this->len;
    }
    
    private function init()
    {
        $this->power = $this->power + 1;
        $size        = pow(2, $this->power);
        $this->size  = $size;
        $tmp         = $this->arr;
        $this->arr   = new \SplFixedArray($this->size);
        foreach ($tmp as $item) {
            $this->recursive($item);
        }
    }
    
    private function recursive($item)
    {
        if (!is_null($item)) {
            $hash = $this->simpleHash($item->key);
            if (isset($this->arr[$hash])) {
                $newNode = new SliceNode($item->key, $item->value, $this->arr[$hash]);
            } else {
                $newNode = new SliceNode($item->key, $item->value, null);
            }
            $this->arr[$hash] = $newNode;
            $this->recursive($item->nextNode);
        }
    }
}

class SliceNode
{
    public $key;
    public $value;
    public $nextNode;
    
    public function __construct($key, $value, $nextNode = null)
    {
        $this->key      = $key;
        $this->value    = $value;
        $this->nextNode = $nextNode;
    }
}

class log2
{
    private function log2($value)
    {
        $x = 0;
        while ($value > 1) {
            $value >>= 1;
            $x++;
        }
        
        return $x;
    }
    
    public function is_power($num)
    {
        if ($num & ($num - 1)) { //0000&1111
            return false;
        } else {
            return $this->log2($num);
        }
    }
}