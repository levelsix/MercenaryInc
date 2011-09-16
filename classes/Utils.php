<?php

function getArrayInString($array, $delim) {
	$arrlength = count($array);
	$toreturn = "";
	for ($i = 0; $i < $arrlength; $i++) {
		$toreturn .= $array[$i];
		if ($i != $arrlength-1) {
			$toreturn .= " " . $delim . " ";
		}
	}
	return $toreturn;
}

// Returns the new level if the user has leveled up
// Otherwise returns -1
function userLeveledUp($currLevel, $totalExp) {
	// Currently just takes 10 exp to level up at each level
	$newLevel = floor($totalExp / 10);
	
	if ($newLevel > $currLevel) {
		$skillPointsGained = 3 * ($newLevel - $currLevel);
		
		return array("newLevel" => $newLevel, "skillPointsGained" => $skillPointsGained);
	}
	
	return false;
}


function getRandomIntegers($n, $max) {
	$randomIntegers = array();
	while (count($randomIntegers) < $n) {
		$randomInt = rand(0, $max - 1);
		if (!isset($randomIntegers[$randomInt])) {
			$randomIntegers[$randomInt] = 1;
		}
	}
	return $randomIntegers;
}


?>