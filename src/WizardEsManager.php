<?php
namespace Wizard\Elasticsearch;

/**
 * wooduan Es 操作模型
 * @author wangxu <wxu@wooduan.com>
 */

use CrCms\ElasticSearch\Grammar;
use Elasticsearch\ClientBuilder;
use CrCms\ElasticSearch\Builder;

class WizardEsManager
{
    protected $clientParams = ['ignore'=>[400,404],'timeout'=>60,'connect_timeout'=>10];
    protected $index = '';
    protected $type = '';
    protected $attributes = [];
    protected $esClient;
    protected $esBuilder;

    public function __construct(array $configs, $retries){
        $this->esClient = ClientBuilder::create()->setHosts($configs)->setRetries($retries)->build();
        $this->esBuilder = new Builder($configs, new Grammar(), $this->esClient);
    }

    public function getIndex(){
        return $this->index;
    }

    public function setIndex($index){
        $this->index = $index;
    }

    public function getType(){
        return $this->type;
    }

    public function setType($type){
        $this->type = $type;
    }

    public function setClientParams(array $params){
        $this->clientParams = $params;
    }

    public function getClientParams(){
        return $this->clientParams;
    }

    /**
     * 获取es文档操作builder
     * @return Builder
     * @example https://github.com/crcms/elasticsearch
     */
    public function getEsBuilder(){
        return $this->esBuilder->index($this->index)->type($this->type);
    }

    /*****************************************************文档操作****************************************************/

    /**
     * 创建文档
     * @param array $data
     * @param null $id
     * @return \stdClass
     * @example https://github.com/crcms/elasticsearch
     */
    public function createDoc(array $data=[], $id=null){
        $result = $this->getEsBuilder()->create($data, $id);
        return $result;
    }

    /**
     * 更新文档
     * @param $id
     * @param array $data
     * @return bool
     * @example https://github.com/crcms/elasticsearch
     */
    public function updateDoc($id, array $data=[]){
        $result = $this->getEsBuilder()->update($id, $data);
        return $result;
    }

    /**
     * 删除文档
     * @param $id
     * @return bool
     * @example https://github.com/crcms/elasticsearch
     */
    public function deleteDoc($id){
        $result = $this->getEsBuilder()->delete($id);
        return $result;
    }

    /**
     * 获取文档
     * @param $id
     * @return \stdClass|null
     * @example https://github.com/crcms/elasticsearch
     */
    public function getDoc($id){
        $result = $this->getEsBuilder()->whereTerm('_id', $id)->first();
        return $result;
    }

    /**
     * 索引文档
     * 不存在索引会新建
     * 文档id存在会更新
     * @param array $data
     * @param null $id
     * @param array $otherParams
     * @return array
     */
    public function index(array $data=[], $id=null, array $otherParams=[]){
        $params = [
            'index' => $this->index,
            'type' => $this->type,
            'body' => $data ? : $this->attributes,
            'client' => $this->client_params
        ];
        if ($id) {
            $params['id'] = $id;
        }
        if (array_key_exists('routing', $otherParams)) {
            $params['routing'] = array_get($otherParams, 'routing');
        }
        if (array_key_exists('timestamp', $otherParams)) {
            $params['timestamp'] = array_get($otherParams, 'timestamp');
        }
        return $this->esClient->index($params);
    }

    /**
     * 根据id获取文档
     * @param $id
     * @return mixed
     */
    public function get($id){
        $params = [
            'index' => $this->index,
            'type' => $this->type,
            'id' => $id,
            'client' => $this->client_params
        ];
        $result = $this->esClient->get($params);
        if (isset($result['found']) && $result['found']) {
            return $result;
        } else {
            return [];
        }
    }

    /**
     * 根据条件搜索文档
     * @param array $patterns
     * @return mixed
     */
    public function search(array $patterns){
        /**
        1. Match 查询
        'query' => [
        'match' => [
        'testField' => 'abc'
        ]
        ]
        2. Bool查询
        'query' => [
        'bool' => [
        'must' => [
        [ 'match' => [ 'testField' => 'abc' ] ],
        [ 'match' => [ 'testField2' => 'xyz' ] ],
        ]
        ]
        ]
        3. Bool 查询包含一个 filter 过滤器和一个普通查询
        'query' => [
        'bool' => [
        'filter' => [
        'term' => [ 'my_field' => 'abc' ]
        ],
        'should' => [
        'match' => [ 'my_other_field' => 'xyz' ]
        ]
        ]
        ]
         */

        $params = [
            'index' => $this->index,
            'type' => $this->type,
            'body' => [
                'query' => [
                    $patterns
                ]
            ],
            'client' => $this->client_params
        ];
        return $this->esClient->search($params);
    }

    /**
     * 更新文档
     * @param $id
     * @param array $newData
     * @return mixed
     */
    public function update($id, array $newData){
        $params = [
            'index' => $this->index,
            'type' => $this->type,
            'id' => $id,
            'body' => [
                'doc' => $newData
            ],
            'client' => $this->client_params
        ];
        return $this->esClient->update($params);
    }

    /**
     * 根据id删除文档
     * @param $id
     * @return mixed
     */
    public function delete($id){
        $params = [
            'index' => $this->index,
            'type' => $this->type,
            'id' => $id,
            'client' => $this->client_params
        ];
        return $this->esClient->delete($params);
    }

    /*****************************************************索引操作****************************************************/

    /**
     * 创建索引
     * @param array $settings
     * @param array $mappings
     * @return mixed
     */
    public function createIndex(array $settings=[], array $mappings=[]){
        /**
        索引body参数样例
        $params = [
        'index' => 'my_index',
        'body' => [
        'settings' => [
        'number_of_shards' => 3,
        'number_of_replicas' => 2
        ],
        'mappings' => [
        'my_type' => [
        '_source' => [
        'enabled' => true
        ],
        'properties' => [
        'first_name' => [
        'type' => 'string',
        'analyzer' => 'standard'
        ],
        'age' => [
        'type' => 'integer'
        ]
        ]
        ]
        ]
        ]
        ];
         */
        if (!$mappings) {
            $mappings = [
                $this->type => [
                    '_source'=>['enabled'=>true],
                    'dynamic'=>false,
                    'properties'=>[]
                ]
            ];

            foreach ($this->casts as $key => $value) {
                $mappings[$this->type]['properties'][$key] = ['type'=>$value];
                if ($value == 'date') {
                    $mappings[$this->type]['properties'][$key]['format'] = 'yyyy-MM-dd HH:mm:ss';
                }
            }
        }
        $params = [
            'index' => $this->index,
            'body' => [
//                'settings' => $settings,
                'mappings' => $mappings
            ],
            'client' => $this->client_params
        ];
        return $this->esClient->indices()->create($params);
    }

    /**
     * 更新索引设置
     * @param array $settings
     * @return array
     */
    public function updateIndexSettings(array $settings){
        /**
        索引body参数样例
        $params = [
        'index' => 'my_index',
        'body' => [
        'settings' => [
        'number_of_replicas' => 0,
        'refresh_interval' => -1
        ]
        ]
        ];
         * */
        $params = [
            'index' => $this->index,
            'body' => [
                'settings' => $settings,
            ]
        ];
        return $this->esClient->indices()->putSettings($params);
    }

    /**
     * 获取若干索引配置
     * @param array $indexNames
     * @return mixed
     */
    public function getIndexSettings(array $indexNames){
        $params = [
            'index' => $indexNames,
        ];
        return $this->esClient->indices()->getSettings($params);
    }

    /**
     * 获取若干索引映射
     * @param array $indexNames
     * @return mixed
     */
    public function getIndexMappings(array $indexNames){
        $params = [
            'index' => $indexNames,
        ];
        return $this->esClient->indices()->getMapping($params);
    }

    public function updateIndexMappings(array $mappings){
        /**
        索引body参数样例
        $params = [
        'index' => 'my_index',
        'type' => 'my_type2',
        'body' => [
        'my_type2' => [
        '_source' => [
        'enabled' => true
        ],
        'properties' => [
        'first_name' => [
        'type' => 'string',
        'analyzer' => 'standard'
        ],
        'age' => [
        'type' => 'integer'
        ]
        ]
        ]
        ]
        ];
         */
        $params = [
            'index' => $this->index,
            'body' => [
                'mappings' => $mappings
            ]
        ];
        return $this->esClient->indices()->putSettings($params);
    }

    /**
     * 销毁索引
     * @return mixed
     */
    public function deleteIndex(){
        $params = [
            'index' => $this->index,
            'client' => $this->client_params
        ];
        return $this->esClient->indices()->delete($params);
    }

    /**
     * 判断是否存在索引
     * @return bool
     */
    public function hasIndex(){
        $params = [
            'index' => $this->index,
            'client' => $this->client_params
        ];
        return $this->esClient->indices()->exists($params);
    }

    public function __set($field,$value){
        $method = 'set'.ucfirst(camel_case($field)).'Attribute';
        if (method_exists($this,$method)) {
            $this->$method($value);
        } else {
            $this->attributes[$field] = $value;
        }

    }

    public function __get($field){
        $method = 'get'.ucfirst(camel_case($field)).'Attribute';
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        if (isset($this->attributes[$field])) {
            return $this->attributes[$field];
        } else {
            throw new \Exception('attributes '.$field.' not found in '.$this->get_class());
        }
    }
}