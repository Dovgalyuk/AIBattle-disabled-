<?php
	include_once('procedures.php');
	
	$_SESSION['adminPanelState'] = 'APcheckers.php';
	
	if (isAdmin())
	{
		$checkerId = -1;
		if (isset($_GET['checker']))
			$checkerId = intval($_GET['checker']);
			
		$_SESSION['adminCheckerId'] = $checkerId;
		
		$checkerList = getCheckerList();
		$currentChecker = getCheckerList($checkerId);
		$gameList = getGameList();
?>

	<script>
		changeActiveAdminButton('checkersButton');
	</script>

	<script>
		function loadCheckerData(checkerId)
		{
			$('#dataContainer').load('APcheckers.php?checker=' + checkerId);
		}

		function getSelectedChecker()
		{
			var selector = document.getElementById('checkerSelector');
			return selector.options[selector.selectedIndex].value;
		}
		
		function getSelectedGame()
		{
			var selector = document.getElementById('gameSelector');
			return selector.options[selector.selectedIndex].value;
		}
		
		function setSelectedIndexInSelector(selector, selectedData)
		{
			var selector = document.getElementById(selector); 
			if (selectedData != -1)
			{
				for (var i=0; i < selector.options.length; i++)
				{
					if (selector.options[i].value == selectedData)
					{
						selector.selectedIndex = i;
						break;
					}
				}
			}
		}
		
		function loadFormData()
		{
			var checkerId = getSelectedChecker();
			var gameId = getSelectedGame();
			var checkerName = document.getElementById('checkerName').value;
			var hasSeed = document.getElementById('hasSeed').checked ? 1 : 0;

			if (checkerName != '')
			{
				var form = new FormData();
				form.append('checkerId', checkerId);
				form.append('gameId', gameId);
				form.append('checkerName', checkerName);
				form.append('hasSeed', hasSeed);
				if ($('#uploadCheckerFile')[0].files[0])
				{
					form.append('checkerPath', 'uploadCheckerFile');
					form.append('uploadCheckerFile', $('#uploadCheckerFile')[0].files[0]);
				}
				
				form.append(checkerId == -1 ? 'createChecker' : 'updateChecker', true);
				
				$.ajax({
					url: 'jqueryChecker.php',
					type: 'POST',
					success: function (data)
					{	
						showModalAlert(data);
						loadCheckerData(checkerId, gameId);
					},
					data: form,
					cache: false,
					contentType: false,
					processData: false
				});
			}
			else showModalAlert('Название тестировщика не должно быть пустым!');
		}
		
		function deleteChecker()
		{
			var checkerId = getSelectedChecker();
			
			$.post
			(	"jqueryChecker.php", 
				{
					'checkerId' : checkerId,
					'deleteChecker' : true
				},
				function (data) 
				{
					showModalAlert(data);
					if (data.indexOf('Checker') != -1)
						loadCheckerData(-1);
					else
						loadCheckerData(checkerId);
				}
			);
		}
	</script>

		<form role="form" method="post">
			<div class="form-group">
				<label for = "checkerSelector" class = "APfont">Тестировщик:</label>
				<select id = "checkerSelector" class="form-control" onchange="loadCheckerData(getSelectedChecker(), <?php if ($checkerId != -1) echo $currentChecker[0]['game']; else echo -1;?>)">
					<option value = "-1">Новый тестировщик</option>
					<?php
						foreach ($checkerList as $checker)
						{
					?>
						<option value = "<?php echo $checker['id']; ?>">
							<?php echo $checker['name']; ?>
						</option>
					<?php
						}
					?>
				</select>
				<script>
					setSelectedIndexInSelector('checkerSelector', <?php echo $checkerId; ?>);
				</script>
			</div>
			<br>
			<div class="form-group">
				<label for = "gameSelector" class = "APfont">Игра:</label>
				<!--onchange="loadCheckerData(getSelectedChecker(), getSelectedGame())" -->
				<select id = "gameSelector" class="form-control" >
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
					setSelectedIndexInSelector('gameSelector', <?php if ($checkerId != -1) echo $currentChecker[0]['game']; else echo -1; ?>);
				</script>
			</div>
			<br>
			<div class="form-group">
				<label for="checkerName" class = "APfont">Название тестировщика:</label>
				<input type="text" class="form-control" id="checkerName" placeholder="Введите название тестировщика" value = "<?php if ($checkerId != -1) echo $currentChecker[0]['name']; ?>">
			</div>
			<br>
			<div class="form-group">
				<label for="hasSeed" class = "APfont">Генератор случайных чисел:</label>
				<div class="checkbox">
					<label>
						<input type="checkbox" id="hasSeed" <?php if ($checkerId != -1 && $currentChecker[0]['hasSeed']) echo "checked"; ?>>
						Использовать начальное значение из настроек раунда
					</label>
				</div>
			</div>
			<br>
			<?php
				if ($checkerId != -1)
				{
			?>
			<div class="form-group">
				<div id = "source">
					<a href="javascript://" onclick="$('#source').load('getChecker.php?id=<?php echo $checkerId; ?>');return false;">Показать код</a>
				</div>
			</div>
			<?php
				}
			?>
			<div class="form-group">
				<label for = "uploadCheckerFile" class = "APfont">Код тестировщика:</label>
				<input type = "file" id = "uploadCheckerFile">
			</div>
			<br>
			<div class="btn-group">
				<button type = "submit" name = "submit" onclick = "loadFormData(); return false;" class = "btn btn-default">
					<?php
						if ($checkerId == -1)
							echo 'Создать тестировщик';
						else
							echo 'Применить изменения';
					?>
				</button>
				<?php
					if ($checkerId != -1)
					{
				?>
				<button type = "submit" name = "submit" onclick = "deleteChecker(); return false;" class = "btn btn-default">Удалить тестировщик</button>	
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