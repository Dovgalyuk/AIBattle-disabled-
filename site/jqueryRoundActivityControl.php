<?php
	include_once('procedures.php');
	$roundId = intval($_POST['roundId']);
	$link = getDBConnection();
	if (mysqli_select_db($link, getDBName()))
	{
		if ($_POST['mode'] == "clearAcceptedUsers")
		{
			mysqli_query($link, "DELETE FROM roundActivity WHERE round = $roundId AND state = 'ACT'");
		} 
		else if ($_POST['mode'] == "declineUsers")
		{
			$acceptedUsers = $_POST['users'];
			foreach($acceptedUsers as $strategyId)
			{
				$userId = intval(getUserByStrategy($strategyId));
				mysqli_query($link, "DELETE FROM roundActivity WHERE round = $roundId AND user = $userId");
			}
		}
		else if ($_POST['mode'] == "acceptPossibleUsers")
		{
			$possibleUsers = $_POST['users'];
			foreach($possibleUsers as $strategyId)
			{
				$userId = intval(getUserByStrategy($strategyId));
				mysqli_query($link, "INSERT INTO roundActivity SET round = $roundId, user = $userId, state = 'ACT'");
			}
		}
		else if ($_POST['mode'] == "acceptFirstNthUsers")
		{
			$possibleUsers = $_POST['users'];
			$value = intval($_POST['value']);
			$counter = 0;
			if ($counter < $value)
			{
				foreach($possibleUsers as $strategyId)
				{
					$userId = intval(getUserByStrategy($strategyId));
					mysqli_query($link, "INSERT INTO roundActivity SET round = $roundId, user = $userId, state = 'ACT'");
					$counter = $counter + 1;
					if ($counter == $value)
						break;
				}
			}
		}
		else if ($_POST['mode'] == "declineLastUsers")
		{
			$acceptedUsers = $_POST['users'];
			$value = intval($_POST['value']);
			$previousRound = intval($_POST['previousRound']);
			foreach($acceptedUsers as $strategyId)
			{
				if (getScoreByStrategy($previousRound, $strategyId) > $value)
				{
					$userId = getUserByStrategy($strategyId);
					mysqli_query($link, "INSERT INTO roundActivity SET round = $roundId, user = $userId, state = 'ACT'");
				}
			}
		}
	}
?>