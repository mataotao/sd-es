<?php

namespace App\Console\Commands;

use App\Http\Design\Infrastructure\Helper\Scheduler;
use App\Http\Design\Infrastructure\Library\Redis;
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
        for(;;){
            $a = [];
            for ($i = 0; $i <= 10; $i++) {
                $a[] = [
                    'topic' => 'test2',
                    'value' => serialize(new Redis()),
                ];
            }
            $this->send($a);
            unset($a);
        }
        
    }
    
    public function send($data): void
    {
        $config = ProducerConfig::getInstance();
        $config->setMetadataRefreshIntervalMs(10000);
//        $config->setMessageMaxBytes(999999999);
        $config->setMetadataBrokerList('119.23.237.167:9092,119.23.237.167:9093,119.23.237.167:9094');//集群配置
        $config->setBrokerVersion('1.1.0');
        $config->setRequiredAck(1);
        $config->setIsAsyn(false);
        $config->setProduceInterval(500);
        $send = [];
        
        foreach ($data as $value) {
            $send[] = [
                'topic' => $value['topic'],
                'value' => $value['value'],
            ];
        }
        $producer = new \Kafka\Producer(function () use ($send) {
            return $send;
        });
        
        $producer->success(function ($result) {
            var_dump($result);
        });
        $producer->error(function ($errorCode) {
            var_dump($errorCode);
        });
        $producer->send(true);
        
    }
}