<?php
/**
 * author: yutao
 * createTime: 2018/9/5 下午2:09
 * description:
 */
include_once './vendor/autoload.php';
$queue = 'main';//队列名,对应与redis的有序集key名
$sleep = 5;//延迟时间
while(true){
	\DelayQueue\Queue::getInstance($queue)->perform();
	sleep($sleep);
}

