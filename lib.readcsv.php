<?php
// This is modified function from "spam at cyber-space dot nl" - 07-Sep-2006 06:53
// "I needed a fast/robust csv parser in PHP that could handle unix-, windows- and mac-style linebreaks. i takes a csv-string as input and outputs a multidimensional array with lines and fields."
// http://in2.php.net/fgetcsv#69485

// The modified version takes file and returns array:
/*$ret_array=
Array
(
    [id] => Array
        (
            [1] => 001-2an1lb-01
            [2] => 001-2an1ld-01
        )

    [code] => Array
        (
            [1] => 001-2AN1LB-01
            [2] => 001-2AN1LD-01

*/


function readcsv($file,$delim=',',$enclosure='"')
{
	if (!is_readable($file)) return false;
	
	$data=file_get_contents($file);
	$enclosed=false;
	$fldcount=0;
	$linecount=1;
	$fldval=''; $titlesline=true; $colcount=0;
	for($i=0;$i<strlen($data);$i++)
	{
		$chr=$data{$i};
		switch($chr)
		{
			case $enclosure: // if it is enclosure we're adding it to data only if enclosed and only if it is doubled
				if($enclosed&&isset($data{$i+1})&&$data{$i+1}==$enclosure&&$titlesline==false) {
					$fldval.=$chr;
					++$i; //skip next char
				} elseif ($enclosed&&isset($data{$i+1})&&$data{$i+1}==$enclosure&&$titlesline==true) {
					$titlefldval.=$chr;
					++$i; //skip next char
				} else {
					$enclosed=!$enclosed;
				}
				break;
			case $delim: // if it is delimeter we're adding it to data only if it is enclosed
				if ($enclosed&&$titlesline==false) {
					$fldval.=$chr;
				} elseif($enclosed&&$titlesline==true) {
					$titlefldval.=$chr;
				} elseif (!$enclosed&&$titlesline==false) {
					$fldtitle=$titles[$fldcount++];
					$ret_array[$fldtitle][$linecount]=$fldval;
					$fldval='';
				} elseif (!$enclosed&&$titlesline==true){
					$titles[$colcount++]=$fldval;
					$fldval='';
				}
				break;
			case "\r":
				if(!$enclosed&&$data{$i+1}=="\n")
					continue;
			case "\n":
				
				if ($enclosed&&$titlesline==false) {
					$fldval.=$chr;
				} elseif ($enclosed&&$titlesline==true){
					$titlefldval.=$chr;
				} elseif (!$enclosed&&$titlesline==false) {
					$fldtitle=$titles[$fldcount];
					$ret_array[$fldtitle][$linecount++]=$fldval;
					$fldcount=0;
					$fldval='';
				} elseif (!$enclosed&&$titlesline==true){
					$titles[$colcount++]=$fldval;
					$fldcount=0;
					$fldval='';
					$titlesline=false;
				}
				
				break;
			default:
				$fldval.=$chr;
		}
	}
	if($fldval)
		$fldtitle=$titles[$fldcount];
		$ret_array[$fldtitle][$linecount]=$fldval;
	return $ret_array; 
}


function readcsvColumn($file,$column,$delim=',',$enclosure='"') {
	$array=readcsv($file,$delim,$enclosure);
	if (isset($array[$column])) {
		return $array[$column];
	} else { 
		return false; 
	}
}



?>