<?php
	include_once('procedures.php');
	$tournamentId = intval($_GET['id']);
	$tournamentData = getTournamentData($tournamentId);
?>
	
	<?php include("top.php"); ?>

	<div class = "container content">
		<div class="titleName">
			<?php echo $tournamentData["name"]; ?>
		</div>
		
		<div class="tournamentMenu">
			<div class="btn-group">
				<button type="button" id="gameButton" class="btn btn-default" onclick="$('#dataContainer').load('game.php?id=<?php echo $tournamentData['game']; ?>&tournament=<?php echo $tournamentId; ?>');">Описание</button>
				<button type="button" id="roundButton" class="btn btn-default" onclick="$('#dataContainer').load('round.php?id=<?php echo $tournamentData['game']; ?>&tournament=<?php echo $tournamentId; ?>')">Раунды</button>
				<?php
					if (isActiveUser())
					{
				?>
					<button type="button" id="stategyButton" class="btn btn-default" onclick="$('#dataContainer').load('userStrategy.php?id=<?php echo $tournamentData['game']; ?>&tournament=<?php echo $tournamentId; ?>');">Стратегии</i></button>
				<?php
					}
				?>
				<?php
					if (isActiveUser() && $tournamentData['state'] == 'running')
					{
				?>
					<button type="button" id="trainingButton" class="btn btn-default" onclick="$('#dataContainer').load('training.php?id=<?php echo $tournamentData['game']; ?>&tournament=<?php echo $tournamentId; ?>');">Тренировка</button>
				<?php
					}
				?>
			</div>
		</div>
	</div>
		
	<div id = "dataContainer" class = "container content">
		
	</div>
	
	<script>
		tournamentData = $.post("jqueryGetCurrentTournamentState.php", function(responceText) 
			{
				getSettings = 'id=<?php echo $tournamentData['game']; ?>&tournament=<?php echo $tournamentId; ?>';
								
				if (responceText != 'null')
				{
					if (responceText.indexOf('duels') != -1)
						$('#dataContainer').load(responceText + '?game=<?php echo $tournamentData['game']; ?>&round=<?php echo $_SESSION['tournamentDuel']?>&tournament=<?php echo $tournamentId; ?>');
					else if (responceText.indexOf('roundResults') != -1)
						$('#dataContainer').load(responceText + '?round=<?php echo $_SESSION['roundResultsRoundId']; ?>');
					else if (responceText.indexOf('roundTable') != -1)
						$('#dataContainer').load(responceText + '?round=<?php echo $_SESSION['roundTableRoundId']; ?>');
					else
						$('#dataContainer').load(responceText + '?' + getSettings);
				}
				else
					$('#dataContainer').load('game.php?' + getSettings);
			}
		);
	</script>
	
	<?php include("bottom.php"); ?>