# airflow-php-dagrun

用于投放后台的批量请求处理。
对于某一个网络资源，用不同的请求方法和参数多次访问

# 使用方式

## 创建请求client
```
use Airflow\AirflowClient;

//实例化客户端，注意配置env变量
$client = new AirflowClient();
```

## 组织需要批量运行的参数，触发dag
```

//设置公共参数
$client->setCommonQueryParams(array('access_token' => 'abcedfg')); # 可省略，公共url参数，会覆盖相同key的params批量请求参数
$client->setHeaders(array("Accept" => "*/*")); # 可省略
$client->setCookies(array("csrf_token" => '123456')); # 可省略

/**
 * 设置批量请求参数，一个数组元素表示一次请求的所有参数
 * 不同请求数据类型使用key如下：
 *      params：设置get请求携带参数，或者url参数，如果commonQueryParams有相同的key，将会被commonQueryParams设置的值覆盖
 *      data：设置post请求参数，如form-data等，不可和json参数同时设置
 *      json：设置json请求体内容，不可和data参数同时设置
        $requestParam = array(
            array('params' => array("p" => "01")),
            array('json' => array('{"j":"02"}')),
            array('data' => array("d" => "03")),
            //组合使用
            array('params' => array("p" => "01"), 'data' => array("d" => "03")),
            array('params' => array("p" => "01"), 'json' => array('{"j":"02"}')),
        );
 */
$params = array(
    array('params' => array("p" => "01", "c" => "03")),
    array('params' => array("p" => "02")),
);

//设置批量请求地址
$url = "https://www.baidu.com";
$method = "GET";
$timeout = 30; # 可省略，默认30秒

//发送批量请求。成功返回true，有异常则throw
$ret = $client->batchRequest($method, $url, $params, $timeout);
```
