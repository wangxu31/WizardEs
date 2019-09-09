<?php
namespace Wizard\Elasticsearch;

/**
 * 操作示例类
 * Class DemoController
 * @package Wizard\Elasticsearch
 * @author wangxu <wxu@wooduan.com>
 */

class DemoController extends WizardLogManager
{
    protected $casts = [
        'log_level'     => 'text',
        'created_at'    => 'date',
        'kibana_time'   => 'date',
    ];
    const HOSTS = ['host'=>'127.0.0.1:9200'];
    const RETRIES = 3;
    const CLIENT_PARAMS = ['ignore'=>[400,404],'timeout'=>60,'connect_timeout'=>10];

    public function __construct($index, $type){
        /**
         * 传入host配置
         * 重试次数配置
         */
        parent::__construct(self::HOSTS, self::RETRIES);

        /**
         * 设置索引
         */
        $this->setIndex($index);

        /**
         * 设置类型
         */
        $this->setType($type);

        /**
         * 判断是否存在索引
         */
        if (!$this->hasIndex()) {
            /**
             * 不存在索引则新建索引
             */
            $this->createIndex();
        }
    }

    /**
     * 日志记录样例
     * @throws \Exception
     */
    public function logDemo(){
        $data = [
            'name'       => 'ES log demo',
            'content'    => 'This demo shows how to make an info log',
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $this->info($data);
        $this->error($data, new \Exception('Error happened'));
    }

}