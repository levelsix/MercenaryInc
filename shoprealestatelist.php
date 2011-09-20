<?php 
include_once($_SERVER['DOCUMENT_ROOT'] . "/topmenu.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/User.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/RealEstate.php");

$incrementRealEstatePercentage = .1;

function realEstateIsLocked($re, $playerLevel) {
	if ($re->getMinLevel() == ($playerLevel + 1)) {
		return true;
	}
	return false;
}


$realEstateIDsToRealEstates = RealEstate::getRealEstateIDsToRealEstatesVisibleInShop($playerLevel);
$num = count($realEstateIDsToRealEstates);

if ($num == 0) { 
	echo "No real estate available.";
} else {
	$userRealEstateIDsToQuantity = User::getUsersRealEstateIDsToQuantity($_SESSION['userID']);
	foreach ($realEstateIDsToRealEstates as $realEstateID => $realEstate) {
		if (realEstateIsLocked($realEstate, $playerLevel)) {
			print "<b>LOCKED</b> <br>";
		}
		
		print "real estate: " . $realEstate->getName() . "<br>";
		
			
		$quantity = 0;
		
		if ($userRealEstateIDsToQuantity && array_key_exists($realEstateID, $userRealEstateIDsToQuantity)) {
			$quantity = $userRealEstateIDsToQuantity[$realEstate->getID()];
		}
		
		$realEstatePrice = $realEstate->getPrice() + ($incrementRealEstatePercentage*$realEstate->getPrice())*$quantity;
		
		?>

		Income gained: <?php echo $realEstate->getIncome();?><br>
		Minimum level: <?php echo $realEstate->getMinLevel();?><br>
		Price: <?php echo $realEstatePrice?><br>
		Quantity Owned: <?php echo $quantity?><br>
<?php 	
		if (($playerCash >= $realEstatePrice) && (!realEstateIsLocked($realEstate, $playerLevel))){
?>
			<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/backend/shoprealestateaction.php' method='post'>
			<input type='hidden' name='actionToDo' value='buy' />
			<input type='hidden' name='purchasePrice' value='<?php echo $realEstatePrice;?>' />
			<input type='hidden' name='realEstateID' value='<?php echo $realEstateID;?>' />
			<input type='submit' value='Buy' />
			</form>
<?php 
		} else {
			echo "you can't buy this real estate (you don't have enough cash or it's locked)<br>";
		}
		if ($quantity >= 1 && !realEstateIsLocked($realEstate, $playerLevel)) {
?>
			<form action='<?php $_SERVER['DOCUMENT_ROOT'] ?>/backend/shoprealestateaction.php' method='post'>
			<input type='hidden' name='actionToDo' value='sell' />
			
			<input type='hidden' name='sellBasePrice' value='<?php
				echo ($realEstate->getPrice() +	
				($incrementRealEstatePercentage*$realEstate->getPrice()*($quantity-1)))?>' />
			<input type='hidden' name='realEstateID' value='<?php echo $realEstateID;?>' />
			<input type='submit' value='Sell' />
			</form>
<?php 
		} else {
			print "you can't sell this real estate (don't have any or it's locked)<br>";
		}

		
		print "<br><br>";
	}	
}

?>