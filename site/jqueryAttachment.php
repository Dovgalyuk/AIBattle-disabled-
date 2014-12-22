<?php
	include_once('procedures.php');
	
	$echoAnswer = "";
	
	if (isset($_POST['createAttachment']))
	{
		$gameId = intval($_POST['gameId']);
		$answer = createAttachment($gameId, $_POST['originalName'], $_POST['description'], $_POST['attachmentPath']);
		switch ($answer)
		{
			case 0:
				$echoAnswer = "Файл игры успешно загружен!";
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
	else if (isset($_POST['updateAttachment']))
	{
		$gameId 		= intval($_POST['gameId']);
		$attachmentId 	= intval($_POST['attachmentId']);
		
		$answer			= updateAttachment($attachmentId, $gameId, $_POST['originalName'], $_POST['description'], $_POST['attachmentPath']);
		
		switch ($answer)
		{
			case 0:
				$echoAnswer = "Файл игры успешно обновлен (текст + файл)!";
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
	else if (isset($_POST['deleteAttachment']))
	{
		$attachmentId = intval($_POST['attachmentId']);
		$answer = deleteAttachment($attachmentId);
		
		switch ($answer)
		{
			case 0:
				$echoAnswer = "Файл игры успешно удален!";
				break;
			case 1:
				$echoAnswer = "Не удалось удалить файл ./attachments/". $attachmentId . " !";
				break;
			case 2:
				$echoAnswer = "Не получилось удалить информацию из БД!";
				break;
			case 3:
				$echoAnswer = "Не удалось подключиться к БД!";
				break;
		}
	}
	
	echo $echoAnswer;
?>