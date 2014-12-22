<?php
	include_once('procedures.php');
	# get data from form
	if (isset($_POST['submitLoginPassword']))
	{
		if (isset($_POST['login']) && isset($_POST['password']))
		{
			$link = mysqli_connect('localhost', $_POST['login'], $_POST['password']) or die("Can't connect to DB: ".mysqli_error()); 
			if ($link)
			{
				$file = fopen('authData.txt', 'w');
				if ($file)
				{
					fwrite($file, $_POST['login']."\r\n".$_POST['password']."\r\n");
					fclose($file);
					echo '<meta http-equiv="refresh" content="0; url=installCreateDB.php">';
					exit();
				}
			}
		}
	}	
?>
<html>
	<body>
<form method="post">
		<center>Логин и пароль для регистрации в БД
				<p>Логин: 		<input type="text" name="login" value=""></input>	</p>
				<p>Пароль: 		<input type="password" name="password" value=""></input>	</p>
								<input type="submit" name="submitLoginPassword" value="Отправить данные"></input>
		</center>
	</form>
</body></html>
