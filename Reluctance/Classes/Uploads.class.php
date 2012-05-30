<?php
/**
 * 文件上传+缩放+水印类
 * @Author: Gaoqilin
 * @Mail  : 334136724@qq.com
 * @Time  : 2012-1-26 13:53:06
 */
class Uploads extends Water{
	//上传路径,和水印路径共用
	STATIC protected $filepath = './Public/Upload/';

	//上传后路径
	STATIC private $upAfterPath = array();

	//上传错误信息
	STATIC private $errorInfo = array();

	//允许大小
	STATIC private $allowsize = 2000000;


	//允许类型
	STATIC private $allowtype = array('image/jpg','image/jpeg','image/pjpeg','image/png','image/x-png','image/gif','image/wbmp');

	//允许后缀
	STATIC private $allowsuffix = array( 'gif', 'png', 'jpeg' , 'jpg', 'bmp');

	//文件前缀
	STATIC private $prefix = '';

	//文件后缀
	STATIC private $suffix;

	//是否启用随机文件名,和水印属性共用
	STATIC protected $randfix = 1;

	//文件原名
	STATIC private $name;

	//文件大小
	STATIC private $size;

	//文件类型
	STATIC private $type;

	//临时文件名
	STATIC private $tmp_name ;

	//加水印
	STATIC private $water = array();

	//缩放
	STATIC private $zoom = array();


	function __construct( )
	{
	}


	function up($inputFileName)
	{
		$flag = TRUE;
		if(!self::checkUpfilePath())
			$flag = FALSE;
		$name 	  = $_FILES[$inputFileName]['name'];
		$type 	  = $_FILES[$inputFileName]['type'];
		$tmp_name = $_FILES[$inputFileName]['tmp_name'];
		$error 	  = $_FILES[$inputFileName]['error'];
		$size     = $_FILES[$inputFileName]['size'];

		if(!is_array($name)){
			//单文件上传
			if( self::getFiles ($name , $type , $tmp_name , $error , $size )){
				if(self::checkSizes() && self::checkType() && self::checkSuffix()){
					       	$newPath = self::getAfterUpPath();		//上传文件的路径
						self::$upAfterPath['file'] = $newPath;
						if(!self::moveFile($newPath))
							$flag = FALSE;


						if(!empty(self::$water) && is_array(self::$water)){
							if($flag)
								self::$upAfterPath['water'] = self::waterMark(self::$filepath , $newPath , self::$water['logopath'], self::$water['position'] , self::$water['alpha'] , self::$water['prefix']);
						}

						if(isset(self::$zoom['width']) && isset(self::$zoom['height'])){
							if($flag)
								self::$upAfterPath['zoom'] = self::zoom( self::$filepath , $newPath , self::$zoom['width'] , self::$zoom['height'] , self::$zoom['prefix']);
						}
				} else  {
					$flag = FALSE;
				}
			} else  {
				$flag = FALSE;
			}
			return $flag ? self::$upAfterPath : self::$errorInfo ;
		} else  {
			//多文件上传
			$count = count($name);
			for ($i = 0; $i < $count; $i++){
				if( self::getFiles ($name[$i] , $type[$i] , $tmp_name[$i] , $error[$i] , $size[$i] )){
					if(self::checkSizes() && self::checkType() && self::checkSuffix()){
							$newPath = self::getAfterUpPath();
							self::$upAfterPath['file'][] = $newPath;
							if(!self::moveFile($newPath))
								$flag = FALSE;


							if(!empty(self::$water) && is_array(self::$water)){
								self::$upAfterPath['water'][] = self::waterMark(self::$filepath , $newPath , self::$water['logopath'], self::$water['position'] , self::$water['alpha'] , self::$water['prefix']);
							}

							if(isset(self::$zoom['width']) && isset(self::$zoom['height'])){
								self::$upAfterPath['zoom'][] = self::zoom( self::$filepath , $newPath , self::$zoom['width'] , self::$zoom['height'] , self::$zoom['prefix']);
							}
					} else  {
						$flag = FALSE;
					}
				} else  {
					$flag = FALSE;		//用不到了!
				}
			}
			return array_merge(self::$upAfterPath , self::$errorInfo);
		}


	}

	STATIC private function moveFile($newPath )
	{
		if(!is_uploaded_file( self::$tmp_name )){
			self::error(-6);
			return FALSE;
		}

		if(!move_uploaded_file(self::$tmp_name , $newPath)){
			self::error(-7);
			return FALSE;
		}
		return TRUE;
	}


	STATIC private function getAfterUpPath()
	{
		return self::$randfix ?  self::$filepath . self::$prefix . uniqid() . '.' . self::$suffix : self::$filepath . self::$prefix . self::$name;

	}

	STATIC private function checkSizes()
	{
		if( self::$size > self::$allowsize ){
			self::error(-5);
			return FALSE ;
		}
		return TRUE;
	}

	STATIC private function checkType()
	{
		if( !in_array(self::$type , self::$allowtype) ) {
			self::error(-4);
			return FALSE;
		}
		return TRUE;
	}

	STATIC private function checkSuffix()
	{
		if (!in_array(self::$suffix , self::$allowsuffix) ){
			self::error(-3);
			return FALSE;
		}
		return TRUE;

	}

	STATIC private function getFiles($name , $type , $tmp_name , $error , $size)
	{
		if($error){
			self::error($error);
			return FALSE;
		}
		self::$name 	= $name;
		self::$type 	= $type;
		self::$tmp_name = $tmp_name;
		self::$size 	= $size;
		$tmp 		= explode( '.', $name );
		self::$suffix 	= strtolower( array_pop( $tmp ) );
		return TRUE;
	}

	STATIC private function error($error) 
	{
		switch($error){
		case -1:
			self::addErrorInfo(-1,'上传目录' . self::$filepath . '不可写');
			break;
		case -2:
			self::addErrorInfo(-2,'创建目录' . self::$filepath . '失败');
			break;
		case -3:
			self::addErrorInfo(-3,'上传文件后缀不正确');
			break;
		case -4:
			self::addErrorInfo(-4,'上传的类型不正确!');
			break;
		case -5:
			self::addErrorInfo(-5,'上传文件超过了限定的大小!');
			break;
		case -6:
			self::addErrorInfo(-6,'不是上传的文件请确认');
			break;
		case -7:
			self::addErrorInfo(-7,'文件移动到新路径失败');
			break;
		case -8:
			self::addErrorInfo(-8,'水印图片路径不正确');
			break;
		case 1:
			self::addErrorInfo(1,'上传文件超过了PHP.INI中限定的大小');
			break;
		case 2:
			self::addErrorInfo(2,'上传文件超过了HTML表单中限定的大小');
			break;
		case 3:
			self::addErrorInfo(3,'文件只有部分被上传');
			break;
		case 4:
			self::addErrorInfo(4,'没有文件被上传');
			break;
		case 6:
			self::addErrorInfo(6,'找不到临时文件夹');
			break;
		case 7:
			self::addErrorInfo(7,'文件写入失败!');
			break;
		}
		return TRUE;
	}

	//第一个参数貌似用不到了!
	STATIC private function addErrorInfo($errorNum , $Info)
	{
		$name = empty(self::$name) ? '' : '文件名称:&nbsp;' . self::$name ;
		self::$errorInfo[] = '<span style="color:red;width:300px;">'.$Info.'</span><span style="text-align:left;color:red;">' . $name . '</span>';
	}


	STATIC private function checkUpfilePath()
	{
		self::$filepath = rtrim(self::$filepath , '/') . '/';
		if(!file_exists(self::$filepath)){
			if( !mkdir(self::$filepath , 0755 , TRUE)){
				self::error(-2);
				return FALSE;
			}
		}
		if(!is_writable(self::$filepath)){
			self::error(-1);
			return FALSE;
		} 
		return TRUE;
	}

	function set($type , $setArray)
	{
		switch($type) {
		case 'upload':
			$comPareArray = array(
				'filepath',		//上传路径(可选)
				'allowsize',		//允许大小(可选)
				'allowtype',		//允许类型(可选)
				'allowsuffix',		//允许后缀(可选)
				'randfix',		//是否启用随机文件名(可选)
				'prefix',		//上传文件前缀(可选)
			);
			if( Debug::compareArray($type, $setArray , $comPareArray)){
				foreach($setArray as $key => $value){
					if($key == 'allowtype'){
						if(!is_array($value))
							exit('请设置allowtype为数组!');
					}elseif($key == 'allowsuffix'){
						if(!is_array($value))
							exit('请设置allowsuffix为数组!');
					}
					self::$$key = $value;		//设置成员属性
				}
			}
			break;
		case 'water':
			$comPareArray = array(
				'logopath',		//水印图片路径(必须)
				'position',		//水印位置(可选)
				'alpha',		//透明度(可选)
				'prefix',		//前缀(可选)
			);
			if( Debug::compareArray($type, $setArray , $comPareArray)){
				if(!file_exists($setArray['logopath']))
					self::error(-8);
				$setArray['prefix'] 	= !isset($setArray['prefix'])   ?  'wa_' :  $setArray['prefix'];
				$setArray['position']	= !isset($setArray['position']) ?  '9'   :  $setArray['position'];
				$setArray['alpha']	= !isset($setArray['alpha'])    ?  100   :  $setArray['alpha'];
			}
				self::$water = $setArray;
			break;
		case 'zoom':
			$comPareArray = array(
				'width',		//缩放宽度(必须)
				'height',		//缩放高度(必须)
				'prefix',		//前缀(可选)
			);
			if( Debug::compareArray($type, $setArray , $comPareArray)){
				if(!isset($setArray['prefix'])){
					$setArray['prefix'] = 'zo_';
				}
			}
				self::$zoom = $setArray;
			break;
		}
		return $this;
	}
}
