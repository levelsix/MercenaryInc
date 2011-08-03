<?php

function getCityNameFromCityID($cityID) {
	switch ($cityID)
	{
		case 1:
			return "kalm";
		case 2:
			return "zanarkand";
		case 3:
			return "midgar";
		default:;
	}
}
?>