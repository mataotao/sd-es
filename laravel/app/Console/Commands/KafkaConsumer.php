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
        $config->setMetadataBrokerList('192.168.0.113:9092,192.168.0.113:9093,192.168.0.113:9094');
        $config->setGroupId('test');
        $config->setBrokerVersion('1.1.0');
        $config->setTopics(array('test'));
        $consumer = new \Kafka\Consumer();
        $consumer->start(function($topic, $part, $message) {
        });
        
    }
}