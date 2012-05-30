<?php
DEFINE('APP_PATH'       , './home'   );			//定义项目名称
DEFINE('TEMPLATE_STYLE' , 'default'  ); 		//定义首选模板目录         , 对应View目录中的首选模板
DEFINE('CACHE_LIFETIME' , 60	     );			//设置缓存更新时间
DEFINE('CACHING'        , 0          );			//设置缓存开启
$whihcDebug = array(		//开启DEBUG的主机
	'127.0.0.1',
	'10.207.16.168',
);
include './Reluctance/Reluctance.php';
