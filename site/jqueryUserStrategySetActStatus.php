<?php
	include_once('procedures.php');
	setActStatus(intval($_POST['strategyId']), intval($_POST['gameId']), intval($_POST['tournamentId']));
?>