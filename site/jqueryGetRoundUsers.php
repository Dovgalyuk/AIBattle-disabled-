<?php
	include_once('procedures.php');

	$roundId = intval($_POST['roundId']);
	$tournamentId = intval($_POST['tournamentId']);
	$previousRound = getPreviousRound($roundId);

	$data = array("possibleUsers" => array(), "acceptedUsers" => array());

	$data["acceptedUsers"] = getAcceptedUsers($roundId, $previousRound, $tournamentId);
	$data["possibleUsers"] = getPossibleUsers($roundId, $previousRound, $tournamentId);

	echo json_encode($data);
?>