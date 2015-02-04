<?php
	include_once('procedures.php');
	
	$_SESSION['tournamentState'] = 'round.php';	
	$gameId = intval($_GET['id']);
	$tournamentId = intval($_GET['tournament']);
	
	$rounds = getTournamentRounds($tournamentId);
?>

<script>
	changeActiveTournamentButton('roundButton');
</script>

<script>
	function startRound(roundId)
	{
		$.post("startRound.php", {'roundId' : roundId});
	}

	function loadRoundResults(roundId)
	{
		$('#dataContainer').load('roundResults.php?round=' + roundId);
	}
	
	function loadTrainingDuel(gameId, tournamentId)
	{
		$('#dataContainer').load('duels.php?game=' + gameId + '&tournament=' + tournamentId);
	}
</script>

<h3>Раунды</h3>
<ul>
	<?php
		foreach ($rounds as $round)
		{
			if ($round['visible'] || isAdmin())
			{
	?>
			<li>
            <a href="javaScript:loadRoundResults(<?php echo $round['id']; ?>)">Раунд "<?php echo $round['name']; ?>" (<?php echo $round['date']; ?>)</a><?php if (!$round['visible']) echo " <b>(скрытый)</b>"; ?>
			</li>
	<?php
			}
		}
	?>
	<?php
		if (getActiveUserID() != -1)
		{
	?>
		<li>
			<a href="javascript:loadTrainingDuel(<?php echo $gameId;?>, <?php echo $tournamentId; ?>)">Партии вне раундов</a>
		</li>
	<?php
		}
	?>
<ul>
