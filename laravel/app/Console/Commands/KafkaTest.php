<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Kafka\ConsumerConfig;
use Kafka\Producer;
use Kafka\ProducerConfig;

class KafkaTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka';
    
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
        
        /////////////////////producer
        $config = ProducerConfig::getInstance();
        $config->setMetadataRefreshIntervalMs(10000);
        $config->setMetadataBrokerList('192.168.0.113:9092,192.168.0.113:9093,192.168.0.113:9094');//集群配置
        $config->setBrokerVersion('1.1.0');
        $config->setRequiredAck(1);
        $config->setIsAsyn(false);
        $config->setProduceInterval(500);
        $producer = new \Kafka\Producer(function() {
            return array(
                array(
                    'topic' => 'test',
                    'value' => '11111111111111111111111111.',
                    'key' => '11111111111111',
                ),
            );
        });
        $producer->success(function($result) {
            var_dump($result,1);
        });
        $producer->error(function($errorCode) {
            var_dump($errorCode,2);
        });
        $producer->send(true);
        
        
    }
}