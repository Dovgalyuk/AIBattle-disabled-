<?php
	include_once('procedures.php');

	if (isActiveUser())
	{
	
		if (stripos($_SERVER['REQUEST_URI'], 'APimageUpload'))
			$_SESSION['adminPanelState'] = 'APimageUpload.php';
		
		$gameList 		= getGameList();
		
		if (isset($_POST['submit']))
		{
			loadImage($_POST['imagePath'], $_POST['imageType'], $_POST['imageDescription'], $_POST['gameId']);
		}
?>

	<script>
		$(window).load(function() 
		{ 
			//console.log(document);
			//showModalAlert(document.getElementById('imageDescription'));
			var gameSelector = document.getElementById('gameSelectorDiv');
			gameSelector.style.display = 'none';
		})
	</script>


	<script>
		changeActiveAdminButton('imageUploadButton');
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
		
		function loadFormData()
		{
			if (!$('#uploadImage')[0].files[0])
				showModalAlert('Нет выбранного изображения!');
			else 
			{
				var imageExtension = $('#uploadImage')[0].files[0].name.split('.').pop();
				if (imageExtension != 'jpg' && imageExtension != 'jpeg' && imageExtension != 'png')
					showModalAlert('Поддерживаемые расширения изображений: jpeg/png');
				else
				{
					var typeSelector = document.getElementById('typeSelector');
					var typeSelectorValue = typeSelector.options[typeSelector.selectedIndex].value;
					
					var gameSelector = document.getElementById('gameSelector');
					var gameSelectorValue = -1;
					if (gameSelector)
						gameSelectorValue = gameSelector.options[gameSelector.selectedIndex].value;
					
					var imageDescription = document.getElementById('imageDescription').value;
					
					var form = new FormData();
					form.append('imageType', typeSelectorValue);
					form.append('gameId', gameSelectorValue);
					form.append('imageDescription', imageDescription);
					
					form.append('imagePath', 'uploadImage');
					form.append('uploadImage', $('#uploadImage')[0].files[0]);
					
					form.append('submit', true);
					
					$.ajax({
						url: 'APimageUpload.php',
						type: 'POST',
						data: form,
						cache: false,
						contentType: false,
						processData: false,
						success: function ()
						{	
							showModalAlert('Изображение успешно загружено!');
						}
					});
				}
			}
		}
		</script>

	<center><h3>Uploader</h3></center>
	<form role="form" method="post" enctype="multipart/form-data">
		<div class="form-group">
			<label for = "typeSelector" class = "APfont">Категория изображения:</label>
			<select id = "typeSelector" class="form-control" onchange = "activateGameSelector()">
				<option value = "-1">Нет категории</option>
				<option value = "0">Новости</option>
				<option value = "1">Визуализатор</option>
			</select>
		</div>
		
		<div class="form-group" id = "gameSelectorDiv">
			<label for = "gameSelector" class = "APfont">Игра:</label>
			<select id = "gameSelector" class="form-control">
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
		</div>
		
		<div class="form-group">
			<label for="imageDescription" class = "APfont">Описание изображения:</label>
			<input type="text" class="form-control" id="imageDescription" placeholder="Введите описание изображения">
		</div>
		
		<div class="form-group">
			<label for = "uploadImage" class = "APfont">Изображение:</label>
			<input type = "file" id = "uploadImage">
		</div>
		
		<button type = "submit" name = "submit" onclick = "loadFormData(); return false;" class = "btn btn-default">Загрузить изображение</button>
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