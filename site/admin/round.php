<?php
    $id = intval($_GET["id"]);
    $tournamentId = intval($_GET["tournament"]);
    if ($id == -1)
    {
        $tournament = getTournamentList($tournamentId);
        $checkers = getCheckerListByGameId($tournament['game']);
		$rounds = getRoundList($tournamentId);
?>
<script>
		function loadRoundUsers()
		{
			$.post(
				"jqueryGetRoundUsers.php",
				{
					'roundId' : -1, 
					'tournamentId' : <?php echo $tournamentId; ?>
				},
				function(responceText) 
				{
					var data = JSON.parse(responceText);
					
					var acceptedUsers = data.acceptedUsers;
					var possibleUsers = data.possibleUsers;
					
					var acceptedList = document.getElementById('acceptedUsers');
					var possibleList = document.getElementById('possibleUsers');
					
					for (var i = 0; i < acceptedUsers.length; ++i)
					{
						var currentUser = acceptedUsers[i];
						var text = currentUser.login;
						if ("score" in currentUser)
						{
							text = text + ' (' + currentUser.score + ')';
						}
						acceptedList.options[i] = new Option(text, currentUser.id);	
					}
					
					for (var i = 0; i < possibleUsers.length; ++i)
					{
						var currentUser = possibleUsers[i];
						var text = currentUser.login;
						if ("score" in currentUser)
						{
							text = text + ' (' + currentUser.score + ')';
						}
						possibleList.options[i] = new Option(text, currentUser.id);
					}
				}
			);
		}

		function clearAcceptedUsers(roundId)
		{
			/*var checker = document.getElementById('clearAcceptedUsersButton');
			var seed = document.getElementById('checkerSeed').value;
			changeRoundActivity(roundId, 'clearAcceptedUsers', [], -1, -1, seed);*/
		}
		
		function acceptAllUsers()
		{
			/*var checker = document.getElementById('acceptPossibleUsersButton'); 
			var possibleUsers = getPossibleUsersStrategy();
			var seed = document.getElementById('checkerSeed').value;
			changeRoundActivity(roundId, 'acceptPossibleUsers', possibleUsers, -1, -1, seed);*/
		}

		function acceptNewUser()
		{
			var possibleUsers = document.getElementById('possibleUsers');
			if (possibleUsers)
			{
				var index = possibleUsers.selectedIndex;
				if (index != -1)
				{
					var strategyId = possibleUsers[index].value;
					//changeRoundActivity(roundId, 'acceptPossibleUsers', [strategyId], -1, -1, seed);
				}
			}
		}
		
		function declineUser()
		{
			var acceptedUsers = document.getElementById('acceptedUsers');
			if (acceptedUsers)
			{
				var index = acceptedUsers.selectedIndex;
				if (index != -1)
				{
					var strategyId = acceptedUsers[index].value;
					//changeRoundActivity(roundId, 'declineUsers', [strategyId], -1, -1, seed);
				}
			}
		}

</script>
        <form role="form" method="post">
			<div class="form-group"> 
                <label for="name" class="APfont">Название раунда:</label>
                <input type="text" class="form-control" id="name" 
                    placeholder="Введите название раунда">
            </div>
			<div class="form-group"> 
				<label for = "checker" class = "APfont">Тестировщик:</label>
				<select id = "checker" class="form-control">
					<option value = "-1">Отсуствует</option>
					<?php
						foreach ($checkers as $checker)
						{
					?>
						<option value = "<?php echo $checker['id']; ?>">
							<?php echo $checker['name']; ?>
						</option>
					<?php
						}
					?>
				</select>
            </div>
            <div class="form-group">
				<label for="seed" class = "APfont">Начальное значение генератора случайных чисел:</label>
				<input id="seed" type="text" class="form-control" placeholder="Начальное значение">
			</div>
<!-- TODO			<div class="form-group">
				<label for = "previousRound" class = "APfont">Отобрать участников:</label>
				<select id = "previousRound" class="form-control">
					<option value = "-1">Все</option>
					<option value = "-2">Выбрать вручную</option>
					<?php
						foreach ($rounds as $round)
						{
					?>
							<option value = "<?php echo $round['id']; ?>">
								<?php echo 'Победители раунда "'.$round['name'].'"'; ?>
							</option>
					<?php
						}
					?>
				</select>
			</div>
-->
			<label class = "APfont">Участники раунда:</label>
			<div class="form-group">
				<table class = "table table-hover">
					<tr>
						<td class = "PossibleListWidth">Возможные участники</td>
						<td class = "filterListTd"></td>
						<td class = "AcceptedListWidth">Отобранные участники</td>
					</tr>
					<tr>
						<td class = "PossibleListWidth">
							<select id = "possibleUsers"
                                onclick="acceptNewUser();"
                                multiple class="form-control">
							</select>
						</td>
						<td class = "filterListTd">
							<button class = "button" id = "acceptPossibleUsersButton" onclick="acceptAllUsers(); return false;" > >> </button>
							<br>
							<br>
							<button class = "button" id = "clearAcceptedUsersButton" onclick = "clearAcceptedUsers(); return false;" > << </button>
						</td>
						<td class = "AcceptedListWidth">
							<select id = "acceptedUsers" onclick="declineUser();" multiple class="form-control">
							</select>
						</td>
					</tr>
				</table>
			</div>
			<script>
				loadRoundUsers();
			</script>

            <div class="btn-group">
                <button type = "submit" name = "submit" 
                    onclick = "submitForm(); return false;" 
                    class = "btn btn-default">
                    Создать раунд
                </button>
            </div>    
        </form>
<?php
    }
    else
    {
?>
<?php
    }
?>
