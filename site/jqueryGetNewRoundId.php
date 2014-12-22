<?php
	include_once('procedures.php');
	
	$link = getDBConnection();
	if (mysqli_select_db($link, getDBName()))
	{
		echo mysqli_result(mysqli_query($link, "SELECT id FROM rounds ORDER BY id DESC LIMIT 1"), 0);
	}
?>