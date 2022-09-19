<?php

namespace Airflow;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;

class AirflowClient
{
    const SDK_VERSION = '1.0.0';

    private $airflow_host;
    private $airflow_dag_id;
    private $request = array();

    /** @var Client */
    protected $httpClient;

    /**
     * @param $airflow_host string
     * @param $airflow_username string
     * @param $airflow_password string
     */
    public function __construct($airflow_host, $airflow_username, $airflow_password)
    {
        $this->airflow_host = $airflow_host;

        $this->httpClient = new Client(array(
            "auth" => array($airflow_username, $airflow_password),
        ));
    }

    /**
     * @param $dag_id string dag id
     * @param $url string 批量请求的url
     * @param $method string 批量请求的方法 get\post\put\delete\patch
     * @param $params_list array 批量请求的参数列表
     * @param $headers array 批量请求头
     * @return bool
     * @throws \Exception
     * @throws GuzzleException
     */
    public function triggerDagRun($dag_id, $url, $method, $params_list = array(), $headers = array())
    {
        $this->airflow_dag_id = $dag_id;

        $this->request = array(
            "conf" => array(
                "common_params" => array(
                    "method" => strtoupper($method),
                    "url" => $url,
                    "header" => $headers
                ),
                "request_params_list" => $params_list,
            )
        );

        $this->_validData();
        return $this->_triggerDagRun();
    }

    /**
     * @throws \Exception
     */
    private function _validData()
    {
        $validMethod = array('GET', 'POST', 'PUT', 'DELETE', 'PATCH');
        if (!in_array($this->request["conf"]["common_params"]["method"], $validMethod)) throw new \Exception("method参数不合法");
    }

    /**
     * @throws GuzzleException
     * @throws \Exception
     */
    private function _triggerDagRun()
    {
        $headers = array(
            "Content-type" => "application/json"
        );
        $url = "{$this->airflow_host}/dags/{$this->airflow_dag_id}/dagRuns";
        $request = new Request('POST', $url, $headers, json_encode($this->request));
        $response = $this->httpClient->send($request);
        $code = $response->getStatusCode();
        if ($code == 200) {
            return true;
        }
        throw new \Exception("请求失败，错误状态码{$code}");
    }

}