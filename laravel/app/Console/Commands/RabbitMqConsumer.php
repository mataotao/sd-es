<?php

namespace App\Console\Commands;

use App\Http\Design\Infrastructure\Library\RabbitMq;
use App\Http\Design\Infrastructure\Library\Redis;
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
        for (; ;) {
        
            try {
                $callback = function ($msg) {
                    echo $msg->body . PHP_EOL;
                    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
                };
                RabbitMq::pop('test2222', $callback);
            } catch (\Exception $exception) {
                $errorLog = [
                    'errorMessage' => $exception->getMessage(),
                    'file'         => $exception->getFile(),
                    'line'         => $exception->getLine(),
                ];
               
            }
            sleep(1);
        }
    }
}