<?php
	include_once('procedures.php');
	
	$_SESSION['adminPanelState'] = 'AProunds.php';
	
	if (isAdmin())
	{
	
		$tournamentId = -1;
		if (isset($_GET['tournament']))
			$tournamentId = intval($_GET['tournament']);
			
		$_SESSION['adminTournamentId'] = $tournamentId;
			
		$roundId = -1;
		if (isset($_GET['round']))
			$roundId = intval($_GET['round']);
			
		$_SESSION['adminRoundId'] = $roundId;
		
		$tournamentList = getTournamentList();
		$roundList = getRoundList($tournamentId);
		if ($tournamentId != -1)
		{
			$currentTournament = getTournamentList($tournamentId);
			$roundCheckerList = getCheckerListByGameId($currentTournament['game']);
		}
		
		$currentRound = -1;
		
		if ($roundId != -1)
			$currentRound = getRoundList($tournamentId, $roundId);
			
		$isEnded = checkRoundEnding($roundId);
?>

	<script>
		changeActiveAdminButton('roundsButton');
	</script>

	<script>
		
		function loadRoundData(tournamentId, roundId)
		{
			$('#dataContainer').load('AProunds.php?tournament=' + tournamentId + '&round=' + roundId);
		}

		function getSelectorValue(selector)
		{
			var selector = document.getElementById(selector);
			return selector.options[selector.selectedIndex].value;
		}
		
		function getSelectedTournament()
		{
			return getSelectorValue('tournamentSelector');
		}
		
		function getSelectedRound()
		{
			return getSelectorValue('roundSelector');
		}
		
		function getSelectedChecker()
		{
			return getSelectorValue('checkerSelector');
		}
		
		function setSelectedIndexInSelector(selector, selectedData)
		{
			var selector = document.getElementById(selector); 
			if (selectedData != -1)
			{
				for (var i=0; i < selector.options.length; i++)
				{
					if (selector.options[i].value == selectedData)
					{
						selector.selectedIndex = i;
						break;
					}
				}
			}
		}
		
		function getAcceptedUsersStrategy()
		{
			var data = [];
			if (document.getElementById('acceptedUsers') != null)
			{
				var acceptedListOptions = document.getElementById('acceptedUsers').options;
				
				for (var i = 0; i < acceptedListOptions.length; ++i) 
				{
					data.push(acceptedListOptions[i].value);
				}
			}
			return data;
		}
		
		function getPossibleUsersStrategy()
		{
			var data = [];
			if (document.getElementById('possibleUsers') != null)
			{
				var possibleListOptions = document.getElementById('possibleUsers').options;
				
				for (var i = 0; i < possibleListOptions.length; ++i) 
				{
					data.push(possibleListOptions[i].value);
				}
			}
			return data;
		}
		
		function restartPage(button)
		{
			var tournamentId = getSelectedTournament();
			var roundId = getSelectedRound();
			var checker = getSelectedChecker();
			var roundName = document.getElementById('roundName').value;
			var previousRound = document.getElementById('previousRoundSelector').value;
			var strategyData = getAcceptedUsersStrategy();
			var seed = document.getElementById('checkerSeed').value;

			if (roundName != '')
			{
				var data =	{
						'tournamentId' : tournamentId,
						'roundId' : roundId, 
						'checker' : checker,
						'roundName' : roundName,
						'previousRound' : previousRound,
						'activeStrategies' : strategyData,
						'seed' : seed,
					};
				data[button] = true;
				
				$.post
				(
					"jqueryRound.php", 
					data,
					function (data)
					{
						showModalAlert(data);
						if (roundId == -1)
						{
							$.post
							(
								"jqueryGetNewRoundId.php",
								{},
								function (data)
								{
									var id = parseInt(data);
									console.log(id);
									loadRoundData(tournamentId, id);
								}
							);
						}
						else loadRoundData(tournamentId, roundId);
					}
				);
			}
			else showModalAlert('Название раунда не может быть пустым!')
		}
		
		function loadFormData()
		{
			var roundId = getSelectedRound();
            restartPage(roundId == -1 ? 'createRound' : 'updateRound');
            showModalAlert("Всё хорошо!");
            window.location.reload();
		}
		
		function startRound()
		{
			restartPage('startRound');
		}
		
		function openRound()
		{
			restartPage('openRound');
        }

        function closeRound()
		{
			restartPage('closeRound');
		}
		
		function loadRoundUsers(tournamentId, roundId)
		{
			$.post(
				"jqueryGetRoundUsers.php",
				{
					'roundId' : roundId, 
					'tournamentId' : tournamentId
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
		
		function changeRoundActivity(roundId, mode, users, value, previousRound, seed)
		{
			var data = 
			{
				'mode'			: mode,
				'roundId' 		: roundId,
				'users'			: users,
				'value'			: value,
				'previousRound' : previousRound,
				'seed' : seed
			};
			
			$.post("jqueryRoundActivityControl.php", data);
			
			loadRoundData(getSelectedTournament(), roundId);
		}
		
		function clearAcceptedUsers(roundId)
		{
			var checker = document.getElementById('clearAcceptedUsersButton');
			var seed = document.getElementById('checkerSeed').value;
			changeRoundActivity(roundId, 'clearAcceptedUsers', [], -1, -1, seed);
		}
		
		function acceptAllUsers(roundId)
		{
			var checker = document.getElementById('acceptPossibleUsersButton'); 
			var possibleUsers = getPossibleUsersStrategy();
			var seed = document.getElementById('checkerSeed').value;
			changeRoundActivity(roundId, 'acceptPossibleUsers', possibleUsers, -1, -1, seed);
		}
		
		function acceptFirstNthUsers(roundId)
		{
			var checker = document.getElementById('acceptFirstNthUsersCheckbox');
			var value = document.getElementById('acceptFirstNthUsersLabel').value; 
			if (checker.checked)
			{
				var seed = document.getElementById('checkerSeed').value;
				var possibleUsers = getPossibleUsersStrategy();
				changeRoundActivity(roundId, 'acceptFirstNthUsers', possibleUsers, value, -1, seed);
			}
		}
		
		function declineLastUser(roundId, previousRoundId)
		{
			var checker = document.getElementById('declineLastUsersCheckbox');
			var value = document.getElementById('declineLastUsersLabel').value; 
			if (checker.checked)
			{
				var seed = document.getElementById('checkerSeed').value;
				var possibleUsers = getPossibleUsersStrategy();
				changeRoundActivity(roundId, 'declineLastUsers', possibleUsers, value, previousRoundId, seed);
			}
		}
		
		function acceptNewUser(roundId)
		{
			var possibleUsers = document.getElementById('possibleUsers');
			if (possibleUsers)
			{
				var index = possibleUsers.selectedIndex;
				if (index != -1)
				{
					var strategyId = possibleUsers[index].value;
					var seed = document.getElementById('checkerSeed').value;
					changeRoundActivity(roundId, 'acceptPossibleUsers', [strategyId], -1, -1, seed);
				}
			}
		}
		
		function declineUser(roundId)
		{
			var acceptedUsers = document.getElementById('acceptedUsers');
			if (acceptedUsers)
			{
				var index = acceptedUsers.selectedIndex;
				if (index != -1)
				{
					var strategyId = acceptedUsers[index].value;
					var seed = document.getElementById('checkerSeed').value;
					changeRoundActivity(roundId, 'declineUsers', [strategyId], -1, -1, seed);
				}
			}
		}
	</script>

		<form role="form" method="post">
			<div class="form-group">
				<label for = "tournamentSelector" class = "APfont">Турнир:</label>
				<select id = "tournamentSelector" class="form-control" onchange="loadRoundData(getSelectedTournament(), -1)">
					<option value = "-1">Турнир не выбран</option>
					<?php
						foreach ($tournamentList as $tournament)
						{
					?>
						<option value = "<?php echo $tournament['id']; ?>">
							<?php echo $tournament['name']; ?>
						</option>
					<?php
						}
					?>
				</select>
				<script>
					setSelectedIndexInSelector('tournamentSelector', <?php echo $tournamentId; ?>);
				</script>
			</div>
			<br>
			<div class="form-group">
				<label for = "roundSelector" class = "APfont">Раунд:</label>
				<select id = "roundSelector" class="form-control" onchange="loadRoundData(getSelectedTournament(), getSelectedRound())">
					<option value = "-1">Новый раунд</option>
					<?php
						foreach ($roundList as $round)
						{
					?>
						<option value = "<?php echo $round['id']; ?>">
							<?php echo $round['name']; ?>
						</option>
					<?php
						}
					?>
				</select>
				<script>
					setSelectedIndexInSelector('roundSelector', <?php echo $roundId; ?>);
				</script>
			</div>
					<br>
			<div class="form-group"> 
				<label for="roundName" class = "APfont">Название раунда:</label>
				<input type="text" class="form-control" id="roundName" placeholder="Введите название раунда" value = "<?php if ($roundId != -1) echo $currentRound['name']; ?>" <?php if ($isEnded) { ?> disabled <?php } ?>>
			</div>
			
			<br>
			<div class="form-group">
				<label for = "checkerSelector" class = "APfont">Текущий тестировщик:</label>
				<select id = "checkerSelector" class="form-control" <?php if ($isEnded) { ?> disabled <?php } ?>>
					<option value = "-1">Отсуствует</option>
					<?php
						foreach ($roundCheckerList as $checker)
						{
					?>
						<option value = "<?php echo $checker['id']; ?>">
							<?php echo $checker['name']; ?>
						</option>
					<?php
						}
					?>
				</select>
				<script>
					setSelectedIndexInSelector('checkerSelector', <?php if ($currentRound != -1) echo $currentRound['checker']; else echo $currentRound; ?>);
				</script>
			</div>

			<br>
			<div class="form-group">
				<label for="checkerSeed" class = "APfont">Начальное значение генератора случайных чисел:</label>
				<input id="checkerSeed" type="text" class="form-control" placeholder="Начальное значение"
					<?php if ($isEnded) { ?> disabled <?php } ?>
					value=<?php if ($currentRound != -1) echo $currentRound['seed']; else echo 0; ?>>
			</div>
			
			<br>
			<div class="form-group">
				<label for = "previousRoundSelector" class = "APfont">Предыдущий раунд:</label>
				<select id = "previousRoundSelector" class="form-control" <?php if ($isEnded) { ?> disabled <?php } ?>>
					<option value = "-1">Отсуствует</option>
					<?php
						foreach ($roundList as $round)
						{
							if ($round['id'] != $roundId)
							{
					?>
							<option value = "<?php echo $round['id']; ?>">
								<?php echo $round['name']; ?>
							</option>
					<?php
							}
						}
					?>
				</select>
				<script>
					setSelectedIndexInSelector('previousRoundSelector', <?php if ($currentRound != -1) echo $currentRound['previousRound']; else echo $currentRound; ?>);
				</script>
			</div>
			<br>
			<?php 
				if ($tournamentId != -1 && $roundId != -1)
				{
			?>
			<?php
				if (checkRoundEnding($roundId) == false)
				{
			?>
			<label class = "APfont">Фильтры участников:</label>
			<div class="form-group">
				<table class="table table-condensed">
					<tr>
						<?php
							if ($currentRound['previousRound'] != -1)
							{
						?>
						<td>
							<div>
								<div class="checkbox">
									<label>
										<input type="checkbox" id = "acceptFirstNthUsersCheckbox" onchange = "acceptFirstNthUsers(<?php echo $roundId;?>);"> Принять первых N человек
									</label>
								</div>
								<div>
									<label class = "inlineLabel">N:</label><input id = "acceptFirstNthUsersLabel" class = "form-control inlineInput" type="text" class="form-control" id="upperLimitCount">
								</div>
							</div>
						</td>
						<td>
							<div>
								<div class="checkbox">
									<label>
										<input id = "declineLastUsersCheckbox" type="checkbox" onchange = "declineLastUser(<?php echo $roundId;?>, <?php echo $currentRound['previousRound'];?>);"> Исключить игроков с счетом <= M
									</label>
								</div>
								<div>
									<label class = "inlineLabel">M:</label><input id = "declineLastUsersLabel" class = "form-control inlineInput" type="text" class="form-control" id="lowerLimitCount">
								</div>
							</div>
						</td>
						<?php
							}
						?>
					</tr>
				</table>
			</div>
			<?php
				}
			?>
			
			<br>
			
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
<?php
                                                            if (checkRoundEnding($roundId) == false)
                                                            {
?>
                                                                onclick="acceptNewUser(<?php echo $roundId;?>);"
<?php
                                                            }
?>
                                                            multiple class="form-control">
							</select>
						</td>
						<td class = "filterListTd">
							<button class = "button" id = "acceptPossibleUsersButton" <?php if (checkRoundEnding($roundId) == false) { ?> onclick = "acceptAllUsers(<?php echo $roundId;?>); return false;" <?php } ?> > >> </button>
							<br>
							<br>
							<button class = "button" id = "clearAcceptedUsersButton" <?php if (checkRoundEnding($roundId) == false) { ?> onclick = "clearAcceptedUsers(<?php echo $roundId;?>); return false;" <?php } ?> > << </button>
						</td>
						<td class = "AcceptedListWidth">
							<select id = "acceptedUsers" <?php if (checkRoundEnding($roundId) == false) {?> onclick="declineUser(<?php echo $roundId;?>);" <?php } ?> multiple class="form-control">
							</select>
						</td>
					</tr>
				</table>
			</div>
			<script>
				loadRoundUsers(<?php echo $tournamentId; ?>, <?php echo $roundId; ?>);
			</script>
			<br>
			<?php
				$roundDuel = intval(countDuelsInRound($roundId));
				$roundVisible = intval(isRoundVisible($roundId));
				$checkedDuels = intval(getCheckedDuels($roundId));
				if ($roundDuel == 0)
					{
			?>
					<?php
						if (getAcceptedRoundUsers($roundId) > 1)
						{
					?>
					<button type = "submit" name = "startRoundButton" onclick = "startRound(); return false;" class = "btn btn-default">Начать текущий раунд</button>
					<br>
					<?php
						}
					?>
			<?php
					}
				
				if ($roundDuel > 0 && $roundVisible == 0)
					{
			?>
					<?php
						if ($checkedDuels != $roundDuel) 
						{
					?>
					<p>Проверено <?php echo $checkedDuels;?> из <?php echo $roundDuel; ?> дуэлей раунда <?php echo $roundId; ?></p>
					<br>
					<?php
						}
					?>
					<button type = "submit" name = "openRoundButton" onclick = "openRound(); return false;" class = "btn btn-default">Открыть раунд для просмотра</button>
					<br>
			<?php 
                    };
                if ($roundVisible == 1)
                {
            ?>
                <button type = "submit" name = "closeRoundButton" onclick = "closeRound(); return false;" class = "btn btn-default">Закрыть раунд для просмотра</button>
            <br />
            <?php
                }
				}
			?>
			<button type = "submit" name = "submit" onclick = "loadFormData(); return false;" class = "btn btn-default">
				<?php 
					if ($roundId == -1)
						echo 'Создать раунд';
					else
						echo 'Применить изменения';
				?>
			</button>
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
