<?php
/**
 * author: yutao
 * createTime: 2018/9/4 下午4:49
 * description:
 */
namespace DelayQueue;

class RedisHandler{
	private        $redis;
	private static $_instance = null;

	/**
	 * RedisHandler constructor.
	 *
	 * @param array $config
	 */
	private function __construct($config = []){
		try{
			$this->redis = new \Redis();
			$config['host'] = isset($config['host']) ? $config['host'] : '127.0.0.1';
			$config['port'] = isset($config['port']) ? $config['port'] : '6379';
			$this->redis->connect($config['host'],$config['port']);
		}catch(\Exception $e){
			print_r("The Redis connect failed : " . $e->getMessage() . PHP_EOL);
			exit();
		}
	}

	final private function __clone(){
	}

	/**
	 * @return RedisHandler|null
	 */
	public static function getInstance($config=[]){
		if(!self::$_instance){
			self::$_instance = new self($config);
		}
		return self::$_instance;
	}

	/**
	 * @param string $key   有序集key
	 * @param number $score 排序值
	 * @param string $value 格式化的数据
	 *
	 * @return int
	 */
	public function zAdd($key,$score,$value){
		return $this->redis->zAdd($key,$score,$value);
	}

	/**
	 * 获取有序集数据
	 *
	 * @param      $key
	 * @param      $start
	 * @param      $end
	 * @param null $withscores
	 *
	 * @return array
	 */
	public function zRange($key,$start,$end,$withscores = null){
		return $this->redis->zRange($key,$start,$end,$withscores);
	}

	/**
	 * 删除有序集数据
	 *
	 * @param $key
	 * @param $member
	 *
	 * @return int
	 */
	public function zRem($key,$member){
		return $this->redis->zRem($key,$member);
	}

}