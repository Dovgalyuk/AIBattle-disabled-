<?php
	include_once('procedures.php');
	if (isset($_SESSION['adminPanelState']))
		echo $_SESSION['adminPanelState'];
	else
		echo 'null';
?>