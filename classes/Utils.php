<?php

function getArrayInString($array, $delim) {
	$arrlength = count($array);
	$toreturn = "";
	for ($i = 0; $i < $arrlength; $i++) {
		$toreturn .= $array[$i];
		if ($i != $arrlength-1) {
			$toreturn .= $delim;
		}
	}
	return $toreturn;
}

// Returns the new level if the user has leveled up
// Otherwise returns -1
function userLeveledUp($currLevel, $totalExp) {
	// Currently just takes 10 exp to level up at each level
	$newLevel = $currLevel + 1;
	if ($totalExp >= $newLevel * 10) {
		$skillPointsGained = 3;
		
		return array("newLevel" => $newLevel, "skillPointsGained" => $skillPointsGained);
	}
	
	return false;
}

?>