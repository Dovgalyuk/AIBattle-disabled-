<?php
	include_once('procedures.php');
	
	$echoAnswer = "";

    if (!isAdmin())
    {
        echo "Нет полномочий";
        exit;
    }
	
	$newsId = intval($_POST['newsId']);
	$date = reverseDate($_POST['date'], "/");
	
	if (isset($_POST['createNews']))
	{
		$answer = createNews($_POST['header'], $_POST['text'], $date);
		
		switch ($answer)
		{
			case 0:
				$echoAnswer = "Новость успешно создана!";
				break;
			case 1:
				$echoAnswer = "Произошла ошибка при создании новости!";
				break;
			case 2:
				$echoAnswer = "Не удалось подключиться к БД!";
				break;
		}
	}
	else if (isset($_POST['updateNews']))
	{
		$answer = updateNews($newsId, $_POST['header'], $_POST['text'], $date);
		
		switch ($answer)
		{
			case 0:
				$echoAnswer = "Новость успешно обновлена!";
				break;
			case 1:
				$echoAnswer = "Произошла ошибка при обновлении новости!";
				break;
			case 2:
				$echoAnswer = "Не удалось подключиться к БД!";
				break;
		}
	}
	else if (isset($_POST['deleteNews']))
	{
		$answer = deleteNews($newsId);
		
		switch ($answer)
		{
			case 0:
				$echoAnswer = "Новость успешно удалена!";
				break;
			case 1:
				$echoAnswer = "Произошла ошибка при удалении новости!";
				break;
			case 2:
				$echoAnswer = "Не удалось подключиться к БД!";
				break;
		}
	}
	
	echo $echoAnswer;
?>
