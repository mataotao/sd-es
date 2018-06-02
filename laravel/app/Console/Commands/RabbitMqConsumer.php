<?php

namespace App\Console\Commands;

use App\Http\Design\Infrastructure\Library\RabbitMq;
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
        $callback = function ($msg) {
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
            echo $msg->body;
        };
        RabbitMq::pop('test2222', $callback);
        
        
    }
}