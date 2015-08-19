<?php
    // Получить все текущие турниры
    function getRunningAndClosedTournaments()
    {
        $link = getDBConnection();
        $tournaments = array();
        
        if (mysqli_select_db($link, getDBName()))
        {
            $query = mysqli_query($link, "SELECT id, name FROM tournaments" . ((!isAdmin()) ? " WHERE state = 'running' OR state = 'closed'" : ""));
            while ($data = mysqli_fetch_assoc($query))
                $tournaments[$data['id']] = $data['name'];
        }
        
        return $tournaments;
    }

    // Турнир
    
    // Получение информации о турнире
    function getTournamentData($tournamentId)
    {
        $link = getDBConnection();
        $data = array();
        if (mysqli_select_db($link, getDBName()))
        {
            $tournamentId = intval($tournamentId);
            $query = mysqli_query($link, "SELECT * FROM tournaments WHERE id = $tournamentId LIMIT 1");
            $data = mysqli_fetch_assoc($query);
            mysqli_free_result($query);
        }
        
        return $data; 
    }
    
    // получение игры по турниру
    function getGameByTournament($tournamentId)
    {
        if ($tournamentId == -1)
            return -1;
        else 
        {
            $data = getTournamentData($tournamentId);
            return $data['game'];
        }
    }

    // Получение описания турнира
    function getTournamentDescriptionByTournamentId($tournamentId)
    {
        $link = getDBConnection();
        $tournamentDescription = "none";
        if (mysqli_select_db($link, getDBName()))
        {
            $tournamentId = intval($tournamentId);
            $query = mysqli_query($link, "SELECT description FROM tournaments WHERE id = $tournamentId LIMIT 1");
            $data = mysqli_fetch_assoc($query);
            $tournamentDescription = $data['description'];
            mysqli_free_result($query);
        }
        return $tournamentDescription;
    }
    
    // Получить раунды турнира
    function getTournamentRounds($tournamentId)
    {
        $link = getDBConnection();
        $data = array();
        if (mysqli_select_db($link, getDBName()))
        {
            $tournamentId = intval($tournamentId);
            $query = mysqli_query($link, "SELECT * FROM rounds WHERE tournament = $tournamentId");
            while ($row = mysqli_fetch_assoc($query))
                $data[] = $row;
            mysqli_free_result($query);
        }
        return $data;
    }
    
    // получить данные по играм
    function getTournamentList($tournamentId = -1)
    {
        $link = getDBConnection();
        $data = array();
        if (mysqli_select_db($link, getDBName()))
        {
            $tournamentId = intval($tournamentId);
            if ($tournamentId == -1)
                $query = mysqli_query($link, "SELECT * FROM tournaments");
            else
                $query = mysqli_query($link, "SELECT * FROM tournaments WHERE id = $tournamentId");
                
            if ($tournamentId == -1)
                while ($row = mysqli_fetch_assoc($query))
                    $data[] = $row;
            else
                $data = mysqli_fetch_assoc($query);
            
            mysqli_free_result($query);
        }
        return $data;
    }

    // создать турнир
    function createNewTournament($name, $game, $description, $state, $checker)
    {
        if (!isAdmin())
        {
            return 3;
        }
        $link = getDBConnection();
        if (mysqli_select_db($link, getDBName()))
        {
            $name           = mysqli_real_escape_string($link, $name);
            $description    = mysqli_real_escape_string($link, $description);
            $state          = mysqli_real_escape_string($link, $state);
            $game           = intval($game);
            $checker        = intval($checker);
            
            if (mysqli_query($link, "INSERT INTO tournaments SET name = '$name', game = $game, description = '$description', state = '$state', defaultChecker = $checker"))
                return 0;
            else
                return 1;
        }
        else return 2;
    }
    
    // обновить турнир
    function updateTournament($id, $name, $game, $description, $state, $checker)
    {
        if (!isAdmin())
        {
            return 3;
        }
        $link = getDBConnection();
        if (mysqli_select_db($link, getDBName()))
        {
            $id             = intval($id);
            $game           = intval($game);
            $checker        = intval($checker);
            $name           = mysqli_real_escape_string($link, $name);
            $description    = mysqli_real_escape_string($link, $description);
            $state          = mysqli_real_escape_string($link, $state);
            
            if (mysqli_query($link, "UPDATE tournaments SET name = '$name', game = $game, description = '$description', state = '$state', defaultChecker = $checker WHERE id = $id"))
                return 0;
            else
                return 1;
        }
        else return 2;
    }
    
    // удалить турнир
    function deleteTournament($id)
    {
        if (!isAdmin())
        {
            return 3;
        }
        $link = getDBConnection();
        if (mysqli_select_db($link, getDBName()))
        {
            $id = intval($id);
            if (mysqli_query($link, "DELETE FROM tournaments WHERE id = $id"))
                return 0;
            else
                return 1;
        }
        else return 2;
    }
    
    // получить список раундов турнира
    function getRoundList($tournamentId, $roundId = -1)
    {
        $link = getDBConnection();
        $data = array();
        if (mysqli_select_db($link, getDBName()))
        {
            $tournamentId   = intval($tournamentId);
            $roundId        = intval($roundId);
            $text = "SELECT * FROM rounds WHERE tournament = $tournamentId";
            if ($roundId != -1)
                $text .= " AND id = $roundId";
            $text .= " ORDER BY id";
            $query = mysqli_query($link, $text);
            
            if ($roundId == -1) 
            {
                while ($row = mysqli_fetch_assoc($query))
                    $data[] = $row;
            }
            else
            {
                $data = mysqli_fetch_assoc($query);
            }
                
            mysqli_free_result($query);
        }
        return $data;
    }
    
    function getTournamentStrategies($tournamentId)
    {
        $tournamentId = intval($tournamentId);
        $link = getDBConnection();
        $data = array();
        if (mysqli_select_db($link, getDBName()))
        {
            $query = mysqli_query($link, 
                "SELECT str.id AS strategy, usr.login AS login FROM strategies AS str "
                . " INNER JOIN users AS usr ON usr.id = str.user "
                . "WHERE str.tournament = $tournamentId AND str.status = 'ACT'");
            while ($row = mysqli_fetch_assoc($query))
                $data[] = $row;
            mysqli_free_result($query);
        }
        return $data;
    }

?>
