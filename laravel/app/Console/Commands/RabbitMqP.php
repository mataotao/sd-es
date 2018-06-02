<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMqP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbitp';
    
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
        
        $msg = new AMQPMessage('Hello World!22222222222222222222222222222222222222222');
        $channel->basic_publish($msg, '', 'hello');
        
        $channel->close();
        $connection->close();
    }
}