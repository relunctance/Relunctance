<?php
class MyMemcache
{
	//Memcache对象
	STATIC public $m = NULL;
	STATIC public $prefix = NULL;

	/**
	 * getConn 
	 * 
	 * @Access public
	 * @Return void
	 */
	STATIC private function getConn ()
	{
		global $memServers;
		if( empty($memServers) )	 return FALSE;
		if (!extension_loaded('memcache')) MyException::Exception('请先安装PHP中Memcache模块');
		if (empty(self::$m))
		{
			$m = new Memcache();		//系统默认对象
			foreach($memServers as $key => $value) 
				$m->addServer( $key , $value );
			self::$m = $m;
		}
		if (empty(self::$prefix))
			self::$prefix = $_SERVER['SINASRV_MEMCACHED_KEY_PREFIX'] ? $_SERVER['SINASRV_MEMCACHED_KEY_PREFIX'] : 'Reluc-';
		return self::$m;
	}


	STATIC public function getMemcache()
	{
		return empty(self::$m) ? self::getConn() : self::$m;
	}



	STATIC public function set( $key , $value , $time = 0)		//0表示永久
	{
		return self::getMemcache()->set(self::$prefix . md5($key) , $value , MEMCACHE_COMPRESSED , $time );
	}

	STATIC public function get( $key )
	{
		return self::getMemcache()->get(self::$prefix . md5($key));
	}


	STATIC public function delete($key)
	{
		return self::getMemcache()->delete(self::$prefix . md5($key), 0);
	}

	STATIC public function flush()
	{
		return self::getMemcache()->flush();
	}



	/**
	 * ConnectError *通过这个来判断有没有开启Memcache同时得到Memcache对象,放到self::$m中,以后都可以调用
	 * 
	 * @Access public
	 * @Return void
	 */
	STATIC public function ConnectError()
	{
		 if( !$m = self::getConn() )	return FALSE;
		 $return = $m->getStats() ;
		 return empty($return) ? FALSE : TRUE;
	}


	/**
	 * addMemTab //添加,同一个表中所有sql语句
	 * 
	 * @Param $tabName 
	 * @Param $key 
	 * @Access public
	 * @Return void
	 */
	STATIC public function addMemTab($tabName , $key )
	{
		$m = self::getMemcache();
		$KEYS = $m ->get( $tabName );
		if( empty($KEYS) )
			$KEYS = array();
		if(!in_array($key , $KEYS))
		{
			$KEYS[] = $key;
			$m -> set($tabName , $KEYS , MEMCACHE_COMPRESSED , 0);
			return TRUE;		//成功添加
		} else  {
			return FALSE;		//已经存在
		}
	}



	/**
	 * addCache 
	 * 
	 * @Param $tabName 
	 * @Param $sql 
	 * @Param $data 
	 * @Access public
	 * @Return void
	 */
	STATIC public function addCache( $tabName , $sql , $data )
	{
		$key = md5( $sql );
		if(self::addMemTab( $tabName , $key))
			self::getMemcache()->set($key , $data , MEMCACHE_COMPRESSED , 0);
	}

	/**
	 * delAllTabCache //删除这个表中所有的缓存结果集
	 * 
	 * @Param $tabName 
	 * @Access public
	 * @Return void
	 */
	STATIC public function delAllTabCache($tabName)
	{
		$m = self::getMemcache();
		$KEYS = $m -> get($tabName);
		if(!empty($KEYS))
		{
			foreach($KEYS as $key) 
				$m -> delete( $key , 0);		//0表示立刻删除
		}
	}

	/**
	 * delOneTabCache //删除一条
	 * 
	 * @Param $sql 
	 * @Access public
	 * @Return void
	 */
	STATIC public function delOneTabCache($sql)
	{
		return self::getMemcache() -> delete( md5($sql) , 0);
	}



	/**
	 * getCache 
	 * 
	 * @Param $sql 
	 * @Access public
	 * @Return void
	 */
	STATIC public function getCache($sql)
	{
		$data =  self::getMemcache() -> get( md5($sql) );
		return empty($data) ? NULL : $data ;
	}




	/**
	 * __destruct 
	 * 
	 * @Access protected
	 * @Return void
	 */
	function __destruct()
	{
		self::$m->close();
	}

}
