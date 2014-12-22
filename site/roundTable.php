<?php
	include_once('procedures.php');
	$roundId = intval($_GET['round']);
	
	$_SESSION['tournamentState'] 	= 'roundTable.php';
	$_SESSION['roundTableRoundId'] 	= $roundId;
	
	$data = getRoundData($roundId);
	
	if ($data['visible'] || isAdmin())
	{
?>
	<div class = "centeredText">
		<h2>Раунд "<?php echo $data['name']; ?>" игры "<?php echo $data['gameName']; ?>" от <?php echo $data['date']; ?></h2>
	</div>
	
	<?php
		$users = array();
		$names = array();
		$scores = array();
		$result = getRoundTable($roundId);
		foreach ($result as $row)
		{
			$users[] = $row['id'];
			$names[] = $row['name'];
			$scores[] = $row['score'];
		}
	?>
	
	<table class = "table table-bordered">
		<tr align = center>
			<td></td>
			<?php
			foreach ($names as $name)
			{
			?>
				<td><?php echo $name; ?></td>
			<?php
			}
			?>
			<td>Счет</td>
		</tr>
		<?php
			//$activeUser = getActiveUser();
			for ($i = 0 ; $i < count($names) ; ++$i)
			{
		?>
			<tr align = center>
				<td><?php echo $names[$i]; ?></td>
				<?php
					$user1 = $users[$i];
					for ($j = 0 ; $j < count($users) ; ++$j)
					{
						$val = 0;
						if ($i != $j)
						{
							$user2 = $users[$j];
							$val = 0;
							$duel = getDuelStatus($roundId, $user1, $user2);
				?>
							<td>
								<?php
									$res = $duel['status'];
									if ($res == "TIE")
										$val = 1;
									else if ($res == "WIN 1" || $res == "IM 2" || $res == "RE 2" || $res == "TL 2" || $res == "ML 2")
										$val = 2;
								?>
									<a title="<?php echo $res;?>" 
										href="visualize.php?duel=<?php echo $duel['id'];?>">
										<?php echo $val;?>
									</a>
								
								<?php
									echo " / ";
									$val = 0;
									$duel = getDuelStatus($roundId, $user2, $user1);
									$res = $duel['status'];
									if ($res == "TIE")
										$val = 1;
									else if ($res == "WIN 2" || $res == "IM 1" || $res == "RE 1" || $res == "TL 1" || $res == "ML 1")
										$val = 2;
								?>
									<a title="<?php echo $res;?>"
										href="visualize.php?duel=<?php echo $duel['id'];?>">
										<?php echo $val;?>
									</a>
							</td>
				<?php
						}
						else
						{
				?>
						<td></td>
				<?php	
						}
					}
				?>
				<td><?php echo $scores[$i]; ?></td>
			</tr>
		<?php
			}
		?>
	</table>
<?php
	}
?>
	<button type="button" class="btn btn-link" onclick="$('#dataContainer').load('roundResults.php?round=<?php echo $roundId;?>')">Назад</button>
