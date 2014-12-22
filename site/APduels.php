<?php 
	include_once('procedures.php');
	
	$_SESSION['adminPanelState'] = 'APduels.php';

	if (isAdmin())
	{
	
		$tournamentId = -1;
		if (isset($_GET['tournament']))
			$tournamentId = intval($_GET['tournament']);
			
		$_SESSION['adminTournamentId'] = $tournamentId;
			
		$roundId = -1;
		if (isset($_GET['round']))
			$roundId = intval($_GET['round']);
			
		$_SESSION['adminRoundId'] = $roundId;
		
		$tournamentList = getTournamentList();
		if ($tournamentId != -1)
			$roundList = getRoundList($tournamentId);
		
		$gameId = getGameByTournament($tournamentId);
		$duels = getDuels($roundId, $gameId, getActiveUser(), $tournamentId);
		$visual = @hasVisualizer($gameId);
?>

	<script>
		changeActiveAdminButton('duelsButton');
	</script>

	<script>
		function loadRoundData(tournamentId, roundId)
		{
			$('#dataContainer').load('APduels.php?tournament=' + tournamentId + '&round=' + roundId);
		}

		function getSelectorValue(selector)
		{
			var selector = document.getElementById(selector);
			return selector.options[selector.selectedIndex].value;
		}
		
		function getSelectedTournament()
		{
			return getSelectorValue('tournamentSelector');
		}
		
		function getSelectedRound()
		{
			return getSelectorValue('roundSelector');
		}
		
		function setSelectedIndexInSelector(selector, selectedData)
		{
			var selector = document.getElementById(selector); 
			if (selectedData != -1)
			{
				for (var i=0; i < selector.options.length; i++)
				{
					if (selector.options[i].value == selectedData)
					{
						selector.selectedIndex = i;
						break;
					}
				}
			}
		}
	</script>

	<form role="form" method="post">
		<div class="form-group">
			<label for = "tournamentSelector" class = "APfont">Турнир:</label>
			<select id = "tournamentSelector" class="form-control" onchange="loadRoundData(getSelectedTournament(), -1)">
				<option value = "-1">Турнир не выбран</option>
				<?php
					foreach ($tournamentList as $tournament)
					{
				?>
					<option value = "<?php echo $tournament['id']; ?>">
						<?php echo $tournament['name']; ?>
					</option>
				<?php
					}
				?>
			</select>
			<script>
				setSelectedIndexInSelector('tournamentSelector', <?php echo $tournamentId; ?>);
			</script>
		</div>
		<br>
		<div class="form-group">
			<label for = "roundSelector" class = "APfont">Раунд:</label>
			<select id = "roundSelector" class="form-control" onchange="loadRoundData(getSelectedTournament(), getSelectedRound())">
				<option value = "-1">Тренировочные бои</option>
				<?php
					foreach ($roundList as $round)
					{
				?>
					<option value = "<?php echo $round['id']; ?>">
						<?php echo $round['name']; ?>
					</option>
				<?php
					}
				?>
			</select>
			<script>
				setSelectedIndexInSelector('roundSelector', <?php echo $roundId; ?>);
			</script>
		</div>
	</form>

	<table class = "table table-bordered">
		<tr align = center>
			<tr class = "info" align = center>
			<td>Игрок 1</td>
			<td>Игрок 2</td>
			<td>Результат</td>
			<?php if ($visual)
				{
			?>
				<td>Визуализатор</td>
			<?php
				}
			?>
		</tr>
		
			<?php
			
			//while ($row = mysqli_fetch_assoc($duels))
			foreach ($duels as $row)
			{
		?>
			<tr align = center>
				<td><?php echo getNicknameById($row['user1'])." ({$row['strategy1']})";?></td>
				<td><?php echo getNicknameById($row['user2'])." ({$row['strategy2']})";?></td>
				<td>
					<?php
						$status = $row['status'];
						if ($status != 'W')
						{
					?>
						<div class = "attachmentDiv">
							<li class="download"><span class="glyphicon glyphicon-download"></span>
								<a href="getLog.php?duel=<?php echo $row['id'];?>"><?php echo $status;?></a>
							</li>
						</div>
				
					<?php
						}
						else echo "Waiting...";
					?>
				</td>
				<?php
					if ($visual)
					{
				?>
					<td>
						<?php
							// допилить
							if ($status != 'W')
							{
						?>
							<button class = "btn btn-info" onclick = "window.open('visualize.php?duel=<?php echo $row['id']; ?>', 'Vizualizer', 'width=600,height=650,resizable=yes,scrollbars=yes'); return false;">Просмотреть</button>
						<?php
							}
								//echo "<a href='visualize.php?duel=".$row['id']."'>Просмотреть</a>"; 
						?>
					</td>
				<?php
					}
				?>
			</tr>
		<?php
			}
		?>
		
	</table>
	
	<?php
		}
		else
		{
	?>
		<p>Тебя не должно быть здесь!</p>
	<?php
		}
	?>