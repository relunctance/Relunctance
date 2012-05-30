<?php
/**
 * SessionToMem 保存Session到Memcache中
 * 
 * @Package :	111
 * @Version :	$ID$
 * @Copyright :	Copyright
 * @Author :	Gao qilin <qilin@leju.sina.com.cn> 
 * @License :	PHP Version 3.0 {@link http://www.php.net/license/3_0.txt}
 */
class SessionToMem
{
	//STATIC protected $savePath;
	//STATIC protected $sessionName;
	//保存时间
	STATIC protected $lifeTime;
	//Memcache缓存
	STATIC protected $m;
	//
	STATIC protected $time;

	/**
	 * start 
	 * 
	 * @Param $MemcacheObject 
	 * @Access public
	 * @Return void
	 */
	STATIC public function start($MemcacheObject)
	{

		//将 session.save_handler 设置为 user(自定义)，而不是默认的 files
		ini_set('session.save_handler', 'user');
		
		//不使用 GET/POST 变量方式,如果有需要请手动去掉注释
		//ini_set('session.use_trans_sid',    0);

		//设置垃圾回收最大生存时间,如果有需要请手动去掉注释
		//ini_set('session.gc_maxlifetime',  3600);

		 //使用 COOKIE 保存 SESSION ID 的方式,如果有需要请手动去掉注释
		//ini_set('session.use_cookies',      1);
		//ini_set('session.cookie_path',      '/');

		 //多主机共享保存 SESSION ID 的 COOKIE,如果有需要请手动去掉注释
		//ini_set('session.cookie_domain','.youDomain.com');
		if (!$MemcacheObject)
			MyException::Exception('请确认Memcache开启');
		self::$m 	= $MemcacheObject;
		self::$lifeTime = ini_get('session.gc_maxlifetime');
		self::$time 	= time();

		session_set_save_handler(
			array(__CLASS__, 'open'),
			array(__CLASS__, 'close'),
			array(__CLASS__, 'read'),
			array(__CLASS__, 'write'),
			array(__CLASS__, 'destroy'),
			array( __CLASS__, 'gc')
		);
		session_start();
		return TRUE;
	}


	/**
	 * open 
	 * 
	 * @Param $savePath 
	 * @Param $sessionName 
	 * @Access public
	 * @Return void
	 */
	STATIC public function open($savePath , $sessionName)
	{
		return TRUE;
	}

	/**
	 * close * 关闭
	 * 
	 * @Access public
	 * @Return void
	 */
	STATIC public function close()
	{
		return TRUE;
	}

	/**
	 * read * 从Memcache中读取数据
	 * 
	 * @Param $PHPSESSID 
	 * @Access public
	 * @Return void
	 */
	STATIC public function read($PHPSESSID)
	{
		$data = self::$m->get( self::sessionKeys($PHPSESSID) );
		return empty( $data ) ? '' : $data;
	}

	/**
	 * write * 写入数据到Memcache中
	 * 
	 * @Param $PHPSESSID 
	 * @Param $data 
	 * @Access public
	 * @Return void
	 */
	STATIC public function write($PHPSESSID , $data)
	{
		$Method = $data ? 'set' : 'replace';
		return self::$m->$Method( self::sessionKeys($PHPSESSID) , $data , MEMCACHE_COMPRESSED, self::$lifeTime);
	}

	/**
	 * destroy * 销毁Memcache中session
	 * 
	 * @Param $PHPSESSID 
	 * @Access public
	 * @Return void
	 */
	STATIC public function destroy($PHPSESSID)
	{
		return self::$m->delete(self::sessionKeys($PHPSESSID));
	}

	/**
	 * gc * 垃圾回收机制,Memcache有自己的回收机制
	 * 
	 * @Param $maxLifeTime 
	 * @Access public
	 * @Return void
	 */
	STATIC public function gc($maxLifeTime)
	{
		return TRUE;
	}


	/**
	 * sessionKeys 
	 * 
	 * @Param $PHPSESSID 
	 * @Access private
	 * @Return void
	 */
	STATIC private function sessionKeys($PHPSESSID)
	{
		return 'Reluc_' . $PHPSESSID;
	}

}
