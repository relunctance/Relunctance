<?php
class Ajaxpages
{
	//链接
	STATIC protected $url;
	//总条数
	STATIC protected $total;
	//总页数
	STATIC protected $pages;
	//上一页数
	STATIC protected $prevNum;
	//下一页数
	STATIC protected $nextNum;
	//每页显示数
	STATIC protected $num;
	//当前页
	STATIC protected $page;
	//结束数
	STATIC protected $limit;
	//偏移量
	STATIC protected $offset;
	//样式
	STATIC protected $Style = array(
		1  => 'digg',
		2  => 'yahoo',
		3  => 'meneame',
		4  => 'flickr',
		5  => 'sabrosus',
		6  => 'scott',
		7  => 'quotes',
		8  => 'black',
		9  => 'black2',
		10 => 'black-red',
		11 => 'grayr',
		12 => 'yellow',
		13 => 'jogger',
		14 => 'starcraft2',
		15 => 'tres',
		16 => 'megas512',
		17 => 'technorati',
		18 => 'youtube',
		19 => 'msdn',
		20 => 'badoo',
		21 => 'manu',
		22 => 'green-black',
		23 => 'viciao',
		24 => 'yahoo2',
	);


	function __construct($num, $total)
	{
		self::$url		= $_SERVER['SCRIPT_NAME'] . '/' . $_GET['m'] . '/' .$_GET['a'] ;
		self::$num 		= $num;
		self::$total 		= $total;
		self::$pages 		= ceil(self::$total/self::$num);
		self::$page		= empty($_GET['page']) || $_GET['page'] <= 1 ? 1 :( $_GET['page'] > self::$pages ? self::$pages : (int)$_GET['page']);
		self::$prevNum 		= self::$page <= 1 ? FALSE : (self::$page - 1) ;
		self::$nextNum 		= self::$page >= self::$pages ? FALSE : (self::$page + 1) ;
		self::$offset 		= (self::$page - 1) * self::$num ;
//		self::$limit 		= ' where id > ' . ceil(self::$offset/2) . ' limit ' . self::$offset . ',' . self::$num ;
		self::$limit 		= self::$offset . ',' . self::$num ;
	}


	//这个方法是兼容$limit
	//比如:
	//$p = new Pages(10,20000);
	//$p->limit();
	//$p->$limit();
	function limit()
	{
		return  self::$limit ; 
	}

	function __get($param)
	{
		if($param == 'limit')
			return self::$limit;
	}

	function fpage($styleNum = 3 , $isInfoStart = TRUE, $isNumStart = TRUE, $isSelectStart = TRUE, $NumPageNum = 10, $isJumpStart = TRUE, $selectNum = 20)
	{
		$Page  = '<link rel="stylesheet" type="text/css" href="'.$GLOBALS['root'].'Reluctance/Classes/PagesCss/page.css" />';
		$Page .= $isInfoStart 	? self::getPageInfo() 	 		: '';
		$Page .= self::getPrevPage();
		$Page .= $isNumStart	? self::getNumStart($NumPageNum) 	: '';
		$Page .= self::getNextPage();
		$Page .= $isJumpStart  	? self::getJumpStart() 			: '';
		$Page .= $isSelectStart ? self::getSelectStart($selectNum)	: '';

		return '<div class="' . self::$Style[$styleNum] . '">' . $Page . '</div>';
	}

	STATIC protected function getPrevPage()
	{
		return 	self::$page <= 1     ?     '<span class="disabled">首页</span><span class="disabled">上一页</span>'     :     '<a href="javascript:setPage(\''.self::$url.'/page/1\');">首页</a>' . '<a href="javascript:setPage(\''.self::$url.'/page/'.self::$prevNum.'\')">上一页</a>';

	}

	STATIC protected function getNextPage()
	{
		return	self::$page < self::$pages    ?     '<a href="javascript:setPage(\''.self::$url.'/page/'.self::$nextNum.'\')">下一页</a>' . '<a href="javascript:setPage(\''.self::$url.'/page/'.self::$pages.'\')">尾页</a>'      :      '<span class="disabled">下一页</span><span class="disabled">尾页</span>';
	}


	STATIC protected function getPageInfo()
	{
		return '<span class="disabled">'.self::$offset.' - '.(self::$offset + self::$num ).'条&nbsp;&nbsp;第'.self::$page.'页/共'.self::$pages.'页</span>';
	}



	STATIC protected function getNumStart($NumPageNum)
	{
		$page 	= '';
		$num 	= floor(min($NumPageNum , self::$num) / 2);
		$mm 	= self::$page < $NumPageNum ?  1 : floor(self::$page/$num);
		$n	= min( ($mm+1) * $num , self::$pages);
		$m 	= ($mm-1) * $num;

		for($i=$m;$i<=$n; $i++){
			if($i ==0) continue;
			$page .= self::$page == $i ?  '<span class="current">'.$i.'</span>' : '<a href="javascript:setPage(\''.self::$url.'/page/'.$i.'\')">'.$i.'</a>';
		}
		return $page;
	}

	STATIC protected function getJumpStart()
	{
		$page = "\n转到<input type='text' size='3' title='请输入要跳转到的页数并回车' onkeyup=\"this.value=this.value.replace(/\D/g,'')\" onafterpaste=\"this.value=this.value.replace(/\D/g,'')
\" onkeydown=\"javascript:if(event.charCode==13||event.keyCode==13){if(!isNaN(this.value)){setPage('".self::$url."/page/'+this.value);}return false;}\"/>页\n";
		return $page;
	}

	STATIC protected function getSelectStart( $selectNum )
	{
		$page 	= "\n跳至<select name='topage' size='1' onchange=\"setPage('".self::$url."/page/'+this.value)\">\n";
		$num 	= floor(min($selectNum , self::$num) / 2);
		$mm 	= self::$page < $selectNum ?  1 : floor(self::$page/$num);
		$n	= min( ($mm+1) * $num , self::$pages);
		$m 	= ($mm-1) * $num;
		for($i=$n; $i>$m; $i--)
			$page .= self::$page == $i ?  '<option value="'.$i.'" selected>'.$i.'</option>' : '<option value="'.$i.'">'.$i.'</option>';
		$page  .= '</select>页';
		return $page;
	}



}
?>
