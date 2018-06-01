<?php
namespace App\Http\Design\Infrastructure\Helper;
class HashTable
{
    private $arr;
    private $size = 16;
    
    public function __construct()
    {
        $this->arr = new \SplFixedArray($this->size);
    }
    
    private function simpleHash($key)
    {
        $asciiTotal = 0;
        $len        = strlen($key);
        for ($i = 0; $i < $len; $i++) {
            $asciiTotal += ord($key[$i]);
        }
        
        return $asciiTotal % $this->size;
    }
    
    public function set($key, $value)
    {
        $hash = $this->simpleHash($key);
        if (isset($this->arr[$hash])) {
            $newNode = new HashNode($key, $value, $this->arr[$hash]);
        } else {
            $newNode = new HashNode($key, $value, null);
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
        $this->size = $size;
        $this->arr->setSize($size);
    }
}

class HashNode
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