<?php
	// ������������ ACT-������ � �������� ��������� ���������� ������������
	function setActStatus($strategyID, $game, $tournamentId)
	{
		$link = getDBConnection();
		if (mysqli_select_db($link, getDBName()))
		{
			$strategyID 	= mysqli_real_escape_string($link, $strategyID);
			$game 			= mysqli_real_escape_string($link, $game);
			$tournamentId 	= mysqli_real_escape_string($link, $tournamentId);
			mysqli_query($link, "UPDATE strategies SET status = 'OK' WHERE status = 'ACT' AND user = ".intval(getActiveUserID())." AND game = ".$game." AND tournament = ".$tournamentId);
			mysqli_query($link, "UPDATE strategies SET status = 'ACT' WHERE id = ".$strategyID." AND user = ".intval(getActiveUserID())." AND tournament = ".$tournamentId);
		}
	}
		
	// ��������� �������� �� ������ ���������
	function getUserIdByStrategy($strategyId)
	{
		$link = getDBConnection();
		if (mysqli_select_db($link, getDBName()))
		{
			$strategyId = intval($strategyId);
			$userId = mysqli_query($link, "SELECT user FROM strategies WHERE id = $strategyId");
			return mysqli_result($userId, 0);
		}
		return -1;
	}
	function getNicknameByStrategy($strategyId)
	{
		return getNicknameById(getUserIdByStrategy($strategyId));
	}

	// �������������� ������
	function getStrategyStateColor($state)
	{
		$states = array
		(
			'CE' => "class = danger",
			'OK' => "class = success",
			'ACT' => "class = info"
		);
		
		return $states[$state];
	}
	
	// ����������� �������� ���������
	function getStrategyStatusRusTip($state)
	{
		$states = array
		(
			'CE' => "������ ����������",
			'OK' => "OK",
			'ACT' => "������� ���������"
		);
		
		return $states[$state];
	}
	
	// �������� ���� �� ���������
	function getScoreByStrategy($roundId, $strategyId)
	{
		$link = getDBConnection();
		if (mysqli_select_db($link, getDBName()))
		{
			$roundId = intval($roundId);
			$strategyId = intval($strategyId);
			return mysqli_result(mysqli_query($link, "SELECT score FROM scores WHERE round = $roundId AND strategy = $strategyId"), 0);
		}
	}

    function strategies_getStatistics($roundId)
    {
        $roundId = intval($roundId);
        $link = getDBConnection();
        if (mysqli_select_db($link, getDBName()))
        {
            return mysqli_fetch_all(mysqli_query($link, "SELECT COUNT(*) as cnt, DATE(date) as dt FROM strategies GROUP BY DATE(date)"));
        }
    }
	
?>