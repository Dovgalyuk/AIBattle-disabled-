<?php
	include_once('procedures.php');
	
$echoAnswer = "";

    if (!isAdmin())
    {
        echo "Нет полномочий";
        exit;
    }
	
	if (isset($_POST['createRound']))
	{
		$answer = createRound(intval($_POST['tournamentId']), $_POST['roundName'], 
					intval($_POST['checker']), intval($_POST['previousRound']), intval($_POST['seed']));
	
		switch ($answer)
		{
			case 0:
				$echoAnswer = "Раунд успешно создан!";
				break;
			case 1:
				$echoAnswer = "Произошла ошибка во время создания раунда : проблемы с БД!";
				break;
			case 2:
				$echoAnswer = "Произошла ошибка во время работы с БД!";
				break;
		}
	}
	else if (isset($_POST['updateRound']))
	{
		$roundId = intval($_POST['roundId']);
		$answer = updateRound(intval($_POST['tournamentId']), $roundId, $_POST['roundName'], 
						intval($_POST['checker']), intval($_POST['previousRound']), intval($_POST['seed']));
		
		switch ($answer)
		{
			case 0:
				$echoAnswer = "Раунд успешно обновлен!";
				break;
			case 1:
				$echoAnswer = "Произошла ошибка во время обновления раунда : проблемы с БД!";
				break;
			case 2:
				$echoAnswer = "Произошла ошибка во время работы с БД!";
				break;
		}
	}
	else if (isset($_POST['startRound']))
	{
		$roundId = intval($_POST['roundId']);
		if ($roundId != -1)
		{
			include('startRound.php');
			$echoAnswer = "Раунд начался!";
		}
		else $echoAnswer = "Не удалось начать раунд - roundId = -1 !";
	}
	else if (isset($_POST['openRound']))
	{
		$roundId = intval($_POST['roundId']);
		if ($roundId != -1)
		{
			$answer = setRoundVisible($roundId);
			switch ($answer)
			{
				case 0:
					$echoAnswer = "Раунд успешно опубликован!";
					break;
				case 1:
					$echoAnswer = "Произошла ошибка во время опубликовывания раунда : проблемы с БД!";
					break;
				case 2:
					$echoAnswer = "Произошла ошибка во время работы с БД!";
					break;
			}
		}
		else $echoAnswer = "Не удалось опубликовать раунд - roundId = -1";
	}
    else if (isset($_POST['closeRound']))
	{
		$roundId = intval($_POST['roundId']);
		if ($roundId != -1)
		{
			$answer = setRoundVisible($roundId, false);
			switch ($answer)
			{
				case 0:
					$echoAnswer = "Раунд успешно скрыт!";
					break;
				case 1:
					$echoAnswer = "Произошла ошибка во время скрытия раунда : проблемы с БД!";
					break;
				case 2:
					$echoAnswer = "Произошла ошибка во время работы с БД!";
					break;
			}
		}
		else $echoAnswer = "Не удалось скрыть раунд - roundId = -1";
	}
	
	echo $echoAnswer;
?>
