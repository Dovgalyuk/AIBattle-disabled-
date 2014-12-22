<?php
	include_once('procedures.php');
	
	$_SESSION['adminPanelState'] = 'APnews.php';
	
	if (isAdmin())
	{
	
		$newsId = -1;
		
		if (isset($_GET['news']))
			$newsId = intval($_GET['news']);
			
		$_SESSION['adminNewsId'] = $newsId;
			
		$newsList = getNewsData();
		$currentNews = getNewsData($newsId);
		
		$date = '';
		if ($newsId == -1)
		{
			$date[0] = date("Y");
			$date[1] = date("m");
			$date[2] = date("d");
		}
		else
		{
			$date = explode("-", $currentNews["date"]);
		}
?>
	<script>
		$(
			function() 
			{
				$("#datepicker").datepicker({
					yearRange: "1990:2016",
					dateFormat: "dd/mm/yy"
				});
			}
		);
		
	</script>

	<script>
		changeActiveAdminButton('newsButton');
	</script>

	<script>
		function loadNewsData(newsId)
		{
			$('#dataContainer').load('APnews.php?news=' + newsId);
		}
		
		function loadFormData()
		{
			var currentNews 		= getSelectedNews();
			var currentTitle 		= document.getElementById('newsHeader').value;
			var currentText 		= CKEDITOR.instances.newsDescription.getData();
			var currentDate			= document.getElementById('datepicker').value;
			
			if (currentTitle != '')
			{
				var newsData = {'newsId' : currentNews, 'header' : currentTitle, 'text' : currentText, 'date' : currentDate};
				
				if (currentNews == -1)
					newsData['createNews'] = true;
				else
					newsData['updateNews'] = true;
				
				$.post
				(	"jqueryNews.php", 
					newsData,
					function (data)
					{
						alert(data);
						loadNewsData(currentNews);
					}
				);
			}
			else alert('Заголовок новости не должен быть пустым!');
		}
		
		function deleteNews()
		{
			
			var currentNews = getSelectedNews();
			$.post
			(
				"jqueryNews.php",
				{
					'newsId' 		: currentNews,
					'deleteNews' 	: true
				},
				function (data)
				{
					alert(data);
					loadNewsData(-1);
				}
			);
			
		}
		
		function getSelectorValue(selector)
		{
			var selector = document.getElementById(selector);
			return selector.options[selector.selectedIndex].value;
		}
		
		function setSelectedIndexInSelector(selector, selectedData)
		{
			var selector = document.getElementById(selector); 
			if (selectedData != -1)
			{
				for (var i = 0; i < selector.options.length; i++)
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
		
		function getSelectedNews()
		{
			return getSelectorValue('newsSelector');
		}
	</script>

		<form role="form" method="post">
			<div class="form-group">
				<label for = "newsSelector" class = "APfont">Новость:</label>
				<select id = "newsSelector" class="form-control" onchange="loadNewsData(getSelectedNews())">
					<option value = "-1">Новая новость</option>
					<?php
						foreach ($newsList as $data)
						{
					?>
						<option value = "<?php echo $data['id']; ?>">
							<?php echo $data['header'] . ' - ' . $data['date']; ?>
						</option>
					<?php
						}
					?>
				</select>
				<script>
					setSelectedIndexInSelector('newsSelector', <?php echo $newsId; ?>);
				</script>
			</div>
			<br>
			<div class="form-group">
				<label for="newsHeader" class = "APfont">Заголовок новости:</label>
				<input type="text" class="form-control" id="newsHeader" placeholder="Введите заголовок" value = "<?php if ($newsId != -1) echo $currentNews['header']; ?>">
			</div>
			<br>
			<div class="form-group">
				<label for="newsDescription" class = "APfont">Описание новости:</label>
				<textarea id = "newsDescription" class="form-control" rows="3"><?php if ($newsId != -1) echo trim($currentNews['text']);?></textarea>
				<script>
					CKEDITOR.replace('newsDescription');
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
				<label for="date" class = "APfont">Дата новости:</label>
				<input type="text" name="date" value="<?php echo $date[2]."/".$date[1]."/".$date[0];?>" id="datepicker">
			</div>
			<div class="btn-group">
				<button type = "submit" name = "submit" onclick = "loadFormData(); return false;" class = "btn btn-default">
					<?php
						if ($newsId == -1)
							echo 'Создать новость';
						else
							echo 'Применить изменения';
					?>
				</button>
				<?php
					if ($newsId != -1)
					{
				?>
				<button type = "submit" name = "submit" onclick = "deleteNews(); return false;" class = "btn btn-default">Удалить новость</button>
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
