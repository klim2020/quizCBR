<?php

namespace App\Models;

use \App\Contracts\ICacheHandler;
use Phpfastcache\CacheManager;
use Phpfastcache\Config\Config;
use Phpfastcache\Core\phpFastCache;



/**
 * Example user model
 *
 * PHP version 7.0
 */
class CacheHandler extends \Core\Model implements ICacheHandler
{

	private $pool;
	private $ttl;
	public function __construct($dir = 'cache', $TTL = 24 ){
		$this->ttl = $TTL;
		// Setup File Path on your config files
		CacheManager::setDefaultConfig(new Config([
			"path" => $dir,
			"itemDetailedDate" => false
		]));

		// In your class, function, you can call the Cache
		$this->pool = CacheManager::getInstance('files');

	}

    //Sets Cache Var
    public function setVariable($name, $var){
		$data = array('val'=>$var);

		$this->pool->save($this->pool->getItem($name)->set(serialize($data))->expiresAfter($this->ttl*60*60));
	}
	//Get Cache Var
    public function getVariable($name){
		$val = unserialize($this->pool->getItem($name)->get());

		return isset($val['val'])?$val['val']:false;

	}
	//Get TTL for a cache  var
	public function getTime($name){

	}

}
