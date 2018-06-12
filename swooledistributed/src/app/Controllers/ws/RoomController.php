<?php
/**
 * Created by PhpStorm.
 * User: matao
 * Date: 2018/5/10
 * Time: 13:17
 */

namespace app\Controllers\ws;


use app\Actors\RoomActor;
use Server\CoreBase\Actor;
use Server\CoreBase\Controller;

class RoomController extends Controller
{
    public function create(): void
    {
        $room_id = 1;
        $RoomActorName = 'roomActorId' . $room_id;  // 房间的actor可以用一个规则来命名来保证唯一性
        
        Actor::create(RoomActor::class, $RoomActorName); //创建房间Actor
        $room_info = '测试';
        Actor::getRpc($RoomActorName)->initData($room_info);// 可以在创建完成后在初始化房间的数据
     
    }
    
    public function join(){
    
        $room_id = 1;
        $RoomActorName = 'roomActorId' . $room_id;
        $user_info = [
            'name' => 'mt',
            'id'   => 1,
        ];
        $join_res  = Actor::getRpc($RoomActorName)->joinRoomReply($user_info);
    }
}
