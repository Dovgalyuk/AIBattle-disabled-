<?php
	include_once('procedures.php');
		$val = changePassword($_POST['newPassword'], (isAdmin() && isset($_GET['id']))?$_GET['id']:getActiveUserID());
		switch ($val)
		{
			case 0:
				echo "Пароль успешно обновился!";
				break;
			case 1:
				echo "Не удалось обновить пароль в БД!";
				break;
			case 2:
				echo "Проблемы с доступом к БД!";
				break;
			case 4: 
				echo "Некорректный пароль";
				break;
		}

?>
