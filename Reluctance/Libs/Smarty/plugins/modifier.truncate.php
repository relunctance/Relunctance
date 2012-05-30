<?php
function smarty_modifier_truncate($string, $length = 80, $dot = '...',  $break_words = false, $middle = false ){
    if(strlen($string) <= $length)
        return $string;
    $string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);
    $strcut = '';
    $charset = 'utf-8';
    if(strtolower($charset) == 'utf-8')
    {
        $n = $tn = $noc = 0;
        $mylength = strlen($string);
        while($n < $mylength) {
            $t = ord($string[$n]);
            if($t == 9 || $t == 10 || (32 <= $t && $t <= 126))
            {
                $tn = 1;
                $n++;
                $noc++;
            } else if(194 <= $t && $t <= 223) {
                $tn = 2;
                $n += 2;
                $noc += 2;
            } else if(224 <= $t && $t < 239) {
                $tn = 3;
                $n += 3;
                $noc += 2;
            } else if(240 <= $t && $t <= 247) {
                $tn = 4;
                $n += 4;
                $noc += 2;
            } else if(248 <= $t && $t <= 251) {
                $tn = 5;
                $n += 5;
                $noc += 2;
            } else if($t == 252 || $t == 253) {
                $tn = 6;
                $n += 6;
                $noc += 2;
            } else {
                $n++;
            }

            if($noc >= $length)
            {
                break;
            }
        }
        if($noc > $length)
        {
            $n -= $tn;
        }
        $strcut = substr($string, 0, $n);
    } else {
	for($i = 0; $i < $length; $i++) {
	    $strcut .= isset($string[$i]) ? (ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i]) : '';
	}
    }
    
    return $strcut . $dot;
}
