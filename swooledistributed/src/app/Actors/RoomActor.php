<?php
/**
 * 游戏房间
 * User: 4213509@qq.com
 * Date: 18-05-17
 * Time: 上午10:10
 */
namespace app\Actors;
use Server\Components\Event\EventDispatcher;
use Server\CoreBase\Actor;
use Server\CoreBase\ChildProxy;

class RoomActor extends Actor
{
    
    // 用户离线消息处理  自己判断是要让用户退出还是离线
    public function offlineMessage($userId, $code = 1)
    {
        
        //处理离线逻辑
        //....
        echo __FUNCTION__.PHP_EOL;
        
        // 发送消息给房间所有人该用户的离线事件
        $send['cmd'] = $code;
        $send['code'] = 200;
        $send['data']['userId'] = $userId;
        $this->sendPub('Room/' . $this->saveContext['room_id'], $send);
    }
    
    /**
     * 初始化储存房间信息
     * @param $room_info
     */
    public function initData($room_info){
        
        $this->saveContext['info'] = $room_info;
        
    }
    
    /**
     * 进房询问
     * @param $user_info
     * @return bool
     * @throws \Server\Asyn\MQTT\Exception
     */
    public function joinRoomReply($user_info)
    {
        $user_id = $user_info['id'];
        $join_users = $this->saveContext['user_list'];  // 已经进入的用户储存在这里
        if (!isset($join_users[$user_id])) { // 检查用户是否已经进来
            //当前用户还没有进入房间的逻辑
            if (count($join_users) >= 6) {  //房间总共可以进入6个人，查询是否已经满了。 满了就不让进来了
                echo '房间已满';
                //throw new GameException("房间已满");
            }
            $corrent_user_actor = $this->name . $user_id; //根据规则创建一个唯一的命名
            try {
                Actor::create(PlayerActor::class, $corrent_user_actor); //没有进来过 创建一个用户的actor
                $user_info['room_actor'] = $this->name;
                Actor::getRpc($corrent_user_actor)->initData($user_info);  //初始化该actor的默认值属性 initData这个方法是储存用户信息
            } catch (\Exception $e) {
                echo $e->getMessage();
                //throw new GameException('创建失败：'.$e->getMessage())
                }
            $this->saveContext->getData()['user_list'][$user_id] = $corrent_user_actor; //储存当前用户和其中对应的actorName
            $this->saveContext->save();
        }else{
            //重新进入房间的逻辑
        }
        get_instance()->addSub('Room/' . $this->name, $user_id);  // 进入成功，开始订阅当前房间消息
        get_instance()->pub('Room/' . $this->name, '给所有用户推送$user_id进到房间了');
        return true;
    }
    
    public function registStatusHandle($key, $value)
    {

        var_dump($key,$value);
        echo __CLASS__.__FUNCTION__.PHP_EOL;
        // TODO: Implement registStatusHandle() method.
    }
    
}

