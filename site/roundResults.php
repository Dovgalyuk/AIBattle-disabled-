<?php
	include_once('procedures.php');
	$roundId = intval($_GET['round']);
	
	$_SESSION['tournamentState'] = 'roundResults.php';
	$_SESSION['roundResultsRoundId'] = $roundId;
	
	$data = getRoundData($roundId);
	$gameId = $data['game'];
?>

<script>
	function loadRoundTable(roundId)
	{
		$('#dataContainer').load('roundTable.php?round=' + roundId);
	}
	
	function loadPlayersDuel(roundId, gameId, tournamentId)
	{
		$('#dataContainer').load('duels.php?game=' + gameId + '&round=' + roundId + '&tournament=' + tournamentId);
	}
</script>

	<div class = "centeredText">
	<h2>Раунд "<?php echo $data['name']; ?>" игры "<?php echo $data['gameName']; ?>" от <?php echo $data['date']; ?></h2>
	</div>

			<h3>Результаты раунда</h3>
			<table class = "table table-bordered">
				<tr align = center>
					<td>Пользователь</td>
					<td>Счет</td>
				</tr>
				<?php
                $result = getUsersRoundScores($roundId);
				foreach ($result as $row)
				{
				?>
					<tr align = center>
                    <td>
                    <?php
                    if (isAdmin())
                    {
                        echo '<b><a href="userProfile.php?id='.$row['id'].'">'.$row['name'].'</a></b> (';
                        echo getUserSurname($row['id']), ' ', getUserRealName($row['id']), ' ', getUserPatronymic($row['id']);
                        echo ')';
                    }
                    else
                        echo $row['name'];
                    ?></td>
						<td><?php echo $row['score']; ?></td>
					</tr>
				<?php
					}
				?>
			</table>
			<br>
			<h3>Турнирная таблица</h3>
			<a href="javascript:loadRoundTable(<?php echo $roundId;?>)">Посмотреть</a>
<?php
		if (getActiveUserID() != -1)
		{
?>
			<br>
<?php
			if (isAdmin())
				echo "<h3>Все партии в раунде</h3>";
			else
				echo "<h3>Свои партии в раунде</h3>";
?>
			<a href="javascript:loadPlayersDuel(<?php echo $roundId;?>, <?php echo $gameId;?>, <?php echo $data['tournament']; ?>)">Посмотреть</a>
<?php
		}
		if (isAdmin() && $roundId != -1)
		{
?>
			<br>
			<h3>Визуализация хода раунда</h3>
			<a href="visualRound.php?round=<?php echo $roundId; ?>">Посмотреть</a>
<?php
		}
?>
