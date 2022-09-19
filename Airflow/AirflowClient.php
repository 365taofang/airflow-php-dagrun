<?php

namespace Airflow;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;

class AirflowClient
{
    const SDK_VERSION = '1.0.0';
    const AIRFLOW_DAG_ID = 'advertising_platform.batch_requests';
    const DEFAULT_TIMEOUT = 30;

    private $airflow_url;
    private $request = array();

    //公共参数
    private $common_query_params = array();
    private $headers = array();
    private $cookies = array();


    /** @var Client */
    protected $httpClient;

    public function __construct()
    {
        $airflow_host = getenv('AIRFLOW_HOST');
        $airflow_username = getenv('AIRFLOW_USERNAME');
        $airflow_password = getenv('AIRFLOW_PASSWORD');

        $this->airflow_url = "{$airflow_host}/dags/" . self::AIRFLOW_DAG_ID . "/dagRuns";
        $this->httpClient = new Client(array(
            "auth" => array($airflow_username, $airflow_password),
        ));
    }

    /**
     * 设置公共请求参数，如携带token等
     * @param $common_params array 通用params参数
     * @return $this
     */
    public function setCommonQueryParams($common_query_params)
    {
        $this->common_query_params = $common_query_params;
        return $this;
    }

    /**
     * 设置批量请求头信息
     * @param $headers array 请求头
     * @return $this
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * 设置批量cookie
     * @param $cookies array cookie
     * @return $this
     */
    public function setCookies($cookies)
    {
        $this->cookies = $cookies;
        return $this;
    }

    /**
     * @param $url string 批量请求的url
     * @param $method string 批量请求的方法 get\post\put\delete\patch
     * @param $timeout int 超时时间
     * @return bool
     * @throws \Exception
     * @throws GuzzleException
     */
    public function batchRequest($method, $url, $params, $timeout = self::DEFAULT_TIMEOUT)
    {
        $this->request = array(
            "conf" => array(
                "timeout" => $timeout,
                "common_params" => array(
                    "method" => strtoupper($method),
                    "url" => $url,
                    "params" => $this->common_query_params,
                    "headers" => $this->headers,
                    "cookies" => $this->cookies
                ),
                "request_params_list" => $params
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
        if ($this->request["conf"]["timeout"] < 1) throw new \Exception('超时时间不得小于1秒');

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
        $request = new Request('POST', $this->airflow_url, $headers, json_encode($this->request));
        $response = $this->httpClient->send($request);
        $code = $response->getStatusCode();
        if ($code > 300) {
            throw new \Exception("请求失败，错误状态码{$code}");
        }
        return true;
    }

}