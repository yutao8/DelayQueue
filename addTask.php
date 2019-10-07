<?php
/**
 * author: yutao
 * createTime: 2018/9/5 下午2:09
 * description:
 */
include_once './vendor/autoload.php';
DelayQueue\Queue::getInstance()->addTask('\DelayQueue\Action','test',time() + 30,['name' => 'test'],true);