<?php
/**
 * @ Description 日期控件	
 * @ Author	qilin@leju.sina.com.cn
 * @ Time 	2012-2-22 11:08:32
 */
function smarty_function_date($params , &$smarty)
{
	$String  ='<script language="javascript" type="text/javascript" src="' . __JS__ . 'My97DatePicker/WdatePicker.js"></script>'; 

	if(!empty($params))
	{
		$args = '';
		foreach($params as $key => $value){
			$args .= $key . '="' . $value . '" ';
		}
	}
	$name    = empty($params['name']) ? ' name="date" ' : '';
	$args 	 = empty($args) ? '' : $args;
	$String .= '<input class="Wdate" type="text" ' . $args . $name . '  onClick="WdatePicker()">';
	return $String;
}
