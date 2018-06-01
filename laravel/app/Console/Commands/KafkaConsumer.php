<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Kafka\ConsumerConfig;
use Kafka\Producer;
use Kafka\ProducerConfig;

class KafkaConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka2';
    
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
        $config = ConsumerConfig::getInstance();
        $config->setMetadataRefreshIntervalMs(10000);
        $config->setMetadataBrokerList('119.23.237.167:9092,119.23.237.167:9093,119.23.237.167:9094');
        $config->setGroupId('test2');
        $config->setBrokerVersion('1.1.0');
        $config->setTopics(array('test2'));
        $config->setoffsetReset('latest');
        $consumer = new \Kafka\Consumer();
        $consumer->start(function($topic, $part, $message) {
            var_dump($message);
           echo file_put_contents('test.log',print_r($message,true),FILE_APPEND);
        });
        
    }
}