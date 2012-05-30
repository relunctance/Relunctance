<?php
/**
 * 多功能验证码组合
 * 注1:如果启用随机上下左右形状,又不显示在验证码上,需用Ajax来请求调用验证码顺序$_SESSION['codesort']
 * 注2:中文验证码必须注意字体路径 
 * @Author: Gaoqilin
 * @Mail  : 334136724@qq.com
 * @Time  : 2012-1-25 15:08:02
 */
class Verify 
{
	//验证码类型,1为纯数字,2为纯字母,3为数字+字母,4为中文验证码(需要字体文件.ttf支持)
	STATIC private $codeType;

	//验证码字母数量
	STATIC private $codeNum;

	//保存验证码字符
	STATIC public $codeStringArray = array();

	//验证码宽度
	STATIC private $width;

	//验证码高度
	STATIC private $height;

	//图片资源
	STATIC private $img;

	//验证码形状类型,1为水平矩形 , 2为随机上右下左类型
	STATIC private $shapeType;

	//输出图片类型格式,PNG,JPEG,BMP,GIF
	STATIC private $imgOutType;

	//是否在$shapeType为2的时候,字体在验证码上显示,如果显示为TRUE,如果不显示为FALSE(注意,不显示的情况下需要用Ajax来重新调用一次)
	STATIC private $fontTextShow;

	//
	STATIC public $orderArray = array();

	STATIC private $fontType;


	public function __construct($width = 60, $height = 60,$codeNum=4, $codeType = 3, $shapeType=1, $fontTextShow=FALSE, $fontType='./Reluctance/Classes/Fonts/heiti.ttf', $imgOutType = 'gif')
	{
		self::$width 		= $width;
		self::$height 		= $height;
		self::$codeNum 		= $codeNum;
		self::$codeType 	= $codeType;
		self::$shapeType	= $shapeType;
		self::$imgOutType 	= $imgOutType;
		self::$fontTextShow	= $fontTextShow;
		if(!file_exists($fontType)){
			$GLOBALS['debug'] = 1;
			Debug::addmsg('<font color="red">验证码字体路径不存在</font>');
		} else {
			self::$fontType	= $fontType;
		}
		self::$img 		= imagecreatetruecolor( $width , $height);
		self::$codeStringArray 	= self::getCodeString($codeType);
	}
	//创建随机背景颜色
	STATIC private function autoBgColor()
	{
		return imagecolorallocate(self::$img, mt_rand(250,255), mt_rand(250,255) , mt_rand(250,255));
		//return imagecolorallocate(self::$img, mt_rand(130,255), mt_rand(130,255) , mt_rand(130,255));
	}

	//创建随机字体颜色
	STATIC private function autoFontColor( $flag = FALSE )
	{
		$number = $flag ? 100 : 50;
		return imagecolorallocate(self::$img, mt_rand(0,$number), mt_rand(0,$number) , mt_rand(0,$number));
	}

	//创建随机矩形背景
	STATIC private function createTangle( $img , $width, $height , $autoBgColor)
	{
		return imagefilledrectangle( $img, 0, 0, $width, $height, $autoBgColor);
	}


	STATIC private function getCodeString($type)
	{
		$returnArray = array();
		switch($type){
		case 1:		//纯数字
			$str = '123456789';
			$returnArray = array_slice( str_split( str_shuffle( $str )), 0, self::$codeNum);
			break;
		case 2:		//纯字母
			$str = 'abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ';
			$returnArray = array_slice( str_split( str_shuffle( $str )), 0, self::$codeNum);
			break;
		case 3:		//数字加字母
			$str = '123456789abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ';
			$returnArray = array_slice( str_split( str_shuffle( $str )), 0, self::$codeNum);
			break;
		case 4:		//中文验证码
			$str = '女儿快两岁时对墙上的插座孔很感兴趣于是我拿来了她的录音机告诉她墙上的孔里有电是录音机需要的插插头的时候需要拿着后面的塑料部分这才安全如果拿了前面的两片金属是会死的而且手湿的时候插插座也是会死的女儿对死还是很敬畏的虽然未必明白具体会怎样但总之是一件很不好很可怕的事在两岁不到的日子里她学会了插插座从那天起我们经常叫她来帮我插一下这样那样的插座她也经常拖着她的录音机这个房间听听再拖到那个房间听听不用求我们帮她了好吧她很安全地活到了现在从没触过电我也没再担心过她会触电我甚至曾告诉她如果看到一根金属线你想知道是否带电可以用手背靠上去试一下有电的话会打到你会有点痛有点麻但记住千万不能用手心去抓手背碰到会弹开没危险手心碰到会抓紧那就是要命了';
			$returnArray = array_rand( array_flip( str_split($str, 3)) ,  self::$codeNum);
			break;
		}
		return $returnArray;
	}

	STATIC private function writeText()
	{
		if(self::$shapeType == 2){
			//这里是上右下左类型
			$order=array('上', '右', '下', '左');
			//随机打乱数组,键不变
			shuffle($order);		
			$n = floor(self::$width/4);
			$i=0;
			foreach($order as $key => $fontOrder)
			{
				switch($fontOrder) {
				case '上':
					$x = $n+10;
					$y =  0;
					break;
				case '左':
					$x = 10;
					$y = $n;
					break;
				case '右':
					$x = 2*$n+10;
					$y = $n;
					break;
				case '下':
					$x = 1*$n+10;
					$y = 2*$n-5;
					break;
				}
				self::$orderArray[$fontOrder] = self::$codeStringArray[$i];
				if(self::$fontTextShow) {
					$offset = self::$codeType != 4 ?  5 :  -10 ;
					if(self::$shapeType ==2 && self::$codeType =4 )
						$offset = 10;
						imagettftext(self::$img,10,mt_rand(-10,30),($n*$i),($n*3+$offset),self::autoFontColor(),self::$fontType,$fontOrder);
				} 
				if(self::$shapeType ==2 && self::$codeType =4 )
						$y = $y + 15;
				self::chineseOrEng( self::$codeType, $x, $y, $i );
				$i++;
			}
		} else if(self::$shapeType == 1) {
			//这里是常规验证码类型
			for($i=0; $i<self::$codeNum; $i++) {
				if(self::$codeType != 4){
					$x = floor(self::$width/self::$codeNum)*$i + 3;
					$y = mt_rand(0,self::$height-20);
				} else  {
					$x = floor(self::$width/self::$codeNum)*$i ;
					$y = mt_rand(20,self::$height-10);
				}
				self::chineseOrEng( self::$codeType, $x, $y, $i );
			}
			self::dian();
		}
	}


	STATIC private function dian()
	{
		for($i=0; $i<50; $i++){
			imagesetpixel(self::$img,mt_rand(0,self::$width),mt_rand(0,self::$height),self::autoFontColor(TRUE));
		}
	}


	STATIC private function chineseOrEng($codeType, $x, $y, $i){
		if($codeType == 4){ 		//中文验证码
			imagettftext(self::$img,12,0,$x,$y,self::autoFontColor(),self::$fontType,self::$codeStringArray[$i]);
		} else  { 			//常规验证码
			imagechar(self::$img,12, $x, $y, self::$codeStringArray[$i], self::autoFontColor());
		}
	}


	STATIC private function sentHeader(){
		$func	     = 'image' . self::$imgOutType;
		$headContent = 'Content-type:'.self::$imgOutType;
		header($headContent);
		$func(self::$img);
	}

	public function img(){
		self::createTangle(self::$img , self::$width , self::$height , self::autoBgColor() );
		self::writeText();
		self::sentHeader();
		$_SESSION['code'] = strtolower(implode( '' , self::$codeStringArray));
		if( !empty( self::$orderArray ) && self::$shapeType ==2)
			$_SESSION['codesort'] = strtolower(implode('', array_keys(self::$orderArray)));
		 else  
			if(isset($_SESSION['codesort']) ) unset($_SESSION['codesort']) ;
	}


	function __get($param)
	{
		if($param == 'code'){
			return implode( '' , self::$codeStringArray);
		} else if ($param == 'codesort') {
			return implode( '' , array_keys(self::$orderArray));
		} else  {
			return self::$$param;
		}
	}


	function __toString()
	{
		self::createTangle(self::$img , self::$width , self::$height , self::autoBgColor() );
		self::writeText();
		self::sentHeader();
		$_SESSION['code'] = strtolower(implode( '' , self::$codeStringArray));
		if( !empty( self::$orderArray ) && self::$shapeType ==2)
			$_SESSION['codesort'] = strtolower(implode('', array_keys(self::$orderArray)));
		else  
			if (isset($_SESSION['codesort']) ) unset($_SESSION['codesort']) ;
	}


	function __destruct()
	{
		imagedestroy(self::$img);
	}

}
/**
 * 使用参数顺序:
 * @param 	$width 	  	Int 		宽度
 * @param 	$height	 	Int 		高度
 * @param 	$codeNum 	Int 		字符数量
 * @param 	$codeType 	Int 		验证码类型		(1纯数字 , 2纯字母 , 3数字+字母 , 4中文)
 * @param 	$shapeTpe 	Int 		验证码形状类型		(1普通类型, 2上下左右类型)
 * @param 	$fontTextShow 	Bool 		是否验证码中显示上下左右(TRUE显示 , FALSE不显示)
 * @param 	$fontType	String 		中文时字体路径		(注:中文验证码时请确认路径正确)
 * @param 	$imgOutType 	int 		输出类型		PNG,JPEG,WBMP,GIF
 *
 *使用方法有两种:
	 1, 
		echo new Verify(60 , 25, 4 ,1 ,1 ,FALSE 'heiti.ttf','png');
	 2, 
		$img = new Verify(60 , 25, 4 ,1 ,1 ,FALSE 'heiti.ttf','png');
		$img ->img();
 */
//echo new Verify(60 , 25 , 4 , 1 , 1 , FALSE);				//纯数字
//echo new Verify(60 , 25 , 4 , 2 , 1 , FALSE);				//纯字母
//echo new Verify(60 , 25 , 4 , 3 , 1 , FALSE);				//数字+字母
//echo new Verify(60 , 25 , 4 , 4 , 1 , FALSE , 'heiti.ttf');		//中文,自己更换字体,也可默认

//echo new Verify(40 , 40 , 4 , 1 , 2 , FALSE);				//纯数字+随机上下左右(不显示)
//echo new Verify(50 , 50 , 4 , 1 , 2 , TRUE);				//纯数字+随机上下左右(显示)

//echo new Verify(50 , 50 , 4 , 2 , 2 , TRUE);				//纯字母+随机上下左右(显示)
//echo new Verify(50 , 40 , 4 , 2 , 2 , FALSE);				//纯字母+随机上下左右(不显示)

//echo new Verify(60 , 40 , 4 , 3 , 2 , FALSE);				//数字+字母+随机上下左右(不显示)
//echo new Verify(60 , 60 , 4 , 3 , 2 , TRUE);				//数字+字母+随机上下左右(显示)

//echo new Verify(60 , 45 , 4 , 4 , 2 , FALSE ,'heiti.ttf');		//中文验证码+随机"上右下左"(不显示)
//echo new Verify(60 , 60 , 4 , 4 , 2 , TRUE  ,'heiti.ttf');		//中文验证码+随机"上右下左"(显示)
