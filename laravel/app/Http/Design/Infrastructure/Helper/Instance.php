<?php
namespace App\Http\Design\Infrastructure\Helper;


class Instance
{
    private static $_instance        = null;
    private static $queue            = null;
    private static $stack            = null;
    private static $priorityQueue    = null;
    private static $doublyLinkedList = null;
    
    
    private function __construct()
    {
        self::$queue            = new \SplQueue();
        self::$stack            = new \SplStack();
        self::$priorityQueue    = new \SplPriorityQueue();
        self::$doublyLinkedList = new \SplDoublyLinkedList();
    }
    
    private function __clone()
    {
        
    }
    
    public static function getInstance()
    {
        if (is_null(self::$_instance) || !isset (self::$_instance)) {
            self::$_instance = new self ();
        }
        
        return self::$_instance;
    }
    
    public function queue()
    {
        return self::$queue;
    }
    
    public function stack()
    {
        return self::$stack;
    }
    
    public function priorityQueue()
    {
        return self::$priorityQueue;
    }
    
    public function doublyLinkedList()
    {
        return self::$doublyLinkedList;
    }
    
}
