<?php
	include_once('procedures.php');
	$answer = sendQuestion($_POST['userQuestion']);
	
	switch ($answer)
	{
		case 0:
			echo "Вопрос успешно отправлен!";
			break;
		case 1:
			echo "Произошла ошибка во время отправления вопроса в БД!";
			break;
		case 2:
			echo "Не удалось подключиться к БД!";
			break;
	}
?>