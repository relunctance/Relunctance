<?php
/**
 * MySmarty * 重写Smarty部分方法
 * 
 * @Uses Smarty
 * @Package :	111
 * @Version :	$ID$
 * @Copyright :	Copyright
 * @Author :	Gao qilin <qilin@leju.sina.com.cn> 
 * @License :	PHP Version 3.0 {@link http://www.php.net/license/3_0.txt}
 */
class MySmarty extends Smarty
{

	function __construct()
	{
		$this->template_dir 	= rtrim(APP_PATH,'/') . '/' . 'View/' . rtrim(TEMPLATE_STYLE , '/') . '/';	//模板目录
		$this->compile_dir 	= RUNTIME_COMPLATES;					//组合目录
		$this->caching 		= CACHING;						//index.php中定义的是否开启缓存
		$this->cache_lefetime 	= CACHE_LIFETIME;					//缓存时间
		$this->cache_dir 	= RUNTIME_CACHE;					//缓存目录
		$this->left_delimiter	= LEFT_DELIMITER;					//左标签
		$this->right_delimiter 	= RIGHT_DELIMITER;					//右标签
		parent::__construct();
	}

	/**
	 * display *$cache_id是变化的这样,smarty才可以动态缓存,缓存的ID 用URL来做!因为每一个URL是不相同的!$_SERVER['REQUEST_URI'];
	 * 
	 * @Param $resource_name 
	 * @Param $cache_id 
	 * @Param $compile_id 
	 * @Access public
	 * @Return void
	 */
	function display($resource_name ='', $cache_id=NULL , $compile_id=NULL){
		$this->assign( 'root'	, rtrim($GLOBALS['root'], '/')	);
		$this->assign( 'app' 	, $GLOBALS['app']	);
		$this->assign( 'url' 	, $GLOBALS['url']	);
		$this->assign( 'res' 	, $GLOBALS['res']	);
		$this->assign( 'public'	, rtrim(MYPUBLIC ,  '/'));
		$this->assign( 'PUBLIC'	, $GLOBALS['public']	);
		$this->assign( 'CSS'	, rtrim( __CSS__ , '/'	)	);
		$this->assign( 'IMAGES'	, rtrim( __IMAGES__ ,'/')	);
		$this->assign( 'JS'	, rtrim( __JS__ , '/'	)	);
		$this->assign( 'UPLOAD'	, rtrim( __UPLOAD__ ,'/')	);
		$this->assign( 'css'	, rtrim( CSS 	, '/'	)	);
		$this->assign( 'js'	, rtrim( JS 	, '/'	)	);
		$this->assign( 'images'	, rtrim( IMAGES , '/'	)	);
		if(empty($resource_name)){
			$resource_name = $_GET['m'] . '/' . $_GET['a'] . '.' . TPL_PREFIX;//这个就规定了必须新建一个控制器目录里面放模板文件
		} else if(strstr($resource_name , '/')){				  //如果有斜线的话就找指定的文件
			$resource_name = $resource_name . '.' . TPL_PREFIX;		  //这里的意思是传了  比如index/update
		} else  {								  //这是只传方法名的情况,比如$this->display('display');
			$resource_name = $_GET['m'] . '/' . $resource_name . '.' .TPL_PREFIX;
		}
		if(!file_exists($this->template_dir . $resource_name))
			Debug::addmsg('[<font color="red">模板错误</font>]:<font color="red">' . $this->template_dir . $resource_name . '模板不存在</font>');
		parent::display($resource_name , $cache_id , $compile_id , TRUE);
	}

	/**
	 * is_cached * 手册中只有两个参数,但是Smarty代码中这个方法有三个参数,所以用Smarty源代码中的方法重写 * 是否有缓存,可以加在想要缓存的地方比如: * if($this->is_cached('index/update' , $_SERVER['REQUEST_URI'], )){ * }
	 * 
	 * @Param $tpl_file 
	 * @Param $cache_id 
	 * @Param $compile_id 
	 * @Access public
	 * @Return void
	 */
	function is_cached($tpl_file = '', $cache_id = NULL, $compile_id = NULL)
	{
		if( empty($tpl_file) ){
			$tpl_file = $_GET['m'] . '/' . $_GET['a'] . '.' . TPL_PREFIX;
		} else if(strstr($tpl_file , '/')){				  
			$tpl_file = $tpl_file . '.' . TPL_PREFIX;		  
		} else  {								  
			$tpl_file = $_GET['m'] . '/' . $tpl_file . '.' .TPL_PREFIX;
		}
		parent::is_cached($tpl_file , $cache_id , $compile_id );
	}



	/**
	 * clear_cache * 这个用在比如我们删除某文章帖子的时候同时把缓存也删了,不占用磁盘空间
	 * 
	 * @Param $tpl_file 
	 * @Param $cache_id 
	 * @Param $compile_id 
	 * @Param $exp_time 
	 * @Access public
	 * @Return void
	 */
	function clear_cache($tpl_file = NULL, $cache_id = NULL, $compile_id = NULL, $exp_time = NULL)
	{
		if(empty($tpl_file)){
			$tpl_file = $_GET['m'] . '/' . $_GET['a'] . '.' . TPL_PREFIX;
		} else if(strstr($tpl_file , '/')){				  
			$tpl_file = $tpl_file . '.' . TPL_PREFIX;		  
		} else  {								  
			$tpl_file = $_GET['m'] . '/' . $tpl_file . '.' .TPL_PREFIX;
		}
		parent::clear_cache($tpl_file, $cache_id, $compile_id, $exp_time);
	}


}
