# airflow-php-dagrun

用于投放后台的批量请求处理。
对于某一个网络资源，用不同的请求方法和参数多次访问

# 使用方式

## 创建请求client
```
use Airflow\AirflowClient;

$airflow_host = "http://127.0.0.1:8080/api/v1";
$airflow_username = "airflow";
$airflow_password = "airflow";
$airflow_dag_id = "advertising_platform.batch_requests";

$af = new AirflowClient($airflow_host, $airflow_username, $airflow_password);
```

## 组织需要批量运行的参数，触发dag
```
# header、url、method这三个参数标记唯一网络资源及访问方式

$header = "Content-type:application/json"; # header可省略
$url = "https://www.baidu.com";

# 仅支持 'GET', 'POST', 'PUT', 'DELETE', 'PATCH'
$method = "get";

# 每次请求的不同参数使用二维数组。一个元素表示一次请求的参数列表
$params = array(
    array("name" => "liudehua", "age" => "55"),
    array("name" => "zhourunfa", "age" => "60"),
    array("name" => "liangjiahui", "age" => "65"),
);

# dag触发成功返回true，有异常则throw
$ret = $af->triggerDagRun($airflow_dag_id, $url, $method, $params, $header);
```
