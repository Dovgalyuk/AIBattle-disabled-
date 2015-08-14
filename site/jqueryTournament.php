<?php
	include_once('procedures.php');

    if (!isAdmin())
    {
        echo "Нет полномочий";
        exit;
    }
	
    $echoAnswer = "";
    
	
	if (isset($_POST['createTournament']))
	{
		$answer = createNewTournament($_POST['tournamentName'], intval($_POST['gameSelector']), $_POST['tournamentDescription'], $_POST['tournamentState'], intval($_POST['defaultChecker']));
	
		switch ($answer)
		{
			case 0:
				$echoAnswer = "Турнир успешно создан!";
				break;
			case 1:
				$echoAnswer = "Произошла ошибка при создании турнира во время запроса к БД!";
				break;
			case 2:
				$echoAnswer = "Не удалось подключиться к БД!";
				break;
		}
	
	}
	else if (isset($_POST['updateTournament']))
	{
		$answer = updateTournament(intval($_POST['tournamentSelector']), $_POST['tournamentName'], intval($_POST['gameSelector']), $_POST['tournamentDescription'], $_POST['tournamentState'], intval($_POST['defaultChecker']));
		
		switch ($answer)
		{
			case 0:
				$echoAnswer = "Турнир успешно обновлен!";
				break;
			case 1:
				$echoAnswer = "Произошла ошибка при обновлении турнира во время запроса к БД!";
				break;
			case 2:
				$echoAnswer = "Не удалось подключиться к БД!";
				break;
		}
	}
	else if (isset($_POST['deleteTournament']))
	{
		$answer = deleteTournament(intval($_POST['tournamentId']));
		
		switch ($answer)
		{
			case 0:
				$echoAnswer = "Турнир успешно удален!";
				break;
			case 1:
				$echoAnswer = "Произошла ошибка при удалении турнира во время запроса к БД!";
				break;
			case 2:
				$echoAnswer = "Не удалось подключиться к БД!";
				break;
		}
	}
	
	echo $echoAnswer; 
?>
