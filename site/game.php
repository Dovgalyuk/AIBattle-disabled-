<?php include_once('procedures.php'); ?>

<script>
	changeActiveTournamentButton('gameButton');
</script>

<?php
	$id = intval($_GET['id']);
	$tournamentId = -1;
	if (isset($tournamentId))
		$tournamentId = intval($_GET['tournament']);
		
	$_SESSION['tournamentState'] = 'game.php';
	if ($tournamentId != -1)
	{
 ?>
	<h2>Описание турнира</h2>
	<div class="textSettings"><?php echo getTournamentDescription($tournamentId); ?></div>
<?php
	}
?>
<br>
<h2>Описание игры</h2>
<div class="textSettings"><?php echo getGameDescription($id); ?></div>
<br>

<div>
	<h2>Ссылки</h2>
	<ul>
		<?php
			$attachments = getGameAttachments($id);
			foreach($attachments as $key => $value)
			{
				$description 	= $value['description'];
				$originalName 	= $value['originalName'];
				
				echo $description.":";
		?>
				<div class = "attachmentDiv">
					<li class="download"><span class="glyphicon glyphicon-download"></span>
						<a href="downloadAttachment.php?file=<?php echo $key;?>"><?php echo $originalName;?></a>
					</li>
				</div
				<br>
		<?php
			}
		?>
	</ul>
</div>