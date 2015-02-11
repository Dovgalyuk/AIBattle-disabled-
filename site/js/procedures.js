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

function showModalAlert(message)
{
    alert(message)
   /* $('.modal').remove();
    var s1 = '<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>      </div><div class="modal-body">'
    var s2 ='</div><div class="modal-footer"><button type="button" class="btn btn-primary" data-dismiss="modal">Close</button></div></div></div></div>'
    var modal = $(s1 + message + s2)
    $('body').append(modal)
    $('.modal').modal()*/
}
