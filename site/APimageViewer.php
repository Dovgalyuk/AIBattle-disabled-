<?php
	include_once('procedures.php');
	$gameList 		= getGameList();
	
	if (isActiveUser())
	{
	
		if (stripos($_SERVER['REQUEST_URI'], 'APimageViewer'))
			$_SESSION['adminPanelState'] = 'APimageViewer.php';
		
		// сессия состояния
		
		$typeId = -1;
		if (isset($_GET['type']))
			$typeId = intval($_GET['type']);
			
		// сессия - typeId
		if (stripos($_SERVER['REQUEST_URI'], 'APimageViewer'))
			$_SESSION['adminImgTypeId'] = $typeId;
			
		$gameId = -1;
		if (isset($_GET['game']))
			$gameId = intval($_GET['game']);
			
		// сессия - gameId
		if (stripos($_SERVER['REQUEST_URI'], 'APimageViewer'))
			$_SESSION['adminImgGameId'] = $gameId;
			
		// сбор инфы из бд
		$imageData = getImageData($typeId, $gameId);
		
?>

	<script>
		changeActiveAdminButton('imageViewerButton');
	</script>

	<script>
		function loadViewerData(type, game)
		{
			$('#dataContainer').load('APimageViewer.php?type=' + type + '&game=' + game);
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
		
		function getSelectorValue(selector)
		{
			var selector = document.getElementById(selector);
			return selector.options[selector.selectedIndex].value;
		}
		
		function getTypeSelector()
		{
			return getSelectorValue('typeSelector');
		}
		
		function getGameSelector()
		{
			return getSelectorValue('gameSelector');
		}
		
		function deleteImage(id)
		{
			$.post
			(	"jqueryDeleteImage.php", 
				{
					'id' : id
				},
				function(text) {loadViewerData(getTypeSelector(), getGameSelector());}
			);
		}
	</script>

	<!--
	<script>
		window.load 	
		{
			var gameSelector = document.getElementById('gameSelectorDiv');
			gameSelector.style.display = 'none';
		}
	</script>

	<script>
		function activateGameSelector()
		{
			var typeSelector = document.getElementById('typeSelector');
			var gameSelector = document.getElementById('gameSelectorDiv');
			if (typeSelector[typeSelector.selectedIndex].value == 1)
				gameSelector.style.display = 'block';
			else
				gameSelector.style.display = 'none';
		}
	</script>
	-->
	<center><h3>Viewer</h3></center>
	<div class="form-group">
		<label for = "typeSelector" class = "APfont">Категория изображения:</label>
		<select id = "typeSelector" class="form-control" onchange = "loadViewerData(getTypeSelector(), -1)">
			<option value = "-1">Нет категории</option>
			<option value = "0">Новости</option>
			<option value = "1">Визуализатор</option>
		</select>
		<script>
			setSelectedIndexInSelector('typeSelector', <?php echo $typeId; ?>);
		</script>
	</div>
	<?php
		if ($typeId == 1)
		{
	 ?>
	<div class="form-group" id = "gameSelectorDiv">
		<label for = "gameSelector" class = "APfont">Игра:</label>
		<select id = "gameSelector" class="form-control" onchange = "loadViewerData(getTypeSelector(), getGameSelector())">
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
	<?php
		}
	?>
	<table class="table table-bordered">
		<tr class = "info" align = center>
			<td>Изображение</td>
			<td>Описание</td>
			<td>Путь</td>
			<td></td>
		</tr>
		<?php
			foreach ($imageData as $image)
			{
		?>
			<tr align = center>
				<td>
					<img src = "<?php echo getImageById($image['id']);?>">
				</td>
				<td><?php echo $image['description']; ?></td>
				<td><?php echo getImageById($image['id']); ?></td>
				<td><button type = "submit" name = "deleteImage" class = "btn btn-danger" onclick = "deleteImage(<?php echo $image['id']; ?>);">Удалить</button></td>
			</tr>
		<?php
			}
		?>
	</table>
	
	<?php
		}
		else
		{
	?>
		<p>Тебя не должно быть здесь!</p>
	<?php
		}
	?>