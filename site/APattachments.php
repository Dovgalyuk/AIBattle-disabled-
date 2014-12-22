<?php
	include_once('procedures.php');
	
	$_SESSION['adminPanelState'] = 'APattachments.php';
	
	if (isAdmin())
	{
	
		$gameId = -1;
		if (isset($_GET['game']))
			$gameId = intval($_GET['game']);
			
		$_SESSION['adminGameId'] = $gameId;
			
		$attachmentId = -1;
		if (isset($_GET['attachment']))
			$attachmentId = intval($_GET['attachment']);
			
		$_SESSION['adminAttachmentId'] = $attachmentId;
			
		$gameList 		= getGameList();
		if ($gameId == -1 && !empty($gameList))
		{
			$gameId = $gameList[0]['id'];
			$_SESSION['adminGameId'] = $gameId;
		}
		
		$currentGame 	= getGameList($gameId);
		
		$gameAttachments = array();
		if ($gameId != -1)
		{
			$gameAttachments = getAttachments($gameId);
		}
		
		$currentAttachment = array();
		if ($attachmentId != -1)
		{
			$currentAttachment = getAttachmentById($attachmentId);
		}
?>

	<script>
		changeActiveAdminButton('attachmentsButton');
	</script>

	<script>
		function loadGameData(gameId, attachmentId)
		{
			$('#dataContainer').load('APattachments.php?game=' + gameId + "&attachment=" + attachmentId);
		}
		
		function loadFormData()
		{
			var currentName 			= document.getElementById('originalFileName').value;
			var descriptionAttachment 	= document.getElementById('descriptionAttachment').value;
			var currentGameId 			= getSelectedGame();
			var currentAttachmentId		= getSelectedAttachment();
			
			if (currentName != '')
			{
				var form = new FormData();
				form.append('originalName', 		currentName);
				form.append('description', 			descriptionAttachment);
				form.append('gameId', 				currentGameId);
				form.append('attachmentId', 		currentAttachmentId);
				if ($('#uploadAttachmentFile')[0].files[0])
				{
					form.append('attachmentPath', 'uploadAttachmentFile');
					form.append('uploadAttachmentFile', $('#uploadAttachmentFile')[0].files[0]);
				}
				
				form.append(currentAttachmentId == - 1 ? 'createAttachment' : 'updateAttachment', true);
				
				$.ajax({
					url: 'jqueryAttachment.php',
					type: 'POST',
					data: form,
					success: function (data)
					{	
						alert(data);
						loadGameData(currentGameId, currentAttachmentId);
					},
					cache: false,
					contentType: false,
					processData: false
					});
			}
			else alert('Название файла не должно быть пустым!');
		}
		
		function deleteAttachment()
		{
			var currentAttachmentId	= getSelectedAttachment();
			$.post
			(
				"jqueryAttachment.php", 
				{'attachmentId' : currentAttachmentId, 'deleteAttachment' : true},
				function (data) 
				{
					alert(data);
					loadGameData(getSelectedGame(), -1);
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
		
		function getSelectorValue(selector)
		{
			var selector = document.getElementById(selector);
			return selector.options[selector.selectedIndex].value;
		}

		function getSelectedGame()
		{
			return getSelectorValue('gameSelector');
		}
		
		function getSelectedAttachment()
		{
			return getSelectorValue('attachmentSelector');
		}
	</script>

		<form role="form" method="post" enctype="multipart/form-data">
			<div class="form-group">
				<label for = "gameSelector" class = "APfont">Игра:</label>
				<select id = "gameSelector" class="form-control" onchange="loadGameData(getSelectedGame(), -1)">
					<?php
						if (empty($gameList))
						{
					?>
						<option value = "-1">Новая игра</option>
					<?php
						}
						else
						{
							foreach ($gameList as $game)
							{
					?>
						<option value = "<?php echo $game['id']; ?>">
							<?php echo $game['name']; ?>
						</option>
					<?php
							}
						}
					?>
				</select>
				<script>
					setSelectedIndexInSelector('gameSelector', <?php echo $gameId; ?>);
				</script>
			</div>
			<?php
				if ($gameId != -1)
				{
			?>
					<br>
					<div class="form-group">
						<label for = "attachmentSelector" class = "APfont">Файл для игры:</label>
						<select id = "attachmentSelector" class="form-control" onchange="loadGameData(getSelectedGame(), getSelectedAttachment())">
							<option value = "-1">Новый файл</option>
							<?php
								foreach ($gameAttachments as $attachment)
								{
							?>
								<option value = "<?php echo $attachment['id']; ?>">
									<?php echo $attachment['originalName']; ?>
								</option>
							<?php
								}
							?>
						</select>
						<script>
							setSelectedIndexInSelector('attachmentSelector', <?php echo $attachmentId; ?>);
						</script>
					</div>
					
					<br>
					<div class="form-group">
						<label for = "uploadAttachmentFile" class = "APfont">Файл:</label>
						<input type = "file" id = "uploadAttachmentFile">
					</div>
					
					<script>
						$
						(
							function ()
							{
								$("input:file").change
								(
									function()
									{
										var text = $(this).val();
										$("#originalFileName").val(text);
									}
								);
							}
						);
					</script>
					
					<br>
					<div class="form-group">
						<label for="originalFileName" class = "APfont">Название файла</label>
						<input type="text" class="form-control" id="originalFileName" placeholder="Введите название файла (c расширением)" value = "<?php if ($attachmentId != -1) echo $currentAttachment['originalName']; ?>">
					</div>
					
					<br>
					<div class="form-group">
						<label for="descriptionAttachment" class = "APfont">Описание файла</label>
						<input type="text" class="form-control" id="descriptionAttachment" placeholder="Введите описание файла" value = "<?php if ($attachmentId != -1) echo $currentAttachment['description']; ?>">
					</div>
					
					<br>
					<button type = "submit" name = "submit" onclick = "loadFormData(); return false;" class = "btn btn-default">
						<?php 
							if ($attachmentId == -1) 
								echo 'Создать файл';
							else
								echo 'Применить изменения';
						?>
					</button>
					<?php
						if ($attachmentId != -1)
						{
					?>
						<button type = "submit" name = "delete" onclick = "deleteAttachment(); return false;" class = "btn btn-default">Удалить</button>
					<?php
						}
					?>
			<?php
				}
			?>
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