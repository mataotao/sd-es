<?php

namespace App\Http\Design\Infrastructure\Helper;
class Slice
{
    private $arr;
    private $size = 16;
    private $len  = 0;
    
    public function __construct()
    {
        $this->arr = new \SplFixedArray($this->size);
    }
    
    public function set($value, $key = "")
    {
        if (empty($key)) {
            $key = $this->len;
        }
        $this->len = $key + 1;
        len:
        if ($key > $this->size - 1) {
            $this->editSize($this->size * 2);
            goto len;
        }
        
        $this->arr[$key] = $value;
        
        return true;
    }
    
    public function get($key)
    {
        return $this->arr[$key];
    }
    
    public function getList()
    {
        return $this->arr;
    }
    
    public function editSize($size)
    {
        $this->size = $size;
        $this->arr->setSize($size);
    }
    
    private function cap($key)
    {
        if ($key > $this->size - 1) {
            $this->editSize($this->size * 2);
            $this->cap($key);
        }
    }
    
}
