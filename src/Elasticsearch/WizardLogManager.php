<?php
namespace Wizard\Elasticsearch;

/**
 * wooduan Es 日志操作模型
 * @author wangxu <wxu@wooduan.com>
 */

class WizardLogManager extends WizardEsManager
{
    public function __construct(array $configs, int $retries = 3)
    {
        parent::__construct($configs, $retries);
    }

    /**
     * 调试日志
     * @param array $logData
     * @param \Exception|null $e
     * @return \stdClass
     * @throws \Exception
     */
    public function debug(array $logData=[], \Exception $e=null){
        if (count($logData) != count($logData,1)) {
            throw new \Exception('Log data should be one dimension array');
        }
        $logData['log_level'] = 'debug';
        if (isset($logData['created_at'])) {
            $logData['kibana_time'] = date('Y-m-d H:i:s', strtotime($logData['created_at'])-3600*8);
        } else {
			$logData['created_at']  = date('Y-m-d H:i:s');
            $logData['kibana_time'] = date('Y-m-d H:i:s', strtotime("-8 hours"));
        }
        if (!is_null($e)) {
            $logData['ex_code']  = $e->getCode();
            $logData['ex_msg']   = $e->getMessage();
            $logData['ex_trace'] = json_encode($e);
        }
        return $this->createDoc($logData);
    }

    /**
     * 普通日志
     * @param array $logData
     * @param \Exception|null $e
     * @return \stdClass
     * @throws \Exception
     */
    public function info(array $logData=[], \Exception $e=null){
        if (count($logData) != count($logData,1)) {
            throw new \Exception('Log data should be one dimension array');
        }
        $logData['log_level'] = 'info';
        if (isset($logData['created_at'])) {
            $logData['kibana_time'] = date('Y-m-d H:i:s', strtotime($logData['created_at'])-3600*8);
        } else {
			$logData['created_at']  = date('Y-m-d H:i:s');
            $logData['kibana_time'] = date('Y-m-d H:i:s', strtotime("-8 hours"));
        }
        if (!is_null($e)) {
            $logData['ex_code']  = $e->getCode();
            $logData['ex_msg']   = $e->getMessage();
            $logData['ex_trace'] = json_encode($e);
        }
        return $this->createDoc($logData);
    }

    /**
     * 警告日志
     * @param array $logData
     * @param \Exception|null $e
     * @return \stdClass
     * @throws \Exception
     */
    public function warning(array $logData=[], \Exception $e=null){
        if (count($logData) != count($logData,1)) {
            throw new \Exception('Log data should be one dimension array');
        }
        $logData['log_level'] = 'warning';
        if (isset($logData['created_at'])) {
            $logData['kibana_time'] = date('Y-m-d H:i:s', strtotime($logData['created_at'])-3600*8);
        } else {
			$logData['created_at']  = date('Y-m-d H:i:s');
            $logData['kibana_time'] = date('Y-m-d H:i:s', strtotime("-8 hours"));
        }
        if (!is_null($e)) {
            $logData['ex_code']  = $e->getCode();
            $logData['ex_msg']   = $e->getMessage();
            $logData['ex_trace'] = json_encode($e);
        }
        return $this->createDoc($logData);
    }

    /**
     * 错误日志
     * @param array $logData
     * @param \Exception|null $e
     * @return \stdClass
     * @throws \Exception
     */
    public function error(array $logData=[], \Exception $e=null){
        if (count($logData) != count($logData,1)) {
            throw new \Exception('Log data should be one dimension array');
        }
        $logData['log_level'] = 'error';
        if (isset($logData['created_at'])) {
            $logData['kibana_time'] = date('Y-m-d H:i:s', strtotime($logData['created_at'])-3600*8);
        } else {
			$logData['created_at']  = date('Y-m-d H:i:s');
            $logData['kibana_time'] = date('Y-m-d H:i:s', strtotime("-8 hours"));
        }
        if (!is_null($e)) {
            $logData['ex_code']  = $e->getCode();
            $logData['ex_msg']   = $e->getMessage();
            $logData['ex_trace'] = json_encode($e);
        }
        return $this->createDoc($logData);
    }
}