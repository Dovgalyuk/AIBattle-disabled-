// переключение активной кнопки в админ. панели 

function changeActiveAdminButton(state)
{
	var adminButtons = 
	[
		'tournamentButton', 'gameButton', 'roundsButton', 
		'checkersButton', 'attachmentsButton', 'usersButton', 
		'newsButton', 'faqButton', 'duelsButton', 'imageUploadButton',
		'imageViewerButton'
	];
	
	
	//console.log(state + ':');
	
	adminButtons.forEach
	(
		function check(button)
		{
			
			//console.log(button);
			if (state == button)
			{
				$('#' + button).removeClass("btn-default").addClass("btn-info");
			}
			else if ($('#' + button).hasClass("btn-info")) 
			{
				$('#' + button).removeClass("btn-info").addClass("btn-default");
			}
			
		}
	);
}

// переключение активной кнопки в турнирах
function changeActiveTournamentButton(state)
{
	var tournamentButtons = 
	[
		'gameButton', 'roundButton',
		'stategyButton', 'trainingButton'
	];
	
	tournamentButtons.forEach
	(
		function check(button)
		{
			if (document.getElementById(button))
			{
				if (state == button)
				{
					$('#' + button).removeClass("btn-default").addClass("btn-info");
				}
				else if ($('#' + button).hasClass("btn-info")) 
				{
					$('#' + button).removeClass("btn-info").addClass("btn-default");
				}
			}
		}
	);
}