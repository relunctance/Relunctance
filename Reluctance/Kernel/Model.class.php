<?php
/**
 * Model *主从 *自适应切换 *读写分离 *连贯操作 *缓存字段
 * 
 * @Uses BasePdo
 * @Package :	111
 * @Version :	$ID$
 * @Copyright :	Copyright
 * @Author :	Gao qilin <qilin@leju.sina.com.cn> 
 * @License :	PHP Version 3.0 {@link http://www.php.net/license/3_0.txt}
 */
class  Model extends BasePdo
{
	//单例模式
	STATIC private $SingTon = NULL;
	STATIC private $userModel = array();

	protected function __construct() 
	{
	}

	/**
	 * getSingTon 单例进入函数
	 * 
	 * @Param 	string	 $tabName 
	 * @Param 	string	 $UserModel 
	 * @Access public
	 * @Return void
	 */
	STATIC public function getSingTon($tabName = '', $UserModel='')
	{
		self::construct($tabName);
		if (defined('MEMCACHE') && MEMCACHE)
			return self::mtiTheading($tabName , $UserModel);
		if(empty($UserModel))
			return empty(self::$SingTon) ? self::$SingTon = new Model() : self::$SingTon;
		else  
			return empty(self::$userModel[$tabName]) ?  self::$userModel[$tabName] = new $UserModel() : self::$userModel[$tabName];

	}



	/**
	 * mtiTheading 多线程单例,双重锁
	 * 
	 * @Access public
	 * @Return void
	 */
	STATIC public function mtiTheading($tabName , $UserModel)
	{
		if(empty($UserModel)){
			 if ( empty(self::$SingTon) ) self::$SingTon = new Model();
			 $singTon = serialize(self::$SingTon);
			 $model   = MyMemcache::get('model');
			 if (empty($model)) MyMemcache::set('model', $singTon);
			 return unserialize( MyMemcache::get('model') );
		}else  {
			if ( empty(self::$userModel[$tabName]) ) self::$userModel[$tabName] = new $UserModel();
			$singTon = serialize( self::$userModel[$tabName] );
			$model   = MyMemcache::get($tabName);
			if ( empty($model) ) MyMemcache::set($tabName, $singTon);
			return unserialize( MyMemcache::get($tabName) );
		}
	}

}
