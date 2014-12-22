<?php
	include_once('procedures.php');
	
	$echoAnswer = "";
	$gameId = intval($_POST['gameId']);
	
	if (isset($_POST['createGame']))
	{
		$answer = createGame($_POST['gameName'], $_POST['gameDescription'], $_POST['visualizerPath'], $_POST['timeLimit'], $_POST['memoryLimit']);
		
		switch ($answer)
		{
			case 0:
				$echoAnswer = "Игра успешно создана (вместе с визуализатором)! Не забудьте создать для игры один или несколько тестировщиков!";
				break;
			case -1:
				$echoAnswer = "Игра успешно создана! Не забудьте создать для игры один или несколько тестировщиков!";
				break;
			case 1:
				$echoAnswer = "Произошла ошибка во время передачи визуализатора!";
				break;
			case 2:
				$echoAnswer = "Произошла ошибка во время работы с БД!";
				break;
			case 3:
				$echoAnswer = "Не удалось подключиться к БД!";
				break;
		}
	}
	else if (isset($_POST['updateGame']))
	{
		$answer = updateGame($_POST['gameId'], $_POST['gameName'], $_POST['gameDescription'], $_POST['visualizerPath'], $_POST['timeLimit'], $_POST['memoryLimit']);
	
		switch ($answer)
		{
			case 0:
				$echoAnswer = "Игра успешно обновлена (вместе с визуализатором)!";
				break;
			case -1:
				$echoAnswer = "Игра успешно обновлена!";
				break;
			case 1:
				$echoAnswer = "Произошла ошибка во время передачи визуализатора!";
				break;
			case 2:
				$echoAnswer = "Произошла ошибка во время работы с БД!";
				break;
			case 3:
				$echoAnswer = "Не удалось подключиться к БД!";
				break;
		}
	}
	else if (isset($_POST['deleteVisualizer']))
	{
		$answer = deleteVizualizer($gameId);
		
		switch ($answer)
		{
			case 0:
				$echoAnswer = "Визуализатор успешно удален!";
				break;
			case -1:
				$echoAnswer = "Не удалось удалить визуализатор с сервера (однако данные из БД были удалены)!";
				break;
			case 1:
				$echoAnswer = "Произошла ошибка во время удаления визуализатора : проблемы с БД!";
				break;
			case 2:
				$echoAnswer = "Произошла ошибка во время работы с БД!";
				break;
		}
	}
	else if (isset($_POST['deleteGame']))
	{
		$answer = deleteGame($gameId);
		switch ($answer)
		{
			case 0:
				$echoAnswer = "Игра успешно удалена!";
				break;
			case 1:
				$echoAnswer = "Произошла ошибка во время удаления игры : проблемы с БД!";
				break;
			case 2:
				$echoAnswer = "Произошла ошибка во время работы с БД!";
				break;
		}
	}
	
	echo $echoAnswer;
?>