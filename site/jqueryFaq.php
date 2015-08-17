<?php
	include_once('procedures.php');
	
	$echoAnswer = "";
	$questionId = intval($_POST['questionId']);

    if (!isAdmin())
    {
        echo "Нет полномочий";
        exit;
    }
	
	if (isset($_POST['createQuestion']))
	{
		$answer = createAnswer($_POST['question'], $_POST['answer']);
	
		switch ($answer)
		{
			case 0:
				$echoAnswer = "Вопрос успешно создан!";
				break;
			case 1:
				$echoAnswer = "Произошла ошибка во время отправления вопроса в БД!";
				break;
			case 2:
				$echoAnswer = "Не удалось подключиться к БД!";
				break;
		}
	}
	else if (isset($_POST['updateQuestion']))
	{
		$answer = updateAnswer($questionId, $_POST['question'], $_POST['answer']);
		
		switch ($answer)
		{
			case 0:
				$echoAnswer = "Вопрос успешно обновлен!";
				break;
			case 1:
				$echoAnswer = "Произошла ошибка во время отправления вопроса в БД!";
				break;
			case 2:
				$echoAnswer = "Не удалось подключиться к БД!";
				break;
		}
	}
	else if (isset($_POST['closeQuestion']))
	{
		$answer = closeAnswer($questionId);
		
		switch ($answer)
		{
			case 0:
				$echoAnswer = "Вопрос успешно закрыт!";
				break;
			case 1:
				$echoAnswer = "Произошла ошибка во время отправления вопроса в БД!";
				break;
			case 2:
				$echoAnswer = "Не удалось подключиться к БД!";
				break;
		}
	}
	
	echo $echoAnswer;
?>
