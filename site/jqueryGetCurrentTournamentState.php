<?php
	include_once('procedures.php');
	if (isset($_SESSION['tournamentState']))
		echo $_SESSION['tournamentState'];
	else
		echo 'null';
?>