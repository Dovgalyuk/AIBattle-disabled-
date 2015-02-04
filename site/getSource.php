<?php
	include_once('procedures.php');
	$link = getDBConnection();
	if (mysqli_select_db($link, getDBName()))
	{
		$id = intval($_GET['id']);
		$query = "SELECT * FROM strategies WHERE id = $id";
		if (!isAdmin())
			$query .= " AND user = " . intval(getActiveUserID());
		$res = mysqli_query($link, $query);
		if (mysqli_num_rows($res) == 1 && !(($file = @file_get_contents("./executions/".$id)) === FALSE))
		{
			$file = "<code>" . nl2br(str_replace(" ", "&nbsp;", 
				str_replace("\t", "    ", htmlspecialchars($file)))) . "</code>";
			echo $file;
		}
	}
?>
