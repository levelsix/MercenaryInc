<?php

function getPlayerTypeFromTypeID($type) {
	$playertype1="specialist";
	$playertype2="heavy weapons";
	$playertype3="marine";
	
	switch ($type)
	{
		case 1:
			return $playertype1;
		case 2:
			return $playertype2;
		case 3:
			return $playertype3;
		default:;
	}
}
?>