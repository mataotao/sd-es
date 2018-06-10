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
        $fp           = stream_socket_client("tcp://119.23.237.167:5000", $errno, $errstr);
        if (!$fp) {
            return "$errstr ($errno)<br />\n";
        } else {
            fwrite($fp, 'testdsdasdasdasd');
            while (!feof($fp)) {
                return fgets($fp, 1024) ;
            }
            fclose($fp);
        }
        echo 1;
        exit;
        for (;;){
            RabbitMq::push('test2222', 'sssssssssssssssss');
        }
       
    }
}