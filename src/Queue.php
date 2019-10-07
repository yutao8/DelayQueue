<?php
/**
 * author: yutao
 * createTime: 2018/9/4 下午4:49
 * description:
 */
namespace DelayQueue;

class Queue{
	private        $error;
	private        $prefix    = 'delay_queue:';
	private        $queue;
	private static $_instance = null;

	private function __construct($queue){
		$this->queue = $queue;
	}

	final private function __clone(){
	}

	public static function getInstance($queue = ''){
		$queue or $queue = 'main';
		if(!self::$_instance){
			self::$_instance = new self($queue);
		}
		return self::$_instance;
	}

	/**
	 * 添加任务信息到队列
	 *
	 * @param string  $jobClass  类名
	 * @param  string $method    操作
	 * @param    int  $runAtTime 执行时间戳
	 * @param array   $args      参数
	 * @param  bool   $unique    是否唯一
	 *
	 * @return bool|int
	 */
	public function addTask($jobClass,$method,$runAtTime,$args = null,$unique = true){
		$key = $this->prefix . $this->queue;
		$params = [
			'class' => $jobClass,
			'method' => $method,
			'args' => $args,
		];
		if($unique){
			$result = RedisHandler::getInstance()->zRange($key,0,0);
			if($result){
				$params_cache = unserialize($result[0]);
				unset($params_cache['runtime']);
				unset($params_cache['uuid']);
				if($params_cache == $params){
					$this->error .= '任务已经存在!';
					return false;
				}
			}
		}
		$params['runtime'] = $runAtTime;
		$params['uuid'] = uniqid();
		return RedisHandler::getInstance()->zAdd(
			$key,
			$runAtTime,
			serialize($params)
		);
	}

	/**
	 * 执行job
	 * @return bool
	 */
	public function perform(){
		$key = $this->prefix . $this->queue;
		//取出有序集第一个元素
		$result = RedisHandler::getInstance()->zRange($key,0,0);
		if(!$result){
			return false;
		}
		$jobInfo = unserialize($result[0]);
		print_r('job_' . $jobInfo['uuid'] . ' : ' . $jobInfo['class'] . '->' . $jobInfo['method'] . '() will run at: ' . ($jobInfo['runtime'] - time()) . 's later' . PHP_EOL);
		$jobClass = $jobInfo['class'];
		$method = $jobInfo['method'];
		if(!@class_exists($jobClass)){
			print_r('class ' . $jobClass . ' undefined' . PHP_EOL);
		}
		if(!@method_exists($jobClass,$method)){
			print_r('method ' . $method . ' undefined' . PHP_EOL);
		}
		// 到时间执行
		$job = new $jobClass;
		if($jobInfo['args'] && method_exists($job,'setArgs')){
			$job->setArgs($jobInfo['args']);
		}
		if(time() >= $jobInfo['runtime']){
			$jobResult = $job->{$method}();
			print_r('job_' . $jobInfo['uuid'] . ' : ' . $jobInfo['class'] . '->' . $jobInfo['method'] . '() has run at: ' . date('Y-m-d H:i:s') . PHP_EOL);
			if($jobResult){// 将任务移除
				RedisHandler::getInstance()->zRem($key,$result[0]);
				return true;
			}
		}
		return false;
	}

	public function getError(){
		return $this->error;
	}

}