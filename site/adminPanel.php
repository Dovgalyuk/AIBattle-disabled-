<?php
	include_once('procedures.php');
?>

	<?php include("top.php"); ?>
	
	<?php
		if (isAdmin())
		{
	?>
	
		<?php
			if (isset($_GET['mode']))
			{
				$_SESSION['adminPanelState'] = 'APfaq.php';
				echo '<meta http-equiv="refresh" content="0; url=adminPanel.php">';
			}
		?>
		
		<div class = "container content">
			<div class="titleName">
				Администраторская панель
			</div>
			<div class="tournamentMenu">
				<div class="btn-group">
					<button type="button" id="gameButton" class="btn btn-info" onclick="$('#dataContainer').load('APgame.php');">Игры</button>
					<button type="button" id="tournamentButton" class="btn btn-default" onclick="$('#dataContainer').load('APtournament.php');">Турниры</button>
					<button type="button" id="roundsButton"  class="btn btn-default" onclick="$('#dataContainer').load('AProunds.php');">Раунды</button>
					<button type="button" id="checkersButton" class="btn btn-default" onclick="$('#dataContainer').load('APcheckers.php');">Тестировщики игр</button>
					<button type="button" id="attachmentsButton" class="btn btn-default" onclick="$('#dataContainer').load('APattachments.php');">Файлы игр</button>
					<button type="button" id="usersButton" class="btn btn-default" onclick="$('#dataContainer').load('APuserManagement.php');">Пользователи</button>
					<button type="button" id="newsButton" class="btn btn-default" onclick="$('#dataContainer').load('APnews.php');">Новости</button>
					<button type="button" id="faqButton" class="btn btn-default" onclick="$('#dataContainer').load('APfaq.php');">FAQ</button>
				</div>
				<br>
				<div class="btn-group">
					<button type="button" id="imageUploadButton" class="btn btn-default" onclick="$('#dataContainer').load('APimageUpload.php');">Загрузчик изображений</button>
					<button type="button" id="imageViewerButton" class="btn btn-default" onclick="$('#dataContainer').load('APimageViewer.php');">Просмотр изображений</button>
				</div>
			</div>
		</div>
		
		<div id = "dataContainer" class = "container content">
			
		</div>
		
		<script>
			data = $.post
			(
				'jqueryGetCurrentAdminPanelState.php', 
				function(responceText)
				{
					if (responceText != 'null')
					{
						if (responceText.indexOf('attachments') != -1)
							$('#dataContainer').load(responceText + '?game=<?php echo $_SESSION['adminGameId'];?>&attachment=<?php echo $_SESSION['adminAttachmentId'];?>');
						else if (responceText.indexOf('checkers') != -1)
							$('#dataContainer').load(responceText + '?checker=<?php echo $_SESSION['adminCheckerId'];?>');
						else if (responceText.indexOf('faq') != -1)
							$('#dataContainer').load(responceText + '?status=<?php echo $_SESSION['adminQuestionStatusId'];?>&question=<?php echo $_SESSION['adminQuestionId'];?>');
						else if (responceText.indexOf('game') != -1)
							$('#dataContainer').load(responceText + '?game=<?php echo $_SESSION['adminGameId'];?>');
						else if (responceText.indexOf('news') != -1)
							$('#dataContainer').load(responceText + '?news=<?php echo $_SESSION['adminNewsId'];?>');
						else if (responceText.indexOf('rounds') != -1)
							$('#dataContainer').load(responceText + '?tournament=<?php echo $_SESSION['adminTournamentId'];?>&round=<?php echo $_SESSION['adminRoundId'];?>');
						else if (responceText.indexOf('tournament') != -1)
							$('#dataContainer').load(responceText + '?tournament=<?php echo $_SESSION['adminTournamentId'];?>');
						else if (responceText.indexOf('duels') != -1)
							$('#dataContainer').load(responceText + '?tournament=<?php echo $_SESSION['adminTournamentId'];?>&round=<?php echo $_SESSION['adminRoundId'];?>');
						else if (responceText.indexOf('imageUpload') != -1)
							$('#dataContainer').load(responceText);
						else if (responceText.indexOf('imageViewer') != -1)
							$('#dataContainer').load(responceText + '?type=<?php echo $_SESSION['adminImgTypeId'];?>&game=<?php echo $_SESSION['adminImgGameId']; ?>');
					}
					else
					{
						$('#dataContainer').load('APgame.php');
					}
				}
			);
		</script>
		
		
		<?php
			}
			else
			{
		?>
			<div class = "redColored">
				<p>Тебя не должно быть здесь!</p>
			</div>
		<?php
			}
		?>
	
	<?php include("bottom.php"); ?>
