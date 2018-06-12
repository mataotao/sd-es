<?php

namespace app\Controllers;


use Server\Components\Event\EventDispatcher;
use Server\CoreBase\Controller;

class AppController extends Controller
{
    
    public function onClose()
    {
        if ($this->uid > 0) {
            $this->unBindUid();
            print_r($this->uid . "用户断线");
            EventDispatcher::getInstance()->dispatch('offline' . $this->uid);
        }
        $this->destroy();
    }
    
    public function onopen(){
        echo __FUNCTION__;
    }
    
}
