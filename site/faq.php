<?php include_once('procedures.php');?>
<?php include("top.php"); ?>

<script>
    function sendQuestion()
    {
        $.post
        (
            "jqueryFaqSendQuestion.php",
            {'userQuestion' : document.getElementById('userQuestion').value },
            function (data)
            {
                showModalAlert(data);
                window.location.reload();
            }
        );
    }
</script>

	<div class = "container content">
		<div class="titleName">FAQ</div>
		<?php
			$questions = getQuestions("answered");
			foreach ($questions as $question)
			{
		?>
			<h4>Вопрос:</h4>
			<div class = "questionDiv">
				<?php echo $question['question']; ?>
			</div>
			<h4>Ответ:</h4>
			<div class = "answerDiv">
				<?php echo $question['answer']; ?>
			</div>
			<hr>
		<?php
			}
		?>

		<?php
			if (getActiveUserID() != -1)
			{
		?>
			<br>
			
			<!-- Форма отправки вопроса -->
			<form role="form" method="post">
				<div class="form-group">
					<label for="userQuestion" class = "APfont">Ваш вопрос:</label>
					<textarea id = "userQuestion" name = "userQuestion" class="form-control" rows="3"></textarea>
				</div>
				<button type = "submit" name = "submit" class = "btn btn-default" onclick = "sendQuestion(); return false;">Отправить</button>
			</form>
		<?php
			}
		?>
	</div>
	
<?php include("bottom.php"); ?>