<?php
header('Content-Type:text/html;charset=utf-8');
date_default_timezone_set('PRC');

$GLOBALS['root'] = rtrim( dirname($_SERVER['SCRIPT_NAME']) , DIRECTORY_SEPARATOR ) .'/' ;
$GLOBALS['debug'] = 1;


DEFINE('WEB', str_replace( DIRECTORY_SEPARATOR , '/' ,substr(dirname(__FILE__),0,-10) ));

DEFINE('RELUC_DIR'	, './Reluctance/'			);

DEFINE('__ROOT__'	, './'					);	

DEFINE('__PUBLIC__'	,  __ROOT__ . 'Public/'			);	

DEFINE('__JS__'		, $GLOBALS['root'] .'Public/Js/'	);	

DEFINE('__IMAGES__'	, $GLOBALS['root'] .'Public/Images/' 	);

DEFINE('__UPLOAD__'	, $GLOBALS['root'] .'Public/Upload/'	);

DEFINE('__CSS__'	, $GLOBALS['root'] .'Public/Css/'	);	

DEFINE( 'RUNTIME' 		, './RUNTIME' 			);
DEFINE( 'RUNTIME_COMPLATES' 	, RUNTIME  . '/Complates/'	);
DEFINE( 'RUNTIME_CACHE' , 	  RUNTIME  . '/CACHE/'		);
DEFINE( 'RUNTIME_DBFIELDCACHE' 	, RUNTIME  . '/DbFieldCache/'	);

$Application = rtrim(ltrim(APP_PATH,'./'),'/') . '/' ;
$APP 	     = $GLOBALS['root'] .$Application;
$RES 	     = trim(TEMPLATE_STYLE ,'/') . '/' . 'RESOURCE/';
DEFINE('MYPUBLIC'		,	$APP . 'View/'. trim(TEMPLATE_STYLE , '/') . '/' . 'PUBLIC/');
DEFINE( 'APP'  		, 	$APP 				);
DEFINE( 'RES'		, 	$APP . 'View/'. $RES		);
DEFINE( 'JS'            ,  	$APP . 'View/'. $RES . 'js/' 	);
DEFINE( 'CSS'           ,  	$APP . 'View/'. $RES . 'css/' 	);
DEFINE( 'IMAGES'        ,  	$APP . 'View/'. $RES . 'images/');
include RELUC_DIR . 'Common/Function.php';

$IncludePath  = get_include_path();
$IncludePath .= PATH_SEPARATOR . RELUC_DIR . 'Libs/';				//扩展目录,存放Smarty
$IncludePath .= PATH_SEPARATOR . RELUC_DIR . 'Classes/';			//分页类,验证码类
$IncludePath .= PATH_SEPARATOR . RELUC_DIR . 'Common/';				//
$IncludePath .= PATH_SEPARATOR . RELUC_DIR . 'Kernel/Dbdrive';			//核心文件类目录
$IncludePath .= PATH_SEPARATOR . RELUC_DIR . 'Kernel/';				//核心文件类目录
$IncludePath .= PATH_SEPARATOR . './Public/Classes/';				//用户自定义扩展类
$IncludePath .= PATH_SEPARATOR . './' . $Application . 'Controll/';		//用户自定义扩展类
//$IncludePath .= PATH_SEPARATOR . './' . $Application . 'Model/';	//控制器Model层
$IncludePath .= PATH_SEPARATOR . './' . $Application . 'Model/ActionModel/';	//控制器Model层
$IncludePath .= PATH_SEPARATOR . './' . $Application . 'Model/FactoryModel/';	//工厂Model层
set_include_path($IncludePath);

$debug = in_array( $_SERVER['REMOTE_ADDR'], $whihcDebug ) ? TRUE : FALSE;
DEFINE( 'DEBUG' , $debug );

	if( defined('DEBUG') && DEBUG === TRUE ) {
		error_reporting(E_ALL);
		ini_set('display_errors' , 1);
		Debug::scriptStart();
		set_error_handler(array('Debug', 'CatchDebug')); //设置捕获系统异常
	} else {
		ini_set('display_errors', 0);
	}

if(!file_exists('./Public/Config.inc.php') || !file_exists(RUNTIME) || !file_exists(RUNTIME_COMPLATES) || !file_exists(RUNTIME_CACHE) || !file_exists(RUNTIME_DBFIELDCACHE))
	Struct::init() ;
include_once './Public/Config.inc.php';
include './Public/Function/Function.php';


if(defined('MEMCACHE') && MEMCACHE) 			//启用memcache缓存
{
	if(extension_loaded('memcache')) 		//判断memcache控制是否安装
	{
		if(! MyMemcache::ConnectError() ) 	//判断Memcache服务器是否有异常
		{
			Debug::addmsg('<font color="red">连接memcache服务器失败,请确认IP或端口正确!</font>');
		} else {
			Debug::addmsg('开启Memcache');
		}
	} else {
		Debug::addmsg('<font color="red">PHP没有安装memcache扩展模块,请先安装!</font>');
	}	
} else {
	Debug::addmsg('<span style="color:red;">[未使用Memcache]</span>');
}

if( IS_SESSION_TO_MEMCACHE && MEMCACHE )
{
	SessionToMem::start( MyMemcache::getMemcache() );
	Debug::addmsg('开启Session==>Memcache');
} else {
	session_start();
}
Debug::addmsg( 'SessionID: ' . session_id() );

if(empty($_SESSION['configMtime']))
	$_SESSION['configMtime'] = filemtime('./Public/Config.inc.php');

if(filemtime('./Public/Config.inc.php') > $_SESSION['configMtime'])
{
	Struct::init() ;		//当修改配置文件,同样执行一次框架结构类
					//自动根据数据库驱动切换继承关系
	$_SESSION['configMtime'] = filemtime('./Public/Config.inc.php');
	$String 	= file_get_contents(RELUC_DIR . 'Kernel/Model.class.php');
	$replaceMent 	= 'extends Base' . ucfirst(strtolower( DB_DRIVER )) . "\n";
	$String 	= preg_replace('/extends\s+\w+\s+/', $replaceMent, $String );
	file_put_contents(RELUC_DIR . 'Kernel/Model.class.php' , $String);
}

$_GET['m'] = !isset($_GET['m']) ? 'index' : strtolower($_GET['m']);
$_GET['a'] = !isset($_GET['a']) ? 'index' : strtolower($_GET['a']);
if (array_key_exists('PATH_INFO', $_SERVER)){
	$pathinfo = trim($_SERVER['PATH_INFO'] , '/');
	$PATHINFO = explode( '/' ,  $pathinfo);
	$_GET['m'] = empty($PATHINFO[0]) ? 'index' : strtolower($PATHINFO[0]);
	$_GET['a'] = empty($PATHINFO[1]) ? 'index' : $PATHINFO[1];
	$PATHINFO = array_slice($PATHINFO , 2) ;
	if( !empty($PATHINFO) ){
		$count = count($PATHINFO);
		for( $i=0; $i<$count; $i+=2 ){
			$_GET[ $PATHINFO[$i] ] = $PATHINFO[$i+1];
		}
	}
	$_GET = array_diff( $_GET , array('') );
}
$GLOBALS['app']    = $_SERVER['SCRIPT_NAME'] ;					//应用所在的目录
$GLOBALS['url']    = $GLOBALS['app'] . '/' . $_GET['m'] ;			//定位控制器,后面直接写方法就可以了
$GLOBALS['res']    = rtrim(RES , '/');
$GLOBALS['public'] = $GLOBALS['root'] . 'Public' ;



$M = !isset($_GET['m']) ? 'IndexAction' : ucfirst(strtolower($_GET['m'])) . 'Action';

if(file_exists( __ROOT__ . $Application .'Controll/'. $M . '.class.php')){
	$CLASS = new $M();
	$CLASS->run();
} else  {
	//header('location:'.$_SERVER['SCRIPT_NAME'].'/index/index'); 	//访问的方法不存在跳回首页,这里你可以定制自己的404页面
}


//p($GLOBALS);
//myconst(true);
//p($_SERVER);
if( DEBUG  && $GLOBALS['debug']  ) {		//这里加上$GLOBALS['debug'];如果$GLOBALS['debug']为false,或者0就不执行 
	Debug::scriptEnd();
	Debug::debugPrint();
}
?>
