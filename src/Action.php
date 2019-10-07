<?php
/**
 * author: yutao
 * createTime: 2018/9/5 下午2:36
 * description:
 */
namespace DelayQueue;

class Action{
	protected $args;

	public function setArgs($args = null){
		$this->args = $args;
	}

	function test(){
		print_r('test ok ' . var_export($this->args) . PHP_EOL);
		return true;
	}

	function test2(){
		print_r('test2 ok ' . var_export($this->args) . PHP_EOL);
		return true;
	}

}