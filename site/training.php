<?php include_once('procedures.php');?>

<script>
	changeActiveTournamentButton('trainingButton');
</script>

<script>
	function duel(gameId, strategyId, tournamentId)
	{
		$.post("jqueryRunDuel.php", {'gameId' : gameId, 'tournamentId' : tournamentId, 'strategyId' : strategyId});
		$('#dataContainer').load('duels.php?game=' + gameId + '&tournament=' + tournamentId);
	}
	function loadTrainingDuel(gameId, tournamentId)
	{
		$('#dataContainer').load('duels.php?game=' + gameId + '&tournament=' + tournamentId);
	}
</script>


<?php 
    $_SESSION['tournamentState'] = 'training.php';	
    $tournamentId = intval($_GET['tournament']);
    $gameId = intval($_GET['id']);
?>
<?php
	if (getActiveUser() != -1)
	{
?>
	        <h3>Тренировочные партии</h3>
		<a href="javascript:loadTrainingDuel(<?php echo $gameId;?>, <?php echo $tournamentId; ?>)">Посмотреть</a>
<?php
	}
?>
<?php
    if (getCurrentStrategy(getActiveUser(), $tournamentId) != null) 
    {
?>
        <h3>Сыграть с участниками</h3>
        <table class="table table-bordered">
<?php
            $users = getUsersList(true);
            foreach ($users as $user)
            {
?>
<?php
                $strategies = getUserStrategies($gameId, $user['id'], $tournamentId, true);
                if (!empty($strategies))
                {
?>
                    <tr align=center>
                        <td><b><?php echo $user['login']; ?></b><br />
                            <select class="form-control" id="strategy-<?php echo $user['id']; ?>">
<?php
                                foreach ($strategies as $str)
                                {
                                     $act = $str['status'] == 'ACT';
                                     echo '<option value="'.$str['id'].'" '.($act ? 'selected' : '').'>';
                                     if ($act)
                                         echo 'Активная стратегия '.$str['id'];
                                     else
                                         echo 'Стратегия '.$str['id'];
                                     echo '</option>';
                                }
?>
                            </select>
                        </td>
                        <td>
                            <form method=post>
                                <button class="btn btn-primary" 
                                        onclick = "duel(<?php echo $gameId.", "; ?>parseInt($('#strategy-<?php echo $user['id']; ?>').val())<?php echo ', '.$tournamentId; ?>); return false;">
                                    Сыграть
                                </button>
                            </form>
                        </td>
                    </tr>
<?php
                }
?>
<?php
            }
?>
<?php
        }
        else
        {
?>
            <div>
                <h3 class = "redColored centeredText">Загрузите стратегию для участия в турнире</h3>
            </div>
<?php
        }
?>
</table>
