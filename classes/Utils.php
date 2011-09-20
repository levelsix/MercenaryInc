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

// Returns the number of skill points gained in a level up
// Returns 0 if no level up (i.e. no skill points gained)
function checkLevelUp($currLevel, $totalExp) {
	// Currently just takes 10 exp to level up at each level
	$newLevel = floor($totalExp / 10);
	
	if ($newLevel > $currLevel) {
		$skillPointsGained = 3 * ($newLevel - $currLevel);
	
		return $skillPointsGained;
	}
	
	return 0;
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