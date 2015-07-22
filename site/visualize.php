<?php include_once('procedures.php');?>
<html>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<head>
		<script src="js/jquery-1.10.2.min.js"></script>
	</head>
	<body>
		<div class = "container content">
		<center>
		<?php
			$duel = intval($_GET['duel']);
			$gameId = getGameByDuel($duel);
			if (getVisualizerByGame($gameId) && isActiveUserHasAccessToDuel($duel))
			{
				$file = file("./logs/$duel.txt");
				$log = "";
				$s = current($file);
				while ($s !== false)
				{
					if (strpos($s, "OK") === 0 || strpos($s, "IM") === 0)
					{
						while ($s !== false && $s != "END_OF_OUTPUT\n") 
							$s = next($file);
					}
					else if ($s == "FIELD\n")
					{
						$s = next($file);
						while ($s !== false && $s != "END_OF_FIELD\n") 
						{
							$log .= trim($s) . "\\n";
							$s = next($file);
						}
					}
					else if (strpos($s, "INPUT") === 0)
					{
						while ($s !== false && $s != "END_OF_INPUT\n") 
							$s = next($file);
					}
					$s = next($file);
				}
				
				
				$link = getDBConnection();
						
				if (mysqli_select_db($link, getDBName()))
				{	
					$res = mysqli_query($link, "SELECT strategy1, strategy2, duels.status FROM duels WHERE duels.id=$duel");
					$game = getGameName($gameId);
					$user1 = getNicknameByStrategy(mysqli_result($res, 0, 0)); 
					$user2 = getNicknameByStrategy(mysqli_result($res, 0, 1));
					$status = mysqli_result($res, 0, 2);
				}
		?>

			<h2>Партия по игре <?php echo $game;?> между <?php echo $user1;?> и <?php echo $user2;?><div id = "hiddenScore"><?php echo $status;?></div></h2>
			<h3 id = "scoreDiv"></h3>
			<script>
				window.onload = function()
				{	startup(
						Math.max(document.body.clientWidth, 300),
						Math.max(document.body.clientHeight - 150, 300)
					);
				};
				window.onresize = function()
				{
					resize(
						Math.max(document.body.clientWidth, 300),
						Math.max(document.body.clientHeight - 150, 300)
					);
				};
				<?php include_once("./visualizers/$gameId"); ?>
			</script>
			
			<canvas id="cv"></canvas>
			<br>
			<a href="javascript:play_reset();">Сброс</a>
			&nbsp;|&nbsp;
			<a href="javascript:play_start();">Старт</a>
			&nbsp;|&nbsp;
			<a href="javascript:play_stop();">Стоп</a>
			<br>
			<a href="javascript:stepPrev();">Предыдущий ход</a>
			&nbsp;|&nbsp;
			<a href="javascript:stepNext();">Следующий ход</a>
			
		<?php
			}
		?>

		</center>
		</div>
	</body>
</html>
