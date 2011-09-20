<?php

function getItemTypeFromTypeID($type) {
	$itemtype1="weapon";
	$itemtype2="protection";
	$itemtype3="vehicle";
	
	switch ($type)
	{
		case 1:
			return $itemtype1;
		case 2:
			return $itemtype2;
		case 3:
			return $itemtype3;
		default:;
	}
}
?>