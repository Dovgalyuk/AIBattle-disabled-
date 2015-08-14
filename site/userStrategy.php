<?php include_once('procedures.php'); ?>

<script>
	changeActiveTournamentButton('stategyButton');
</script>

<script>
	function updateACTStrategy(strategyId, gameId, tournamentId)
	{
		$.post("jqueryUserStrategySetActStatus.php", {'strategyId' : strategyId, 'gameId' : gameId, 'tournamentId' : tournamentId});
		$('#dataContainer').load('userStrategy.php?id=' + gameId + '&tournament=' + tournamentId);
	}
	
	function downloadUserStrategy(fileInputName, gameId, tournamentId)
	{	
		var form = new FormData();
		
		form.append('gameId', gameId);
		form.append(fileInputName, $('#' + fileInputName)[0].files[0]);
		form.append('formInputName', fileInputName);
		form.append('tournamentId', tournamentId);
				
		$.ajax({
			url: 'jqueryUserStrategyDownloadStrategy.php',
			type: 'POST',
			success: function ()
			{
				$('#dataContainer').load('userStrategy.php?id=' + gameId + '&tournament=' + tournamentId);
			},
			data: form,
			cache: false,
			contentType: false,
			processData: false
        });
	}
</script> 
<?php
    $id = intval($_GET['id']);
    $tournamentId = intval($_GET['tournament']);
    $tournamentData = getTournamentData($tournamentId);
    $tournamentRunning =  $tournamentData['state'] == 'running';
    $_SESSION['tournamentState'] = 'userStrategy.php';	
    $user = (isAdmin() && isset($_GET['uid']))
                ? intval($_GET['uid'])
                : getActiveUserID();
    $strategies = getUserStrategies($id, $user, $tournamentId, false);
    $page = isset($_GET['page'])
                ? intval($_GET['page'])
                : intval((sizeof($strategies) - 1) / 10);
    if ($page < 0)
        $page = 1;
    if ($page > (sizeof($strategies) / 10))
        $page = sizeof($strategies) / 10;
?>
<?php
    if (getActiveUserID() == $user && $tournamentRunning)
    {
?>
<h2>Добавить новую стратегию</h2>
<form role="form" id="strategyDownloadForm" enctype="multipart/form-data" method="post">
	<div class="form-group">
		<label for="uploadGameFile">Загрузка стратегии:</label>
		<input type="file" id="uploadGameFile">
	</div>
	<button type="submit" class="btn btn-default" onclick = "downloadUserStrategy(<?php echo '\'uploadGameFile\''.','.$id.','.$tournamentId; ?>); return false;">Загрузить</button>
</form>
<?php
    }
?>

<h2>Загруженные стратегии</h2>
<?php
    if (isAdmin())
    {
?>
        <h4>Пользователь:</h4>
        <select id="userSelect" class="form-control" onchange="$('#dataContainer').load('./userStrategy.php?id=<?php
          echo $id; ?>&tournament=<?php echo $tournamentId;?>&uid='+$('#userSelect').val());">
        <option
<?php
            if (getActiveUserID() == $user)
            {
?>
            selected
<?php
            }
?>
            value="<?php echo getActiveUserID(); ?>">
            <?php echo getNicknameById($user); ?>
        </option>
<?php
        $users = getUsersWithStrategies($tournamentId);
        foreach ($users as $field)
        {
            if ($field['id'] != $user)
                echo '<option '.(($field['id'] == $user) ? 'selected ' : '')
                     .'value='.strval($field['id']).'>'
                     .$field['login'].'</option>';
        }
?>
</select>
<?php
    }
?>
<?php
    if (sizeof($strategies) > 0)
    {
?>
<h4>Страница: </h4>
<div class="btn-group">
<?php
    $cur = 1;
    for ($i = sizeof($strategies) ; $i > 0 ; $i -= 10) {
        echo '<button type="button" class="btn '.($page == intval($i / 10) ? 'btn-info' : 'btn-default').'" \
            onclick="$(\'#dataContainer\').load(\'userStrategy.php?id='.$id.'&tournament='.$tournamentId.
            '&page='.strval(intval($i / 10)).'&uid='.$user.'\');">'.$cur.'</button>';
        ++$cur;
    }
?>
  </div>
<?php
if (!isset($_GET['page']))
{
?>
    <script type="text/javascript">
        $('#dataContainer').load('userStrategy.php?id=<?php echo $id;?>&tournament=<?php 
            echo $tournamentId; ?>&page=<?php echo strval(intval(sizeof($strategies) / 10));?>&uid='+$('#userSelect').val()');
    </script>
<?php
};
?>
<br /><br />
<table class="table table-bordered">
    <tr>
        <td align=center>ID</td>
        <td align=center>Статус</td>
        <td align=center></td>
<?php 
        if (getActiveUserID() == $user && $tournamentRunning)
        { 
?>
            <td align=center>Изменение активной стратегии</td>
<?php
        }
?>
	</tr>
<?php
        $strategies2 = getUserStrategies($id, $user, $tournamentId, false, (intval(sizeof($strategies) / 10) - $page) * 10, 10);
        foreach ($strategies2 as $value)
        {
            $key = $value['id'];
?>
		<tr <?php echo getStrategyStateColor($value['status']);?>>
			<td align=center><?php echo $key;?></td>
			<td align=center><?php echo getStrategyStatusRusTip($value['status']); ?></td>
			<td>
				<div id="source<?php echo $key; ?>">
                    <a target="_blank" href="getSource.php?id=<?php echo $key; ?>">Показать код</a>
<?php
            if ($value['status'] == 'CE')
            {
                echo ' / <a target="_blank" href="getCompileLog.php?id=' . $key . '">Показать лог компиляции</a>';
            }
?>
				</div>
            </td>
<?php
    if (getActiveUserID() == $user && $tournamentRunning)
    {
?>
			<td align=center>
				<?php 
					if ($value['status'] == "OK")
					{
				?>
					<div class = "sendStrategyDiv">
						<form method=post>
							<button type="button" class="btn btn-primary" onclick = "updateACTStrategy(<?php echo $key.','.$id.','.$tournamentId; ?>); return false;">Сделать текущей</button>
						</form>
					</div>
				<?php
					}
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
    } else {
?>
        <h1 style="color:blue;" align="center">Нет загруженных стратегий</h1>
<?php
    }
?>
