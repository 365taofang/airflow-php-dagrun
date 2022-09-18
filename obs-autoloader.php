<?php


$mapping = [
	'Airflow\AirflowClient' => __DIR__.'/Airflow/AirflowClient.php',
];


spl_autoload_register(function ($class) use ($mapping) {
    if (isset($mapping[$class])) {
        require $mapping[$class];
    }
}, true);
