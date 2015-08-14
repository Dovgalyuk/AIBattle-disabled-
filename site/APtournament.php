<?php
	include_once('procedures.php');
	
	$_SESSION['adminPanelState'] = 'APtournament.php';
	
	if (isAdmin())
	{
	
		$tournamentId 	= -1;
		$gameId 		= -1;
		if (isset($_GET['tournament']))
			$tournamentId = intval($_GET['tournament']);
			
		$_SESSION['adminTournamentId'] = $tournamentId;
			
		$tournamentList 	= getTournamentList();
		$currentTournament 	= getTournamentList($tournamentId);
		$gameList 			= getGameList();
		
		if ($tournamentId != -1)
			$checkerList = getCheckerListByGameId($currentTournament['game']);
?>

	<script>
		changeActiveAdminButton('tournamentButton');
	</script>

	<script>
		function loadTournamentData(tournamentId)
		{
			$('#dataContainer').load('APtournament.php?tournament=' + tournamentId);
		}
		
		function setSelectedIndexInSelector(selector, selectedData)
		{
			var selector = document.getElementById(selector); 
			if (selectedData != -1)
			{
				for (var i = 0; i < selector.options.length; i++)
				{
					if (selector.options[i].value == selectedData)
					{
						selector.selectedIndex = i;
						break;
					}
				}
			}
		}
		
		function getSelectorValue(selector)
		{
			var selector = document.getElementById(selector);
			if (selector)
				return selector.options[selector.selectedIndex].value
			else 
				return -1;
		}
		
		function getSelectedTournament()
		{
			return getSelectorValue('tournamentSelector');
		}
		
		function getSelectedGame()
		{
			return getSelectorValue('gameSelector');
		}
		
		function getSelectedChecker()
		{
			return getSelectorValue('checkerSelector') ;
		}
		
		function getSelectedTournamentState()
		{
			return getSelectorValue('tournamentState');
		}
		
		function loadFormData()
		{	
			var tournamentSelectorValue = getSelectedTournament();
			var tournamentName = document.getElementById('tournamentName').value;
			var gameSelectorValue = getSelectedGame();
			var tournamentStateSelectorValue = getSelectedTournamentState();
			var checker = getSelectedChecker();
			var textArea = CKEDITOR.instances.tournamentDescription.getData();
			
			if (tournamentName != '')
			{
			
				var dataTournament = 
				{		'tournamentSelector' : tournamentSelectorValue, 
						'tournamentName' : tournamentName, 
						'gameSelector' : gameSelectorValue, 
						'tournamentDescription' : textArea,
						'tournamentState' : tournamentStateSelectorValue,
						'defaultChecker' : checker,
				};
				
				if (tournamentSelectorValue == -1)
					dataTournament['createTournament'] = true;
				else
					dataTournament['updateTournament'] = true;
				
				$.post
				(	
					"jqueryTournament.php",
					dataTournament,
					function (data)
					{
						showModalAlert(data);
						window.location.reload(); 
					}
				);
			}
			else showModalAlert('Название турнира не должно быть пустым!');
		}
		
		function deleteTournament()
		{
		
			var tournamentId = getSelectedTournament();
		
			$.post
			(
				"jqueryTournament.php",
				{
					'tournamentId' 		: tournamentId,
					'deleteTournament' 	: true
				},
				function (data)
				{
					showModalAlert(data);
					window.location.reload(); 
				}
			);
		}
		
		function changeChecker()
		{
			var gameId = getSelectedGame();
			var p = parseInt(<?php if ($tournamentId != -1) echo $currentTournament['defaultChecker']; else echo -1; ?>);
			
			$.post
			(
				'jqueryGetCheckerList.php',
				{'gameId' : gameId},
				function (data)
				{
					var selectData = JSON.parse(data);
					var list = document.getElementById('checkerSelector');
					
					$("#checkerSelector").empty();
					
					for (var i = 0; i < selectData.length; ++i)
					{
						list.options[i] = new Option(selectData[i].name, selectData[i].id, false, p == selectData[i].id);
					}
				}
			);
		}
	</script>

		<form role="form" method="post">
			<div class="form-group">
				<label for = "tournamentSelector" class = "APfont">Турнир:</label>
				<select id = "tournamentSelector" class="form-control" onchange="loadTournamentData(getSelectedTournament())">
					<option value = "-1">Новый турнир</option>
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
				<label for="tournamentName" class = "APfont">Название турнира:</label>
				<input type="text" class="form-control" id="tournamentName" placeholder="Введите название турнира" value = "<?php if ($tournamentId != -1) echo $currentTournament['name']; ?>">
			</div>
			<br>
			<div class="form-group">
				<label for = "gameSelector" class = "APfont">Игра:</label>
				<select id = "gameSelector" class="form-control" onchange = "changeChecker()">
					<?php
						foreach ($gameList as $game)
						{
					?>
						<option value = "<?php echo $game['id']; ?>">
							<?php echo $game['name']; ?>
						</option>
					<?php
						}
					?>
				</select>
				<script>
					setSelectedIndexInSelector('gameSelector', <?php if ($tournamentId != -1) echo $currentTournament['game']; else echo -1; ?>);
				</script>
			</div>
			<br>
			<div class="form-group">
				<label for = "checkerSelector" class = "APfont">Тестировщик по умолчанию:</label>
				<select id = "checkerSelector" class="form-control">
				</select>
			</div>
			<script>
				changeChecker();
			</script>
			<br>
			<div class="form-group">
				<label for="tournamentDescription" class = "APfont">Описание турнира:</label>
				<textarea id = "tournamentDescription" class="form-control" rows="3"><?php if ($tournamentId != -1) echo trim($currentTournament['description']);?></textarea>
				<script>
					CKEDITOR.replace('tournamentDescription');
				</script>
			</div>
			<br>
			<div class="btn-group">
				<button class = "btn btn-info" onclick = "window.open('imageUploader.php', 	'Uploader', 'width=800,height=330,resizable=yes,scrollbars=yes'); return false;">Загрузка изображений</button>
				<button class = "btn btn-info" onclick = "window.open('imageViewer.php', 	'Viewer', 	'width=800,height=330,resizable=yes,scrollbars=yes'); return false;">Просмотр изображений</button>
			</div>
			<br>
			<br>
			<div class="form-group">
				<label for="tournamentState" class = "APfont">Статус турнира:</label>
				<select id = "tournamentState" class="form-control">
					<option value = "preparing">Подготовка к открытию</option>
					<option value = "running">Открыт</option>
					<option value = "closed">Закрыт</option>
				</select>
				<script>
					setSelectedIndexInSelector('tournamentState', '<?php echo $currentTournament['state']; ?>');
				</script>
			</div>
			<br>
			<div class="btn-group">
				<button type = "submit" name = "submit" onclick = "loadFormData(); return false;" class = "btn btn-default">
					<?php
						if ($tournamentId == -1)
							echo 'Создать турнир';
						else
							echo 'Применить изменения';
					?>
				</button>
				<?php
					if ($tournamentId != -1)
					{
				?>
				<button type = "submit" name = "submit" onclick = "deleteTournament(); return false;" class = "btn btn-default">Удалить турнир</button>
				<?php
					}
				?>
			</div>
		</form>
	<?php
		}
		else
		{
	?>
		<p>Тебя не должно быть здесь!</p>
	<?php
		}
	?>
