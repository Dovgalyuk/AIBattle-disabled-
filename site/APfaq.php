<?php
	include_once('procedures.php');
	
	$_SESSION['adminPanelState'] = 'APfaq.php';
	
	if (isAdmin())
	{
	
		$status = -1;
		if (isset($_GET['status']))
		{
			$status = intval($_GET['status']);
		}
		
		$_SESSION['adminQuestionStatusId'] = $status;
		
		$questionId = -1;
		$currentQuestion = array();
		
		if (isset($_GET['question']))
		{
			$questionId = intval($_GET['question']);
			$currentQuestion = getQuestionById($questionId);
		}
		
		$_SESSION['adminQuestionId'] = $questionId;
?>

	<script>
		changeActiveAdminButton('faqButton');
	</script>

	<script>
		function loadFaqData(statusId, questionId)
		{
			$('#dataContainer').load('APfaq.php?status=' + statusId + '&question=' + questionId);
		}
		
		function getSelectorValue(selector)
		{
			var selector = document.getElementById(selector);
			return selector.options[selector.selectedIndex].value;
		}
		
		function loadFormData(questionId)
		{
			var currentQuestion = CKEDITOR.instances.questionDescription.getData();
			var currentAnswer 	= CKEDITOR.instances.answerDescription.getData();
			
			var faqData = 
		{		'questionId' 	: questionId,
				'question' 		: currentQuestion,
				'answer'		: currentAnswer
			};
			
			if (questionId == -1)
				faqData['createQuestion'] = true;
			else
				faqData['updateQuestion'] = true;
			
			$.post
			(	"jqueryFaq.php", 
				faqData,
				function (data)
				{
					alert(data);
					window.location.reload();
				}
			);
		}
		
		function closeQuestion(questionId)
		{
			$.post
			(	"jqueryFaq.php", 
				{
					'questionId' 	: questionId,
					'closeQuestion'	: true
				},
				function (data)
				{
					alert(data);
					window.location.reload();
				}
			);
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
		
		function getSelectedStatus()
		{
			return getSelectorValue('questionStatusSelector');
		}
		
		function getSelectedQuestion()
		{
			return getSelectorValue('questionSelector');
		}
	</script>

		<form role="form" method="post">
			<div class="form-group">
				<label for = "questionStatusSelector" class = "APfont">Статус:</label>
				<select id = "questionStatusSelector" class="form-control" onchange="loadFaqData(getSelectedStatus(), -1)">
					<option value = "-1">Новый вопрос</option>
					<option value = "0">Вопросы от пользователей</option>
					<option value = "1">Ответы на вопросы</option>
				</select>
				<script>
					setSelectedIndexInSelector('questionStatusSelector', <?php echo $status; ?>);
				</script>
			</div>
			<?php
				if ($status != -1)
				{
					$questions = getQuestionData($status);
			?>
				<br>
				<div class="form-group">
					<label for = "questionSelector" class = "APfont">Вопросы:</label>
					<select id = "questionSelector" class="form-control" onchange="loadFaqData(getSelectedStatus(), getSelectedQuestion())">
						<option value = "-1">Не выбран</option>
					<?php
						foreach ($questions as $question)
						{
					?>
						<option value = "<?php echo $question['id']; ?>">
							<?php echo $question['question']; ?>
						</option>
					<?php
						}
					?>
					</select>
					<script>
						setSelectedIndexInSelector('questionSelector', <?php echo $questionId; ?>);
					</script>
				</div>
				

			<?php
				}
			?>
			<br>
			<div class="form-group">
				<label for="questionDescription" class = "APfont">Описание вопроса:</label>
				<textarea id = "questionDescription" class="form-control" rows="3"><?php if ($questionId != -1) echo trim($currentQuestion['question']);?></textarea>
				<script>
					CKEDITOR.replace('questionDescription');
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
				<label for="answerDescription" class = "APfont">Ответ на вопрос:</label>
				<textarea id = "answerDescription" class="form-control" rows="3"><?php if ($questionId != -1 && $currentQuestion['answer'] != null) echo trim($currentQuestion['answer']);?></textarea>
				<script>
					CKEDITOR.replace('answerDescription');
				</script>
			</div>
			<br>
			<div class="btn-group">
				<button class = "btn btn-info" onclick = "window.open('imageUploader.php', 	'Uploader', 'width=800,height=330,resizable=yes,scrollbars=yes'); return false;">Загрузка изображений</button>
				<button class = "btn btn-info" onclick = "window.open('imageViewer.php', 	'Viewer', 	'width=800,height=330,resizable=yes,scrollbars=yes'); return false;">Просмотр изображений</button>
			</div>
			<br>
			<br>	
			
			<div class="btn-group">
				<button type = "submit" name = "submit" onclick = "loadFormData(<?php echo $questionId; ?>); return false;" class = "btn btn-default">Применить изменения</button>
				<?php
					if ($status == 0)
					{
				?>
					<button type = "submit" name = "submit" onclick = "closeQuestion(<?php echo $questionId; ?>); return false;" class = "btn btn-default">Закрыть вопрос</button>
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