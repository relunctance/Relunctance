<?php
/**
 * 图片缩放,水印类
 * @Author: Gaoqilin
 * @Mail  : 334136724@qq.com
 * @Time  : 2012-1-26 13:53:06
 */
class Water{
#成员属性
	#1,路径
	STATIC protected $path;

	#2,是否开启随机后缀名
	STATIC protected $randfix;

#成员方法
	//1,初始化成员属性(判断路径是否存在,如果不存在的话我们递归创建)
	public function __construct( $path='./Public/Upload/', $randfix=1){
		$path = rtrim($path,'/') . '/';
		if(is_writable($path)){
			if(!file_exists($path))
				mkdir($path,0755,TRUE);
		} else  {
			Debug::addmsg('<font color="red">' . $path . '不可写!</font>');
		}
		self::$path    = $path;
		self::$randfix = $randfix;
	}

	//2,water($backgound,$logo,$pos,$tm=100){}
	public function waterMark($path , $background, $logo, $pos = 0, $tm = 100, $prefix = 'wa_'){
		//检测路径
		if(!self::checkpath($background))
			Debug::addmsg('请确认图片路径正确!');
		if(!self::checkpath($logo))
			Debug::addmsg('请确认水印图片路径正确!');
		$backInfo	= self::getInfo($background);		//获得信息
		$logoInfo	= self::getInfo($logo);
		if(!self::checksize($backInfo,$logoInfo))
			Debug::addmsg('水印图片大小超过了长传图片大小!');
		$backres	= self::open_img($background,$backInfo['type']);		//得到资源
		$logores	= self::open_img($logo,$logoInfo['type']);
		$arr_xy		= self::getpos($pos,$backInfo,$logoInfo);		//得到位置的数组
		$newres		= self::outImg($backres,$logores,$arr_xy,$logoInfo,$tm);
		$newpath	= self::getnewpath($path,$prefix,$backInfo,self::$randfix);
		$newPathName    = self::saveImg($backInfo['type'],$newres,$newpath);
		imagedestroy( $newres );
		return $newPathName;
	}

	public function zoom( $path , $background,$width,$height,$prefix='zo_'){
		if(!self::checkpath($background))
			Debug::addmsg('缩放图片路径不正确!');
		$backInfo	= self::getInfo($background);
		$backres	= self::open_img($background,$backInfo['type']);
		$newsize	= self::getNewSize($width,$height,$backInfo);
		$newres		= self::kidOfImage($backres,$newsize,$backInfo);
		$newpath	= self::getnewpath($path,$prefix,$backInfo,self::$randfix);
		$newPathName    = self::saveImg($backInfo['type'],$newres,$newpath);
		imagedestroy( $newres );
		return $newPathName;
	}
	STATIC private function getnewpath($path,$prefix,$backInfo,$randfix){
		$arr = explode('.', $backInfo['name']) ;
		$hz  = array_pop( $arr );
		return $randfix ?  $path . $prefix . uniqid() . '.' . $hz : $path . $prefix . $backInfo['name'];
	}
	STATIC private function saveImg($type,$newres,$path){
		switch($type){
		case 'image/jpg'  :
		case 'image/jpeg' :
		case 'image/pjpeg':
			$res=imagejpeg($newres, $path);
			break;
		case 'image/png'  :
		case 'image/x-png':
			$res=imagepng($newres, $path);
			break;
		case 'image/gif'  :
			$res=imagegif($newres, $path);
			break;
		case 'image/wbmp' :
			$res=imagewbmp($newres, $path);
			break;
		}
		return $path;
	}

	STATIC private function outImg($backres,$logores,$arr_xy,$logoInfo,$tm){
		imagecopymerge($backres,$logores,$arr_xy['x'],$arr_xy['y'],0,0,$logoInfo['width'],$logoInfo['height'],$tm);
		return $backres;
	}

	STATIC private function getpos($pos,$backInfo,$logoInfo){
		switch($pos){
		case 1:
			$x=0;
			$y=0;
			break;
		case 2:
			$x=floor(($backInfo['width']-$logoInfo['width'])/2);
			$y=0;
			break;
		case 3:
			$x=$backInfo['width']-$logoInfo['width'];
			$y=0;
			break;
		case 4:
			$x=0;
			$y=floor(($backInfo['height']-$logoInfo['height'])/2);
			break;
		case 5:
			$x=floor(($backInfo['width']-$logoInfo['width'])/2);
			$y=floor(($backInfo['height']-$logoInfo['height'])/2);
			break;
		case 6:
			$x=$backInfo['width']-$logoInfo['width'];
			$y=floor(($backInfo['height']-$logoInfo['height'])/2);
			break;
		case 7:
			$x=0;
			$y=$backInfo['height']-$logoInfo['height'];
			break;
		case 8:
			$x=floor(($backInfo['width']-$logoInfo['width'])/2);
			$y=$backInfo['height']-$logoInfo['height'];
			break;
		case 9:
			$x=$backInfo['width']-$logoInfo['width'];
			$y=$backInfo['height']-$logoInfo['height'];
			break;
		case 0:
			$x=mt_rand(0,$backInfo['width']-$logoInfo['width']);
			$y=mt_rand(0,$backInfo['height']-$logoInfo['height']);
			break;
		}
		return array( 'x'=>$x, 'y'=>$y);
	}

	STATIC private function checkpath($path) 
	{
		return !file_exists($path) ? FALSE : TRUE;
	}
	STATIC private function getInfo($res){
		$data=getimagesize($res);
		$info['width']  = $data[0];
		$info['height'] = $data[1];
		$info['type']	=$data['mime'];
		$info['name']	=basename($res);
		return $info;
	}
	STATIC private function checksize($backInfo,$logoInfo){
		if($logoInfo['width'] >= $backInfo['width'] || $logoInfo['height'] >= $backInfo['height'])
			return false;
		else
			return true;
	}

	STATIC private function open_img($path,$type){
		switch($type){
		case 'image/jpg'  :
		case 'image/jpeg' :
		case 'image/pjpeg':
			$res = imagecreatefromjpeg($path);
			break;
		case 'image/png'  :
		case 'image/x-png':
			$res = imagecreatefrompng($path);
			break;
		case 'image/gif'  :
			$res = imagecreatefromgif($path);
			break;
		case 'image/wbmp' :
			$res = imagecreatefromwbmp($path);
			break;
		}
		return $res;
	}
	//缩放,返回值是新大小
	STATIC private function getNewSize($width, $height,$imgInfo){	
			$size["width"]  = $imgInfo["width"];          //将原图片的宽度给数组中的$size["width"]
			$size["height"] = $imgInfo["height"];        //将原图片的高度给数组中的$size["height"]
			
			if($width < $imgInfo["width"])
				$size["width"]  = $width;             //缩放的宽度如果比原图小才重新设置宽度

			if($width < $imgInfo["height"])
				$size["height"] = $height;            //缩放的高度如果比原图小才重新设置高度

			if($imgInfo["width"]*$size["width"] > $imgInfo["height"] * $size["height"])
				$size["height"] = round($imgInfo["height"]*$size["width"]/$imgInfo["width"]);
			else
				$size["width"]  = round($imgInfo["width"]*$size["height"]/$imgInfo["height"]);

			return $size;
	}

	//解决gif黑色背景问题,返回值新图片资源
	STATIC private function kidOfImage($srcImg,$size, $imgInfo){
			$newImg = imagecreatetruecolor($size["width"], $size["height"]);		
			$otsc   = imagecolortransparent($srcImg);
			if( $otsc >= 0 && $otsc < imagecolorstotal($srcImg)) {
		  		 $transparentcolor = imagecolorsforindex( $srcImg, $otsc );
				 $newtransparentcolor = imagecolorallocate(
			   		 $newImg,
			  		 $transparentcolor['red'],
			   	         $transparentcolor['green'],
			   		 $transparentcolor['blue']
				 );

		  		 imagefill( $newImg, 0, 0, $newtransparentcolor );
		  		 imagecolortransparent( $newImg, $newtransparentcolor );
			}

		
			imagecopyresized( $newImg, $srcImg, 0, 0, 0, 0, $size["width"], $size["height"], $imgInfo["width"], $imgInfo["height"] );
			imagedestroy($srcImg);
			return $newImg;
		}
}
