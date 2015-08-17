<?php
	include_once('procedures.php');
	$checkerId = intval($_POST['checkerId']);

	$echoAnswer = "";

    if (!isAdmin())
    {
        echo "Нет полномочий";
        exit;
    }
	
	if (isset($_POST['createChecker']))
	{
		$answer = saveChecker($_POST['checkerName'], intval($_POST['gameId']), $_POST['checkerPath'], $_POST['hasSeed']);
		if (is_numeric($answer))
		{
			switch ($answer)
			{
				case 0:
					$echoAnswer = "Тестировщик успешно создан!";
					break;
				case -1:
					$echoAnswer = "Произошла ошибка во время сохранения файла!";
					break;
				case 1:
					$echoAnswer = "Произошла ошибка во время передачи файла!";
					break;
				case 2:
					$echoAnswer = "Не получилось обновить информацию в БД!";
					break;
				case 3:
					$echoAnswer = "Не удалось подключиться к БД!";
					break;
			}
		}
		else
		{
			$answer = substr($answer, 1);
			$echoAnswer = 'Ошибка при компиляции: ' . $answer;
		}
	}
	else if (isset($_POST['updateChecker']))
	{
		$answer = updateChecker(intval($_POST['checkerId']), $_POST['checkerName'], intval($_POST['gameId']), $_POST['checkerPath'], $_POST['hasSeed']);
		if (is_numeric($answer))
		{
			switch ($answer)
			{
				case 0:
					$echoAnswer = "Тестировщик успешно обновлен (текст + код)!";
					break;
				case -1:
					$echoAnswer = "Текстовые данные обновлены!";
					break;
				case 1:
					$echoAnswer = "Произошла ошибка во время передачи файла!";
					break;
				case 2:
					$echoAnswer = "Не получилось обновить информацию в БД!";
					break;
				case 3:
					$echoAnswer = "Не удалось подключиться к БД!";
					break;
			}
		}
		else
		{
			$answer = substr($answer, 1);
			$echoAnswer = 'Ошибка при компиляции: ' . $answer . '. Текстовые данные - обновились!';
		}
	}
	else if (isset($_POST['deleteChecker']))
	{
		$answer = deleteChecker($checkerId);
				
		switch ($answer)
		{
			case 0:
				$echoAnswer = "Тестировщик успешно удален!";
				break;
			case 1:
				$echoAnswer = "Не удалось удалить файл ./testers/". $checkerId ." и ./testers_bin/" . $checkerId . ".exe! Необходимо ручное удаление!";
				break;
			case 2:
				$echoAnswer = "Не удалось удалить файл ./testers/". $checkerId . "Необходимо ручное удаление!";
				break;
			case 3:
				$echoAnswer = "Не удалось удалить файл ./testers_bin/". $checkerId . ".exe. Необходимо ручное удаление!";
				break;
			case 4:
				$echoAnswer = "Не получилось удалить информацию из БД!";
				break;
			case 5:
				$echoAnswer = "Не удалось подключиться к БД!";
				break;
		}
	}
	
	echo $echoAnswer;
?>
