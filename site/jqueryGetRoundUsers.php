<?php
	include_once('procedures.php');

	$roundId = intval($_POST['roundId']);
	$tournamentId = intval($_POST['tournamentId']);
    
    if ($roundId != -1)
    {
        /* Obsolete - to be removed with old admin panel */
        $previousRound = getPreviousRound($roundId);

        $data = array("possibleUsers" => array(), "acceptedUsers" => array());

        $data["acceptedUsers"] = getAcceptedUsers($roundId, $previousRound, $tournamentId);
        $data["possibleUsers"] = getPossibleUsers($roundId, $previousRound, $tournamentId);
    }
    else
    {
        $data["strategies"] = getTournamentStrategies($tournamentId);
    }

	echo json_encode($data);
?>