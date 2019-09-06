<?php
namespace Wizard\Elasticsearch;

/**
 * wooduan Es 日志操作模型
 * @author wangxu <wxu@wooduan.com>
 */

use CrCms\ElasticSearch\Grammar;
use Elasticsearch\ClientBuilder;
use CrCms\ElasticSearch\Builder;

class WizardLogManager extends WizardEsManager
{
    public function __construct(array $configs, int $retries = 3)
    {
        parent::__construct($configs, $retries);
    }

    public function log(string $level='info', array $logData=[], \Exception $e=null){
        if (count($logData) != count($logData,1)) {
            throw new \Exception('Log data should be one dimension array');
        }
        $logData['log_level'] = $level;
        if (!is_null($e)) {
            $logData['exception_code']  = $e->getCode();
            $logData['exception_msg']   = $e->getMessage();
            $logData['exception_trace'] = json_encode($e);
        }
        return $this->createDoc($logData);
    }
}