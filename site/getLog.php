<?php
	include_once('procedures.php');
	$link = getDBConnection();
	if (mysqli_select_db($link, getDBName()))
	{
		$duel = intval($_GET['duel']);
		$user = intval(getActiveUserID());
		$round = mysqli_result(mysqli_query($link, "SELECT round FROM duels WHERE id = $duel"), 0);
		$query = "SELECT COUNT(*) FROM duels";
		if (!isAdmin())
		{
			$query .= " INNER JOIN strategies s1 ON s1.id = duels.strategy1 INNER JOIN strategies s2 ON s2.id = duels.strategy2";
			if ($round != -1)
				$query .= " INNER JOIN rounds ON rounds.id = duels.round";
			$query .= " WHERE (s1.user = $user OR s2.user = $user) AND duels.id = $duel";
			if ($round != -1)
				$query .= " AND rounds.visible = true";
		}
		
		if (mysqli_result(mysqli_query($link, $query), 0) > 0)
		{
			header ("Content-Type: text/plain");
			$file = @file_get_contents("./logs/$duel.txt");
			echo $file;
		}
	}
?>
