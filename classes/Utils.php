<?php

/*
 * returns the concatenation of each element in the array separated by
 * the delimiter
 */
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
// Updates the user object and the database if a level up occurs
function checkLevelUp($user) {
	$currLevel = $user->getLevel();
	$totalExp = $user->getExperience();
	
	// Currently just takes 10 exp to level up at each level
	$newLevel = floor($totalExp / 10);
	
	if ($newLevel > $currLevel) {
		$skillPointsGained = 3 * ($newLevel - $currLevel);
		
		$user->updateLevel($newLevel, $skillPointsGained);
				
		return $skillPointsGained;
	}
	
	return 0;
}

// Returns n random integers in the range [0, $max)
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