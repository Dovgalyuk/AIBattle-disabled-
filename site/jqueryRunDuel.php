<?php
	include_once('procedures.php');
	$s1 = intval($_POST['strategyId']);
	$gameId = intval($_POST['gameId']);
	$tournamentId = intval($_POST['tournamentId']);
		
	$result = getUserStrategy($gameId, $tournamentId);
	
	$link = getDBConnection();
	
	if (mysqli_select_db($link, getDBName()))
	{
		if (mysqli_num_rows($result) > 0)
		{
			$s2 = intval(mysqli_result($result, 0));
			mysqli_query($link, "INSERT INTO duels SET round = -1, strategy1 = $s1, strategy2 = $s2, status = 'W'");
			mysqli_query($link, "INSERT INTO duels SET round = -1, strategy1 = $s2, strategy2 = $s1, status = 'W'");

            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')    
                pclose(popen("start /B php -q runDuels.php", "r"));
            else
                exec("nohup /usr/bin/php -f ./runDuels.php > /dev/null 2>&1 &");

		}
	}
?>
