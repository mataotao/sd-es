<?php

namespace App\Http\Design\Infrastructure\Library;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Created by PhpStorm.
 * User: matao
 * Date: 2018/6/2
 * Time: 15:55
 */
class RabbitMq
{
    
    /**入列  千万不要循环，循环性能太低
     *
     * @param string $queueName 队列名称
     * @param string $msg 信息（字符串形式）
     */
    public static function push(string $queueName, string $msg): void
    {
        $connection = new AMQPStreamConnection('119.23.237.167', 5672, 'myuser', 'mypass');
        $channel    = $connection->channel();
        $channel->queue_declare($queueName, false, false, false, false);
        $msgContent = new AMQPMessage($msg);
        $channel->basic_publish($msgContent, '', $queueName);
        $channel->close();
        $connection->close();
    }
    
    
    /**
     * 回调函数里面第一句必须加  $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
     *     $callback = function ($msg) {
     * $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
     * echo $msg->body;
     * };
     *
     * @param string $queueName 队列名称
     * @param callable $callback 回调函数
     */
    public static function pop(string $queueName, callable $callback): void
    {
        $connection = new AMQPStreamConnection('119.23.237.167', 5672, 'myuser', 'mypass');
        $channel    = $connection->channel();
        
        $channel->queue_declare($queueName, false, false, false, false);
        
        $channel->basic_consume($queueName, '', false, false, false, false, $callback);//
        
        while (count($channel->callbacks)) {
            $channel->wait();
        }
    }
    
}