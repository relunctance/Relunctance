<?php
/**
 * Base * 数据库操作类底层抽象接口
 * 
 * @Package :	111
 * @Version :	$ID$
 * @Copyright :	Copyright
 * @Author :	Gao qilin <qilin@leju.sina.com.cn> 
 * @License :	PHP Version 3.0 {@link http://www.php.net/license/3_0.txt}
 */
interface  Base
{
	 function insert( $arr = NULL );

	 function delete();

	 function total();

	 function find( $param='' );

	 function select();

	 //function update( $arr = NULL );

	 function query( $sql , $executeArray = array() );

	 function dbSize() ;

	 function dbVersion() ;

	 function __call($methodName , $args);

	 //function field($args  , $Method = array(), $relateArray = array() );

}
