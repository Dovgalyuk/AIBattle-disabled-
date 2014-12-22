<?php
	include_once('procedures.php');
	
	$gameId = intval($_POST['gameId']);
	
	echo json_encode(getCheckerListByGameId($gameId));
?>