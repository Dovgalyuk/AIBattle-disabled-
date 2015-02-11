<?php
	include_once('procedures.php');
	
	$_SESSION['adminPanelState'] = 'APgame.php';
	
	if (isAdmin())
	{
	
		$gameId 		= -1;
		if (isset($_GET['game']))
			$gameId = intval($_GET['game']);
			
		$_SESSION['adminGameId'] = $gameId;
			
		$gameList 		= getGameList();
		$currentGame 	= getGameList($gameId);
		$vizualize		= @getVisualizerByGame($gameId);
?>

	<script>
		changeActiveAdminButton('gameButton');
	</script>

	<script>
		function loadGameData(gameId)
		{
			$('#dataContainer').load('APgame.php?game=' + gameId);
		}

		function loadFormData()
		{
			var gameSelector = document.getElementById('gameSelector');
			var gameSelectorValue = gameSelector.options[gameSelector.selectedIndex].value;
			
			var gameName = document.getElementById('gameName').value;
			var gameDescription = CKEDITOR.instances.gameDescription.getData();
			var timeLimit = document.getElementById('gameTurnTime').value;
			var memoryLimit = document.getElementById('gameMemoryLimit').value;
			
			if (gameName != '')
			{
			
				var form = new FormData();
				form.append('gameId', gameSelectorValue);
				form.append('gameName', gameName);
				form.append('gameDescription', gameDescription);
				form.append('timeLimit', timeLimit);
				form.append('memoryLimit', memoryLimit);
				
				if ($('#uploadVisualizerFile')[0].files[0])
				{
					form.append('visualizerPath', 'uploadVisualizerFile');
					form.append('uploadVisualizerFile', $('#uploadVisualizerFile')[0].files[0]);
				}
				form.append(gameSelectorValue == -1 ? 'createGame' : 'updateGame', true);
				
				$.ajax({
					url: 'jqueryGame.php',
					type: 'POST',
					success: function (data)
					{	
						showModalAlert(data);
						loadGameData(gameSelectorValue);
					},
					data: form,
					cache: false,
					contentType: false,
					processData: false
				});
			}
			else showModalAlert('Название игры не может быть пустым!');
		}
		
		function deleteVisualizer()
		{
			var gameSelector = document.getElementById('gameSelector');
			var gameSelectorValue = gameSelector.options[gameSelector.selectedIndex].value;
			
			$.post
			(
				"jqueryGame.php",
				{
					'gameId' 			: gameSelectorValue,
					'deleteVisualizer' 	: true
				},
				function (data)
				{
					showModalAlert(data);
					loadGameData(gameSelectorValue);
				}
			);
		}
		
		function deleteGame()
		{
			var gameSelector = document.getElementById('gameSelector');
			var gameSelectorValue = gameSelector.options[gameSelector.selectedIndex].value;
			
			$.post
			(
				"jqueryGame.php",
				{
					'gameId' 		: gameSelectorValue,
					'deleteGame' 	: true
				},
				function (data)
				{
					showModalAlert(data);
					loadGameData(-1);
				}
			);
		}

		function setSelectedIndexInSelector(selector, selectedData)
		{
			var selector = document.getElementById(selector); 
			if (selectedData != -1)
			{
				for (var i=0; i < selector.options.length; i++)
				{
					console.log(selector.options[i].value);
					if (selector.options[i].value == selectedData)
					{
						selector.selectedIndex = i;
						break;
					}
				}
			}
		}
		
		function getSelectedGame()
		{
			var selector = document.getElementById('gameSelector');
			return selector.options[selector.selectedIndex].value;
		}
	</script>

		<form role="form" method="post" enctype="multipart/form-data">
			<div class="form-group">
				<label for = "gameSelector" class = "APfont">Игра:</label>
				<select id = "gameSelector" class="form-control" onchange="loadGameData(getSelectedGame())">
					<option value = "-1">Новая игра</option>
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
					setSelectedIndexInSelector('gameSelector', <?php echo $gameId; ?>);
				</script>
			</div>
			<br>
			<div class="form-group">
				<label for="gameName" class = "APfont">Название игры:</label>
				<input type="text" class="form-control" id="gameName" placeholder="Введите название игры" value = "<?php if ($gameId != -1) echo $currentGame[0]['name']; ?>">
			</div>
			<br>
			<div class="form-group">
				<label for="gameDescription" class = "APfont">Описание игры:</label>
				<textarea id = "gameDescription" class="form-control" rows="3"><?php if ($gameId != -1) echo trim($currentGame[0]['description']);?></textarea>
				<script>
					CKEDITOR.replace('gameDescription');
				</script>
			</div>
			<br>
			<div class="btn-group">
				<?php
				if ($gameId != -1)
				{
				?>
				<button class = "btn btn-info" onclick = "window.open('imageUploader.php', 	'Uploader', 'width=800,height=330,resizable=yes,scrollbars=yes'); return false;">Загрузка изображений</button>
				<?php	
				}
				?>
				<button class = "btn btn-info" onclick = "window.open('imageViewer.php', 	'Viewer', 	'width=800,height=330,resizable=yes,scrollbars=yes'); return false;">Просмотр изображений</button>
			</div>
			<br>
			<br>
			<div class="form-group">
				<?php if ($vizualize) 
					{ 
				?>
					<label><i>Визуализатор в наличии!</i></label>
					<br>
				<?php 
					} 
				?>
				<label for = "uploadVisualizerFile" class = "APfont">Визуализатор <?php if ($vizualize) { ?> (обновление) <?php } ?>:</label>
				<input type = "file" id = "uploadVisualizerFile">
				<?php
					if ($vizualize)
					{
				?>
					<br>
					<button type = "submit" name = "submit" onclick = "deleteVisualizer(); return false;" class = "btn btn-default">Удалить визуализатор</button>
				<?php
					}
				?>
			</div>
			<br>
			<div class="form-group">
				<label for="gameTurnTime" class = "APfont">Время хода (ms):</label>
				<input type="text" class="form-control" id="gameTurnTime" placeholder="Введите ограничение времени на ход" value = "<?php if ($gameId != -1) echo $currentGame[0]['timeLimit']; ?>">
			</div>
			<br>
			<div class="form-group">
				<label for="gameMemoryLimit" class = "APfont">Лимит памяти (kb):</label>
				<input type="text" class="form-control" id="gameMemoryLimit" placeholder="Введите ограничение стратегии по памяти" value = "<?php if ($gameId != -1) echo $currentGame[0]['memoryLimit']; ?>">
			</div>
			<br>
			<div class="btn-group">
				<button type = "submit" name = "submit" onclick = "loadFormData(); return false;" class = "btn btn-default">
					<?php
						if ($gameId == -1)
							echo 'Создать игру';
						else
							echo 'Применить изменения';
					?>
				</button>
				<?php
					if ($gameId != -1)
					{
				?>
				<button type = "submit" name = "submit" onclick = "deleteGame(); return false;" class = "btn btn-default">Удалить игру</button>
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