<?php
/**
 * Action * 控制器操作类
 * 
 * @Uses MySmarty
 * @Package :	111
 * @Version :	$ID$
 * @Copyright :	Copyright
 * @Author :	Gao qilin <qilin@leju.sina.com.cn> 
 * @License :	PHP Version 3.0 {@link http://www.php.net/license/3_0.txt}
 */
class Action extends MySmarty
{
	STATIC protected $skipFlag = FALSE;
	function run()
	{
		if( method_exists($this, 'init') )
			$this->init();

		$C = !isset($_GET['a']) ? 'index' : strtolower($_GET['a']);

		if( method_exists($this, $C) )
			$this->$C();
		else
			Debug::addmsg('<font color="red">控制器' . ucfirst(strtolower($_GET['m'])) . 'Action中没有' . $C . '这个方法</font>');
		parent::__construct();
	}

	protected function shadowSuccess($Message , $Time , $ControllMethod = '')
	{
		$url = empty($ControllMethod) ?  $GLOBALS['url'] . '/index' : (strpos($ControllMethod , '/') ? $_SERVER['SCRIPT_NAME'] . '/' . ltrim($ControllMethod , '/') : $GLOBALS['url'] . '/' . $ControllMethod);		//当没有第三个参数的时候默认是index方法
		$this->Skip($Message , $Time , $url);
		$this->assign('flag', 1);
		$this->display('PUBLIC/success');
	}


	protected function shadowError($Message , $Time , $ControllMethod = '')
	{
		$url = empty($ControllMethod) ?  '' : (strpos($ControllMethod , '/') ? $_SERVER['SCRIPT_NAME'] . '/' . ltrim($ControllMethod , '/') : $GLOBALS['url'] . '/' . $ControllMethod);				//当没有第三个参数的时候默认是后退(JS操作)!!
		$this->Skip($Message , $Time , $url);
		$this->assign( 'flag', 0 );
		$this->display( 'PUBLIC/success' );
	}

	protected function easySuccess($Message , $Time , $ControllMethod = '')
	{
		echo '<span style="font-size:12px;" >' . $Message . '</span>';
		$this->trun($ControllMethod);
	}

	 protected function easyError($Message , $Time , $ControllMethod = '')
	{
		echo '<span style="font-size:12px;" >' . $Message . '</span>';
		$this->trun($ControllMethod);
	}


	function success($Message , $Time , $ControllMethod = '')
	{
		self::$skipFlag ?  $this->shadowSuccess($Message , $Time , $ControllMethod) : $this->easySuccess($Message , $Time , $ControllMethod);
	}



	function error($Message , $Time , $ControllMethod = '')
	{
		self::$skipFlag ?  $this->shadowError($Message , $Time , $ControllMethod) : $this->easyError($Message , $Time , $ControllMethod);
	}


	private function Skip($Message , $Time , $url)
	{
		$this->caching = 0;		//取消缓存
		$GLOBALS['debug'] = 0;
		$this->assign('mess' , $Message);
		$this->assign('time' , $Time);
		$this->assign('uurl' , $url);
		$this->assign('debug', DEBUG );
	}


	function trun($direction = '')
	{
		//Javascript跳转,可以有输出
		$url = empty($direction) ? '<script> window.history.back() </script>' : '<script>location="' . $GLOBALS['app'] . '/' . trim($direction , '/') . '"</script>';
		echo $url;
	}

}
