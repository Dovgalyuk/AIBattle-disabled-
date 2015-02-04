<?php
	ignore_user_abort(true);
	set_time_limit(0);
	$fp = fopen("duel.txt", "r+");
	//$file = fopen("trash.txt", "w+");
	
	if (flock($fp, LOCK_EX))
	{
		include_once('procedures.php');
		$link = getDBConnection();
		if (mysqli_select_db($link, getDBName()))
		{
			//fwrite($file, 'into\n');
			do
			{
				$cont = false;
				$result = mysqli_query($link, "SELECT * FROM duels WHERE status = 'W'");
				while ($row = mysqli_fetch_assoc($result))
				{
					//fwrite($file, 'next\n');
					$round = $row['round'];
					$checker = -1;
					$hasSeed = false;
					// проверяем, есть ли раунд и чекер
					if ($round != -1)
					{
						$chk = mysqli_fetch_assoc(mysqli_query($link, "SELECT checker, seed FROM rounds WHERE id = $round"));
						$checker = $chk['checker'];
						$seed = $chk['seed'];
					}
					else
					{
						// генерируем seed, если раунд не задан
						// TODO
						$seed = 2014;
					}
					if ($round == -1 || $checker == -1)
					{
						// берем defaultChecker
						$strategyTournament = mysqli_result(mysqli_query($link, "SELECT tournament FROM strategies WHERE id = {$row['strategy1']}"), 0);
						$checker = mysqli_result(mysqli_query($link, "SELECT defaultChecker FROM tournaments WHERE id = $strategyTournament"), 0);
					}
					// есть ли seed у чекера
					$hasSeed = mysqli_result(mysqli_query($link, "SELECT hasSeed FROM checkers WHERE id = $checker"), 0);
                    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
                        $cmdLine = "\"./testers_bin/" . $checker . ".exe\" ./executions_bin/" . $row['strategy1'] . ".exe ./executions_bin/" . $row['strategy2'] . ".exe";
                    else
                        $cmdLine = "\"./testers_bin/" . $checker . "\" ./executions_bin/" . $row['strategy1'] . " ./executions_bin/" . $row['strategy2'] . "";

					if ($hasSeed)
						$cmdLine .= " ". $seed;
					
					#echo $cmdLine;
					
					//fwrite($file, $cmdLine . '\n');
					$output = array();
					exec($cmdLine, $output);
					
					$source = current($output);
					$playerId = 0;
					$curStat = "";
					
					$stat = array("OK", "FIELD", "WIN", "TIE", "IM", "TL", "RE", "ML", "IE", "SV", "INPUT");
					
					$testerStat = 'IL';
					$curStat = 'IL';

					while (current($output) !== false)
					{
						foreach ($stat as $part)
						{
							if (strpos($source, $part) === 0)
							{
								if ($part == "OK")
									while (current($output) !== false && current($output) != "END_OF_OUTPUT") 
										$source = next($output);
								else if ($part == "IM")
								{
									$testerStat = $source;
									$curStat = $part;
									$partArr = explode(" ", $source);
									$playerId = intval($partArr[1]);
									while (current($output) !== false && current($output) != "END_OF_OUTPUT") 
										$source = next($output);
								}
								else if ($part == "FIELD")
									while (current($output) !== false && current($output) != "END_OF_FIELD") 
										$source = next($output);
								else if ($part == "INPUT")
									while (current($output) !== false && current($output) != "END_OF_INPUT") 
										$source = next($output);
								else if ($part == "TIE" || $part == "IE") 
								{
									$curStat = $part;
									$testerStat = $source;
								}
								else
								{
									$testerStat = $source;
									$curStat = $part;
									$partArr = explode(" ", $source);
									$playerId = intval($partArr[1]);
								}
								break;
							}
						}
						$source = next($output);
					}
					
					if ($row['round'] != -1)
					{
						if ($curStat == "TIE" || $curStat == "IE")
						{
							$members = array($row['strategy1'], $row['strategy2']);
							foreach ($members as $strategy) setPlayerScore($row['round'], $strategy, ($curStat == "TIE") ? 1 : 0);
						}
						else if ($curStat != "IL")
						{
							$strategy = "";
							if ($curStat == "WIN") 
								if ($playerId == 1) $strategy = $row['strategy1']; else $strategy = $row['strategy2'];
							else
								if ($playerId == 1) $strategy = $row['strategy2']; else $strategy = $row['strategy1'];
							setPlayerScore($row['round'], $strategy, 2);
						}
					}
					$log = fopen("./logs/" . $row['id'] . ".txt", "w");
					fwrite($log, "PLAYERS\n" . getNicknameByStrategy($row['strategy1']) . "\n" . getNicknameByStrategy($row['strategy2']) . "\n");
					foreach ($output as $line)
					{
						fwrite($log, $line . "\n");
					}
					fclose($log);

					mysqli_query($link, "UPDATE duels SET status = '".$testerStat."' WHERE id = " . $row['id']);
					$cont = true;
				}				
			}
			while ($cont);
		}
		
		flock($fp, LOCK_UN);
	}

	fclose($fp);
?>
