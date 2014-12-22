<?php include_once('procedures.php'); ?>
<?php include("top.php"); ?>

    <script src="./js/visualRound.js"></script>
    <style>
    #bottomBar {
        position: fixed;
        left: 0px;
        bottom: 0px;
        width: 100%;
        height: 40px;
        background-color: #EEE;
        border-top-width: 1px;
        border-top-color: #999;
        border-top-style: solid;
        overflow: hidden;
        z-index: 100;
    }
    #bottomBarText {
        position: relative;
        left: 20px;
        top: 5px;
        font-size: 20px;
        color: black;
        opacity: 1.0;
    }
    .bottom_gray_text {
        position: relative;
        top: 9px;
        font-size: 15px;
        color: gray;
        opacity: 1.0;
    }
    .footer {
        margin-bottom: 50px;
    }
    .button {
        background-color: #9999FF;
        cursor: pointer;
    }
    .left {
        float: left;
    }
    .right {
        float:right;
    }
    .pointer {
        cursor: pointer;
    }
    .timer {
        top: 12px;
    }
    #bottomBarTextOld {
        left: 40px;
    }
    #bottomCounter {
        right: 10px;
    }
    #timersEditor {
        right: 5px;
    }
    </style>
    <div class = "container content">
    
    </div>
    
    <div id = "dataContainer" class = "container content">

<?php
    $roundId = intval($_GET['round']);
    $roundData = getRoundData($roundId);
?>
    
    <div class = "centeredText">
	<h2>Раунд "<?php echo $roundData['name']; ?>" игры "<?php echo $roundData['gameName']; ?>" от <?php echo $roundData['date']; ?></h2>
	</div>

			<h3>Результаты раунда</h3>
			<table class = "table table-bordered">
				<tr align = center>
					<td>Пользователь</td>
					<td>Счет</td>
				</tr>
				<?php
                $result = getUsersRoundScoresNoSort($roundId);
                $i = -1;
				foreach ($result as $row)
				{
                $i++;
				?>
					<tr class="tableRow" title="<?php echo $i; ?>" id="r<?php echo $row['id']; ?>" align = "center">
                    <td>
                    <?php
                        echo $row['name'];
                    ?></td>
						<td id="c<?php echo $row['id']; ?>"><?php echo 0; ?></td>
					</tr>
				<?php
					}
				?>
			</table>
			<br>
	</div>
    
    <script>
    round = <?php echo $roundId; ?>;
    startVisualization();
    </script>
    
    <div id="bottomBar">
        <button id="pause" class="btn btn-info left" onClick="pause();">Начать</button>
        <button class="btn btn-info left" onClick="step();">Далее</button>
        <p id="bottomBarText" class="left"></p>
        <p id="bottomBarTextOld" class="bottom_gray_text left"></p>
        <p id="timersEditor" class="glyphicon glyphicon-time pointer timer right" onClick="showTimersDialog();"></p>
        <p id="bottomCounter" class="bottom_gray_text right"></p>  
    </div>
    
    <?php include("bottom.php"); ?>