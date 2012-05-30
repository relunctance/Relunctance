<?php
/*
 * 输出各种类型的数据，调试程序时打印数据使用。
 * 参数：可以是一个或多个任意变量或值
 */

function __autoload($className) {

	if ($className == 'Smarty') {
		include 'Smarty/Smarty.class.php';
	} else if ($className == 'Memcache') {
		return TRUE; 		

	} else if ($className == 'PHPExcel') {
		include $className . '.php';

	} else if ( strstr($className , '_')) {
		$classPathArr = explode('_',$className);
		include join('/',$classPathArr) . '.php';

	} else {
		include $className . '.class.php';

	}
	Debug::addmsg ( $className, 1 );
}

function p() {
	if (! DEBUG)
		return;
	$args = func_get_args (); //获取多个参数
	echo '<div style="width:100%;text-align:left"><pre>';
	//多个参数循环输出
	foreach ( $args as $arg ) {
		if (is_array ( $arg ) || is_object ( $arg )) {
			print_r ( $arg );
			echo '<br>';
		} else {
			var_dump ( $arg );
			echo '<br>';
		}
	}
	echo '</pre></div>';
}

/**
 * @desc 删除数组中某一元素
 * @param  unknown_type $arr 初始数组
 * @param  unknown_type $elem 要删除的元素
 * @return array
 */
function clearElem($arr, $elem) {
	if (is_array ( $arr )) {
		$key = array_search ( $elem, $arr );
		if ($key === false) {
			return $arr;
		} else {
			unset ( $arr [$key] );
			return clearElem ( $arr, $elem );
		}
	}
	return false;
}

/**
 * 截取指定长度的中文字符串
 *
 * @param string $string
 * @param  int $length
 * @param string $dot
 * @param string $charset
 * @return string
 */
function cutstr($string, $length, $dot = ' ...', $charset = 'utf-8') {
	if (strlen ( $string ) <= $length) {
		return $string;
	}
	$string = str_replace ( array ('&amp;', '&quot;', '&lt;', '&gt;' ), array ('&', '"', '<', '>' ), $string );
	$strcut = '';
	if (strtolower ( $charset ) == 'utf-8') {
		$n = $tn = $noc = 0;
		$mylength = strlen ( $string );
		while ( $n < $mylength ) {
			$t = ord ( $string [$n] );
			if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1;
				$n ++;
				$noc ++;
			} elseif (194 <= $t && $t <= 223) {
				$tn = 2;
				$n += 2;
				$noc += 2;
			} elseif (224 <= $t && $t < 239) {
				$tn = 3;
				$n += 3;
				$noc += 2;
			} elseif (240 <= $t && $t <= 247) {
				$tn = 4;
				$n += 4;
				$noc += 2;
			} elseif (248 <= $t && $t <= 251) {
				$tn = 5;
				$n += 5;
				$noc += 2;
			} elseif ($t == 252 || $t == 253) {
				$tn = 6;
				$n += 6;
				$noc += 2;
			} else {
				$n ++;
			}
			if ($noc >= $length) {
				break;
			}
		}
		if ($noc > $length) {
			$n -= $tn;
		}
		$strcut = substr ( $string, 0, $n );
	} else {
		for($i = 0; $i < $length; $i ++) {
			$strcut .= isset ( $string [$i] ) ? (ord ( $string [$i] ) > 127 ? $string [$i] . $string [++ $i] : $string [$i]) : '';
		}
	}
	
	return $strcut . $dot;
}

/**
 * 获取url返回值，curl方法
 */
function get_url($url, $timeout = 1) {
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
	$ret = curl_exec ( $ch );
	curl_close ( $ch );
	return $ret;
}

/**
 * 提交post请求，curl方法
 *
 * @param string $url         请求url地址
 * @param array  $post_fields 变量数组
 * @return string             请求结果
 */
function post_url($url, $post_fields, $timeout = 10) {
	$post_data = http_build_query ( $post_fields );
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt ( $ch, CURLOPT_POST, 1 );
	curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );
	$ret = curl_exec ( $ch );
	curl_close ( $ch );
	return $ret;
}


function gbk_to_utf8($arr) {
	if (empty ( $arr )) {
		return false;
	}
	if (is_array ( $arr )) {
		foreach ( $arr as $key => $value ) {
			$key = mb_convert_encoding ( $key, 'UTF-8', 'GBK' );
			$arr [$key] = gbk_to_utf8 ( $value );
		}
	} else {
		$arr = mb_convert_encoding ( $arr, 'UTF-8', 'GBK' );
	}
	return $arr;
}

function utf8_to_gbk($arr) {
	if (empty ( $arr )) {
		return false;
	}
	if (is_array ( $arr )) {
		foreach ( $arr as $key => $value ) {
			$keyN = mb_convert_encoding ( $key, 'GBK', 'UTF-8' );
			if ($keyN != $key)
				unset ( $arr [$key] );
			$arr [$keyN] = utf8_to_gbk ( $value );
		}
	} else {
		if (! empty ( $arr ))
			$arr = mb_convert_encoding ( $arr, 'GBK', 'UTF-8' );
	}
	return $arr;
}

/**
 * 查看已经定义的常量
 */
function Myconst($isArray = FALSE) {
	
	$const = get_defined_constants ( true );
	return $isArray ? p ( $const ['user'] ) : $const ['user'];
}

// xml编码
function xml_encode($data, $encoding = 'utf-8', $root = "think") {
	$xml = '<?xml version="1.0" encoding="' . $encoding . '"?' . '>';
	$xml .= '<' . $root . '>';
	$xml .= data_to_xml ( $data );
	$xml .= '</' . $root . '>';
	return $xml;
}

function data_to_xml($data) {
	if (is_object ( $data )) {
		$data = get_object_vars ( $data );
	}
	$xml = '';
	foreach ( $data as $key => $val ) {
		is_numeric ( $key ) && $key = "item id=\"$key\"";
		$xml .= "<$key>";
		$xml .= (is_array ( $val ) || is_object ( $val )) ? data_to_xml ( $val ) : $val;
		list ( $key, ) = explode ( ' ', $key );
		$xml .= "</$key>";
	}
	return $xml;
}

// 自动转换字符集 支持数组转换
function auto_charset($fContents, $from, $to) {
	$from = strtoupper ( $from ) == 'UTF8' ? 'utf-8' : $from;
	$to = strtoupper ( $to ) == 'UTF8' ? 'utf-8' : $to;
	if (strtoupper ( $from ) === strtoupper ( $to ) || empty ( $fContents ) || (is_scalar ( $fContents ) && ! is_string ( $fContents ))) {
		//如果编码相同或者非字符串标量则不转换
		return $fContents;
	}
	if (is_string ( $fContents )) {
		if (function_exists ( 'mb_convert_encoding' )) {
			return mb_convert_encoding ( $fContents, $to, $from );
		} elseif (function_exists ( 'iconv' )) {
			return iconv ( $from, $to, $fContents );
		} else {
			return $fContents;
		}
	} elseif (is_array ( $fContents )) {
		foreach ( $fContents as $key => $val ) {
			$_key = auto_charset ( $key, $from, $to );
			$fContents [$_key] = auto_charset ( $val, $from, $to );
			if ($key != $_key)
				unset ( $fContents [$key] );
		}
		return $fContents;
	} else {
		return $fContents;
	}
}

function D($tabName)
{
	$name = strtolower ( $tabName );
	$objName = ucfirst ( $name ) . 'Model';
	$app = ltrim ( rtrim ( APP_PATH, '/' ), './' );
	$actionModelPath = './' . $app . '/Model/ActionModel/' . $objName . '.class.php';
	$factoryModelPath = './' . $app . '/Model/FactoryModel/' . $objName . '.class.php';

	if (file_exists($actionModelPath))
		$obj = DModel($name , $objName , $actionModelPath);
	else if (file_exists($factoryModelPath))
		$obj = DModel($name , $objName , $factoryModelPath);
	else 
		$obj =  Model::getSingTon($name);		//Model层不存在文件,直接调用Model.class.php
	return $obj;
}


function DModel($name,$objName,$modelPath)
{
	$classString = file_get_contents($modelPath);
	if (stristr( $classString , 'extends Model'))		//继承了Model,说明是某表的Model
		eval("\$obj=$objName::getSingTon('$name','$objName');");
	else 
		$obj = new $objName;
	return $obj;
}


/**
function D($tabName)
{
	$name = strtolower ( $tabName );
	$objName = ucfirst ( $name ) . 'Model';
	$app = ltrim ( rtrim ( APP_PATH, '/' ), './' );
	$modelPath = './' . $app . '/Model/' . $objName . '.class.php';
	if (! file_exists ( $modelPath ))
		$obj =  Model::getSingTon($name);		//Model层不存在文件,直接调用Model.class.php
	else{
		$classString = file_get_contents($modelPath);
		if (stristr( $classString , 'extends Model'))		//继承了Model,说明是某表的Model
			eval("\$obj=$objName::getSingTon('$name','$objName');");
		else 
			$obj = new $objName;
	}
	return $obj;
}
 */


// 获取客户端IP地址
function get_client_ip() {
    static $ip = NULL;
    if ($ip !== NULL) return $ip;
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos =  array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip   =  trim($arr[0]);
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $ip = (false !== ip2long($ip)) ? $ip : '0.0.0.0';
    return $ip;
}
