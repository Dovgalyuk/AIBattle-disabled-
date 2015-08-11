<?php include("top.php"); ?>
<?php
	$newsId = -1;
	
	if (isset($_GET['news']))
		$newsId = intval($_GET['news']);
		
	$commentId = -1;
	if (isset($_GET['comment']))
		$commentId = intval($_GET['comment']);
		
	$currentNews = getNewsData($newsId);
	
	if (isset($_POST['submit']))
	{
		$postNewsId 	= intval($_POST['newsId']);
		$postCommentId 	= intval($_POST['commentId']); 
		if ($postCommentId == -1)
		{
			sendComments($postNewsId, $_POST['userComment']);
		}
		else
		{
			updateComment($postCommentId, $_POST['userComment']);
		}
	}
	
	if (isset($_POST['delete']))
	{
		$postCommentId = intval($_POST['commentId']);
		deleteComment($postCommentId);
	}
?>

<script>
	function loadCommentData(newsId, commentId)
	{
		$('#dataContainer').load('comments.php?news=' + newsId + '&comment=' + commentId);
	}
	
	function deleteComment(newsId, commentId)
	{
		$.post
		(
			"comments.php",
			{
				'commentId' : commentId,
				'delete'	: true
			}
		);
		
		loadCommentData(newsId, -1);
	}
	
	function editComment(newsId, commentId)
	{
		loadCommentData(newsId, commentId);
	}
</script>

    <div class = "container content" id = "dataContainer">

	<!-- Новости -->
	<div class = "boardedHeader">
		<h2><?php echo $currentNews['header']; ?></h2>
	</div>
	<div class = "newsDiv">
		<?php echo $currentNews['text']; ?>
	</div>
	<div class = "commentsDate">
		<?php echo reverseDate($currentNews['date'], "-"); ?>
	</div>
	
	<!-- Комментарии -->
	<h3>Комментарии</h3>
	<?php
		$comments = getComments($newsId);
		foreach ($comments as $comment)
		{
	?>
		<div class = "commentsDiv">
			<i><?php echo getNicknameById($comment['user']).":"; ?></i>
			<?php echo $comment['text'];?>
			<div class = "commentsDate">
				<?php echo $comment['date']; ?>
			</div>
			<?php
/* enable editing later
				if ($comment['user'] == getActiveUserID() || isAdmin() || isModerator())
				{
			?>
					<button type = "submit" name = "submit" onclick = "editComment(<?php echo $newsId.",".$comment['id']; ?>); return false;" class = "btn btn-default">Изменить</button>
			<?php
				}
			?>
			<?php
				if (isAdmin() || isModerator())
				{
			?>
					<button type = "submit" name = "delete" onclick = "deleteComment(<?php echo $newsId.",".$comment['id']; ?>); return false;" class = "btn btn-default">Удалить</button>
			<?php
				}
*/
			?>
		</div>
	<?php
		}
	?>
	
	<?php
		if (getActiveUserID() != -1 )
		{
			$commentText = "Отправить";
			if ($commentId != -1)
			{
				$commentText = "Изменить";
			}
	?>
	<form role="form" method="post">
		<div class="form-group">
			<label for="userComment" class = "APfont">Ваш комментарий:</label>
			<textarea name = "userComment" class="form-control" rows="3"><?php if ($commentId != -1) echo getCommentText($commentId); ?></textarea>
			<input type=hidden name=newsId value=<?php echo $newsId;?> >
			<input type=hidden name=commentId value=<?php echo $commentId;?> >
			<input type=hidden name=submit value=true>
		</div>
		<button type = "submit" name = "submit" class = "btn btn-default"><?php echo $commentText; ?></button>
	</form>
	<?php
		}
	?>

    </div>

<?php include("bottom.php"); ?>
