<?php

namespace App\Console\Commands;

use App\Http\Design\Infrastructure\Library\RabbitMq;
use Illuminate\Console\Command;
use Monolog\Handler\SocketHandler;
use Monolog\Logger;
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
        $logger = new Logger('my_logger');
        $handler  = new SocketHandler("tcp://119.23.237.167:5000");
        $handler->setPersistent(true);
        $logger->pushHandler($handler, Logger::DEBUG);
        $logger->addInfo('My logger is now ready');
        exit;
        for (;;){
            RabbitMq::push('test2222', 'sssssssssssssssss');
        }
       
    }
}