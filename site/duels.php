<?php
    include_once("procedures.php");

    if ($_GET['round'])
        $roundId = intval($_GET['round']);
    else
        $roundId = -1;
	
    $_SESSION['tournamentState'] = 'duels.php';

    $_SESSION['tournamentDuel'] = $roundId;
	
    $gameId = intval($_GET['game']);
    $userId = (isAdmin() && isset($_GET['uid'])) ? intval($_GET['id']) : getActiveUserID();
    $page = isset($_GET['page']) ? intval($_GET['page']) : 0;
    $visual = getVisualizerByGame($gameId);
    $tournamentId = intval($_GET['tournament']);
	
    $duels = getDuels($roundId, $gameId, $userId, $tournamentId, $page * 10, 10);
?>

<h3><?php echo getDuelHeader($roundId, $gameId);?></h3>
Страница:
<br />
<div class="btn-group">
<?php
    $limit = getDuelsCount($roundId, $gameId, $userId, $tournamentId);
    $skipped = false;
    for ($i = 0; $i < $limit; $i += 10)
    {
        $p = intval($i / 10);
        if ((abs($p - $page) <= 2) || ($i == 0) || (($i + 10) >= $limit))
        {
            $skipped = false;
            echo '<button class="btn btn-'. ($p == $page ? 'info' : 'default').'" onclick="$(\'#dataContainer\').load(\'duels.php?game='.$gameId.'&tournament='.$tournamentId.'&page='.strval($p).(($roundId != -1) ? '&round='.$roundId : '').'\')">'.strval($p + 1).'</button>';
        }
        else
        {
            if (!$skipped)
                echo '</div><span style="font-size:24px"> ... </span><div class="btn-group">';
            $skipped = true;
        }
    }
?>
</div>
<br /><br />
<table class="table table-bordered">
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
	if ($gameId != -1 && $roundId != -1)
	{
?>
	<button type="button" class="btn btn-link" onclick="$('#dataContainer').load('roundResults.php?round=<?php echo $roundId;?>')">Назад</button>
<?php
	}
?>
<script type="text/javascript">
    window.scroll(0, 0);
</script>
