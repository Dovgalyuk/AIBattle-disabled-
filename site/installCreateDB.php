<?php
	include_once('procedures.php');
	if (isset($_POST['submitDB']))
	{
		$link = getDBConnection();
		
		if (!mysqli_select_db($link, $_POST['name']))
		{
			if (mysqli_errno($link) == 1049)
			{
				# DB is not created!
				mysqli_query($link, "CREATE DATABASE {$_POST['name']}");
				print "{$_POST['name']} создана!";
			}
		}
		else
		if (isset($_POST['destroy']) && $_POST['destroy'] == 1) 
		{
			mysqli_query($link, "DROP DATABASE {$_POST['name']}");
			mysqli_query($link, "CREATE DATABASE {$_POST['name']}");
		}
			
		// Создаём таблицы в выбранной БД
		if (mysqli_select_db($link, $_POST['name']))
		{	
			mysqli_query($link, "CREATE TABLE `users` (
				`id` int NOT NULL auto_increment,
				`login` varchar(30) NOT NULL,
				`password` varchar(32) NOT NULL,
				`hash` varchar(32),
				`group` varchar(8) NOT NULL default 'user',
				`name` varchar(32),
				`surname` varchar(32),
				`patronymic` varchar(32),
				`description` text,
				PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
				
			mysqli_query($link, "CREATE TABLE `games` (
				`id` int NOT NULL auto_increment,
				`name` varchar(30) NOT NULL,
				`description` text NOT NULL,
				`hasVisualizer` bool NOT NULL default false,
				`timeLimit` int NOT NULL default 1000,
				`memoryLimit` int NOT NULL default 64000,
				PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ");
				
			mysqli_query($link, "CREATE TABLE `checkers` (
				`id` int NOT NULL auto_increment PRIMARY KEY,
				`game` int,
				`name` text NOT NULL,
				`hasSeed` bool NOT NULL default false,
				FOREIGN KEY (`game`) REFERENCES games(`id`) ON DELETE CASCADE
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ");

			mysqli_query($link, "CREATE TABLE `tournaments`(
				`id` int NOT NULL auto_increment PRIMARY KEY,
				`game` int NOT NULL,
				`defaultChecker` int default -1,
				`name` varchar(32) NOT NULL,
				`description` text NOT NULL,
				`state` varchar(16),
				FOREIGN KEY (`game`) REFERENCES games(`id`) ON DELETE CASCADE,
				FOREIGN KEY (`defaultChecker`) REFERENCES checkers(`id`) ON DELETE SET NULL
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; "); 
				
			mysqli_query($link, "CREATE TABLE `strategies` (
				`id` int NOT NULL auto_increment PRIMARY KEY,
				`user` int NOT NULL,
				`game` int NOT NULL,
				`tournament` int NOT NULL,
				`status` varchar(3) NOT NULL default 'OK',
				`language` varchar(3) NOT NULL,
                `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
				INDEX user_ind(`user`),
				INDEX game_ind(`game`),
				FOREIGN KEY (`user`) REFERENCES users(`id`) ON DELETE CASCADE,
				FOREIGN KEY (`game`) REFERENCES games(`id`) ON DELETE CASCADE,
				FOREIGN KEY (`tournament`) REFERENCES tournaments(`id`) ON DELETE CASCADE
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ");
				
			mysqli_query($link, "CREATE TABLE `rounds` (
				`id` int NOT NULL auto_increment PRIMARY KEY,
				`game` int NOT NULL,
				`tournament` int NOT NULL,
				`checker` int default -1,
				`previousRound` int NOT NULL default -1,
				`name` varchar(60) NOT NULL,
				`date` datetime,
				`visible` bool NOT NULL default false,
				`seed` int NOT NULL default false,
				INDEX game_ind(`game`),
				FOREIGN KEY (`game`) REFERENCES games(`id`) ON DELETE CASCADE,
				FOREIGN KEY (`tournament`) REFERENCES tournaments(`id`) ON DELETE CASCADE,
				FOREIGN KEY (`checker`) REFERENCES checkers(`id`) ON DELETE SET NULL
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ");
			
			mysqli_query($link, "CREATE TABLE `roundActivity` (
				`id` int NOT NULL auto_increment PRIMARY KEY,
				`round` int NOT NULL,
				`user` int NOT NULL,
				`state` varchar(8) NOT NULL,
				FOREIGN KEY (`round`) REFERENCES rounds(`id`) ON DELETE CASCADE,
				FOREIGN KEY (`user`) REFERENCES users(`id`) ON DELETE CASCADE
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ");
			
			mysqli_query($link, "CREATE TABLE `roundStatistics` (
				`id` int NOT NULL auto_increment PRIMARY KEY,
				`round` int NOT NULL,
				`user` int NOT NULL,
				`totalScore` int NOT NULL,
				FOREIGN KEY (`round`) REFERENCES rounds(`id`) ON DELETE CASCADE,
				FOREIGN KEY (`user`) REFERENCES users(`id`) ON DELETE CASCADE
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ");
			
			mysqli_query($link, "CREATE TABLE `scores` (
				`round` int NOT NULL,
				`strategy` int NOT NULL,
				`score` int NOT NULL,
				PRIMARY KEY (`round`, `strategy`),
				INDEX round_ind(`round`),
				INDEX strategy_ind(`strategy`),
				FOREIGN KEY (`round`) REFERENCES rounds(`id`) ON DELETE CASCADE,
				FOREIGN KEY (`strategy`) REFERENCES strategies(`id`) ON DELETE CASCADE
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ");
				
			mysqli_query($link, "CREATE TABLE `duels` (
				`id` int NOT NULL auto_increment PRIMARY KEY,
				`round` int NOT NULL default -1,
				`strategy1` int NOT NULL,
				`strategy2` int NOT NULL,
				`status` varchar(8) NOT NULL,
				INDEX round_ind(`round`),
				INDEX strategy_ind1(`strategy1`),
				INDEX strategy_ind2(`strategy2`),
				FOREIGN KEY (`strategy1`) REFERENCES strategies(`id`) ON DELETE CASCADE,
				FOREIGN KEY (`strategy2`) REFERENCES strategies(`id`) ON DELETE CASCADE
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ");
				
			mysqli_query($link, "CREATE TABLE `attachments` (
				`id` int NOT NULL auto_increment PRIMARY KEY,
				`game` int NOT NULL,
				`originalName` varchar(128) NOT NULL,
				`description` varchar(128) NOT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ");

			mysqli_query($link, "CREATE TABLE `news` (
				`id` int NOT NULL auto_increment PRIMARY KEY,
				`header` text NOT NULL,
				`text` text NOT NULL,
				`date` date NOT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ");
				
			mysqli_query($link, "CREATE TABLE `newsComments` (
				`id` int NOT NULL auto_increment PRIMARY KEY,
				`news` int NOT NULL,
				`user` int NOT NULL,
				`text` text NOT NULL,
				`date` datetime NOT NULL,
				FOREIGN KEY (`news`) REFERENCES news(`id`) ON DELETE CASCADE,
				FOREIGN KEY (`user`) REFERENCES users(`id`) ON DELETE CASCADE
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ");
				
			mysqli_query($link, "CREATE TABLE `userQuestions` (
				`id` int NOT NULL auto_increment PRIMARY KEY,
				`user` int NOT NULL,
				`question` text NOT NULL,
				`answer` text,
				`status` varchar(16) NOT NULL,
				FOREIGN KEY (`user`) REFERENCES users(`id`) ON DELETE CASCADE
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ");
			
			mysqli_query($link, "CREATE TABLE `privateMessages` (
				`id` int NOT NULL auto_increment PRIMARY KEY,
				`sender` int NOT NULL,
				`recevier` int NOT NULL,
				`title` text NOT NULL,
				`text` text NOT NULL,
                `date` datetime NOT NULL,
                `viewed` boolean NOT NULL,
				FOREIGN KEY (`sender`) REFERENCES users(`id`) ON DELETE CASCADE,
				FOREIGN KEY (`recevier`) REFERENCES users(`id`) ON DELETE CASCADE
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ");
				
			mysqli_query($link, "CREATE TABLE `images` (
				`id` int NOT NULL auto_increment PRIMARY KEY,
				`game` int,
				`type` text NOT NULL,
				`description` text NOT NULL,
				`originalName` text NOT NULL,
				FOREIGN KEY (`game`) REFERENCES games(`id`) ON DELETE CASCADE
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
			
			$password = md5(md5('admin'));
			mysqli_query($link, "INSERT INTO users SET login='admin', password='".$password."', `group`='admin'");
			
			$file = fopen('authData.txt', 'a');
			fwrite($file, $_POST['name']);
			fclose($file);

			fclose(fopen("duel.txt", "a"));
		}
		
		// Так же создаём папки execution + testers + visualizers + libs + всё остальное
		
		$dirNames = array("testers", "visualizers", "executions", "testers_bin", "executions_bin", "libs", "includes", "attachments", "logs", "_chroot", "compilelogs");
		$dontRemove = array("libs", "includes");
		foreach ($dirNames as $value)
		{
			if (isset($_POST['destroy']) && $_POST['destroy'] == 1 && !in_array($value, $dontRemove)) removeDir($value);
			@mkdir($value);
			$path = './' . $value . '/.htaccess';
			if (!file_exists($path))
			{
				file_put_contents($path, "Order Deny,Allow\nDeny from all\nAllow from 127.0.0.1\n");
			}
			removeDir("./install.php");
		}
		
		logOff();
		
		echo '<meta http-equiv="refresh" content="0; url=redirectIndex.php?reason=Система установлена! Не забудьте скопировать файлы Execution.lib, execution.h и testlib.h в подкаталоги libs и includes. А так же удалить installCreateDB.php!">';
		exit();
		
	}
?>
<html>
	<body>
<form method="post">
	<center>Создание новой базы данных
			<p>Название 					<input type="text" name="name" value=""></input>	</p>
			<p>Уничтожить имеющуюся БД 		<input type="checkbox" name="destroy" value="1"></input></p>
											<input type="submit" name="submitDB" value="Отправить данные"></input>
	</center>
</form>
</body></html>
