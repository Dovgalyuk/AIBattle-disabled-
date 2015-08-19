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
					
					var strategies = data.strategies;
					
					var acceptedList = document.getElementById('acceptedUsers');
					var possibleList = document.getElementById('possibleUsers');
					
					for (var i = 0; i < strategies.length; ++i)
					{
						var current = strategies[i];
						possibleList.options[i] = new Option(current.login, current.strategy);
					}
				}
			);
		}

		function clearAcceptedUsers(roundId)
		{
			var possibleUsers = document.getElementById('possibleUsers');
			var acceptedUsers = document.getElementById('acceptedUsers');
            while (acceptedUsers.length > 0)
            {
                var user = acceptedUsers.options[0];
                acceptedUsers.remove(0);
                possibleUsers.add(user);
            }
		}
		
		function acceptAllUsers()
		{
			var possibleUsers = document.getElementById('possibleUsers');
			var acceptedUsers = document.getElementById('acceptedUsers');
            while (possibleUsers.length > 0)
            {
                var user = possibleUsers.options[0];
                possibleUsers.remove(0);
                acceptedUsers.add(user);
            }
		}

		function acceptUser()
		{
			var possibleUsers = document.getElementById('possibleUsers');
			var acceptedUsers = document.getElementById('acceptedUsers');
			if (possibleUsers)
			{
				var index = possibleUsers.selectedIndex;
				if (index != -1)
				{
					var strategyId = possibleUsers[index].value;
                    var login = possibleUsers[index].label;
                    acceptedUsers.add(new Option(login, strategyId));
					possibleUsers.remove(index);
				}
			}
		}
		
		function declineUser()
		{
			var possibleUsers = document.getElementById('possibleUsers');
			var acceptedUsers = document.getElementById('acceptedUsers');
			if (acceptedUsers)
			{
				var index = acceptedUsers.selectedIndex;
				if (index != -1)
				{
					var strategyId = acceptedUsers[index].value;
                    var login = acceptedUsers[index].label;
                    possibleUsers.add(new Option(login, strategyId));
					acceptedUsers.remove(index);
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
                                onclick="acceptUser();"
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
