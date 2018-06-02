<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMqConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbitc';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test';
    
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $connection = new AMQPStreamConnection('192.168.0.113', 5672, 'myuser', 'mypass');
        $channel = $connection->channel();
    
        $channel->queue_declare('hello', false, false, false, false);
        
        $callback = function ($msg) {
            echo $msg->body;
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };
    
        $channel->basic_consume('hello', '', false, false, false, false, $callback);//
    
        while (count($channel->callbacks)) {
            $channel->wait();
        }
        
    }
}