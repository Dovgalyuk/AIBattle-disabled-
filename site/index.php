<?php include_once('procedures.php');?>
<?php include("top.php"); ?>
	
<?php
	$news = getNewsData();
?>
	<div class = "container content" id = "dataContainer">

		<?php
			foreach ($news as $data)
			{
		?>
			<div class = "boardedHeader">
				<h2><?php echo $data['header']; ?></h2>
			</div>
			<div class = "newsDiv">
				<?php echo $data['text']; ?>
			</div>
			<div class = "commentsDate">
				<?php echo reverseDate($data['date'], "-"); ?>
			</div>
			<div>
				<a href="comments.php?news=<?php echo $data['id']; ?>">Comments (<?php echo getCommentsCount($data['id']); ?>)</a>
			</div>
		<?php
			}
		?>

<?php
    $tournamentId = -1;
    include("statistics.php");
?>

	</div>
	
<?php include("bottom.php"); ?>