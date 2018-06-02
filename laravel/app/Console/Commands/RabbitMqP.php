<?php

namespace App\Console\Commands;

use App\Http\Design\Infrastructure\Library\RabbitMq;
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
        RabbitMq::push('test2222', 44444444444444);
    }
}