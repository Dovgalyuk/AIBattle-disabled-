<?php
    // Принадлежность к группе пользователей
    function isUserInGroup($group, $id="")
    {
        if (isset($_SESSION['SBUserid']))
        {
            $id = ($id == "") ? $_SESSION['SBUserid'] : intval($id);
            $link = getDBConnection();
            if (mysqli_select_db($link, getDBName()))
            {
                $groupExisting = false;
                $group = mysqli_real_escape_string($link, $group);
                $groupQuery = mysqli_query($link, "SELECT `id` FROM `users` WHERE `group` = '$group'");
                while ($adminData = @mysqli_fetch_assoc($groupQuery))
                {
                    if ($id === intval($adminData['id'])) 
                    {   
                        $groupExisting = true;
                        break;
                    }
                }
                return $groupExisting;
            } else return false;
        } else return false;
    }
    
    // Регистрация
    function registerUser($postLogin, $postPassword)
    {
        // $_POST['login']
        // $_POST['password']
        $link = getDBConnection();
        $reason = "";
        if (mysqli_select_db($link, getDBName()))
        {
            $err = array();

            $login = strip_tags($postLogin);
            if ($login != $postLogin)
                $err[] = "Логин содержит некорректные символы";
            $postLogin = mysqli_real_escape_string($link, $postLogin);
            if (strlen($postLogin) < 3 or strlen($postLogin > 30))
                $err[] = "Логин должен быть не меньше 3-х символов и не больше 30";

            $query = mysqli_query($link, "SELECT COUNT(id) FROM users WHERE login='{$login}'");

            if (@mysqli_result($query, 0) > 0)
                $err[] = "Пользователь с таким логином уже существует в базе данных";

            if (count($err) == 0)
            {
                $password = md5(md5(trim($postPassword)));
                mysqli_query($link, "INSERT INTO users SET login='".$postLogin."', password='$password'");
                $reason = "Вы зарегистрированы в системе!";
            }
            else
            {
                $reason = "<b>При регистрации произошли следующие ошибки:</b><br>";
                foreach($err as $error)
                    $reason = $reason.$error."<br>";
            }
        }
        else
        {
            $reason = "Нет возможности подключиться к БД!";
        }

        return $reason;
    }

    // получение стратегий определенного юзера
    function getUserStrategies($gameId, $userId, $tournamentId, $ok, $first=0, $size=-1)
    {
        $link = getDBConnection();
        $strategies = array();
        if (mysqli_select_db($link, getDBName()))
        {
            $gameId = intval($gameId);
            $userId = intval($userId);
            $tournamentId = intval($tournamentId);
            $sql = "SELECT id, user, status FROM strategies WHERE game = $gameId AND user = $userId AND tournament = $tournamentId ";
            if ($ok)
                $sql .= " AND (status = 'OK' OR status = 'ACT') ";
            $query = mysqli_query($link, $sql . " ORDER BY id DESC".(($size != -1) ? " LIMIT $first, $size" : " "));
            while ($data = mysqli_fetch_assoc($query))
                $strategies[] = $data;
        }
        return $strategies;
    }

    function getUserMessages($first=0, $size=-1, $id = 0, $notreaded = false)
    {
        if (!isActiveUser())
            return false;
        $link = getDBConnection();
        $messages = array();
        mysqli_select_db($link, getDBName());
        $userId = intval(getActiveUserID());
        $query = mysqli_query($link, "SELECT * FROM `privateMessages` WHERE (`sender` = $userId OR `recevier` = $userId) ".($id != 0 ? " AND id=".$id : "").($notreaded ? " AND viewed=0" : "")." ORDER BY id DESC".(($size != -1) ? " LIMIT $first, $size" : " "));
        while ($data = mysqli_fetch_assoc($query))
            $messages[$data['id']] = array(
                'sender' => $data['sender'],
                'recevier' => $data['recevier'],
                'title' => $data['title'],
                'text' => $data['text'],
                'date' => $data['date'],
                'viewed' => (($data['sender'] == $userId) ? 1 : $data['viewed'])
            );
        return $messages;
    }
    
    // Дуэли
    function getUserStrategy($gameId, $tournamentId)
    {
        $link = getDBConnection();
        if (mysqli_select_db($link, getDBName()))
        {
            $gameId = intval($gameId);
            $tournamentId = intval($tournamentId);
            return mysqli_query($link, "SELECT id FROM strategies WHERE tournament = $tournamentId AND game = $gameId AND status = 'ACT' AND user=".intval(getActiveUserID()));
        }
    }

    function setUserGroup($newGroup, $id)
    {
        if (!isAdmin()) return 4;
        if ($id == getActiveUserID()) return 4;
        $link = getDBConnection();
        if (($newGroup != "user") && ($newGroup != "moder") && ($newGroup != "news") && ($newGroup != "admin") && ($newGroup != "banned"))
            return 4;
        if (mysqli_select_db($link, getDBName()))
        {
            $currentId = intval($id);
            if (mysqli_query($link, "UPDATE `users` SET `group` = '$newGroup' WHERE id = $currentId"))
                return 0;
            return 1;
        };
        return 2;
    }
    function setUserRealName($newName, $id="")
    {
        if (!isActiveUser()) return 4;
        $newName = htmlspecialchars($newName);
        $link = getDBConnection();
        if (mysqli_select_db($link, getDBName()))
        {
            $newName = mysqli_real_escape_string($link, $newName);
            if (isAdmin() && ($id != ""))
                $currentId = intval($id);
            else
                $currentId = intval(getActiveUserID());

            if (mysqli_query($link, "UPDATE users SET name = '$newName' WHERE id = $currentId"))
                return 0;
            return 1;
        };
        return 2;
    }

    function setUserSurname($newName, $id="")
    {
        if (!isActiveUser()) return 4;
        $newName = htmlspecialchars($newName);
        $link = getDBConnection();
        if (mysqli_select_db($link, getDBName()))
        {
            $newName = mysqli_real_escape_string($link, $newName);
            if (isAdmin() && ($id != ""))
                $currentId = intval($id);
            else
                $currentId = intval(getActiveUserID());

            if (mysqli_query($link, "UPDATE users SET surname = '$newName' WHERE id = $currentId"))
                return 0;
            return 1;
        };
        return 2;
    }

    function setUserPatronymic($newName, $id="")
    {
        if (!isActiveUser()) return 4;
        $newName = htmlspecialchars($newName);
        $link = getDBConnection();
        if (mysqli_select_db($link, getDBName()))
        {
            $newName = mysqli_real_escape_string($link, $newName);
            if (isAdmin() && ($id != ""))
                $currentId = intval($id);
            else
                $currentId = intval(getActiveUserID());

            if (mysqli_query($link, "UPDATE users SET patronymic = '$newName' WHERE id = $currentId"))
                return 0;
            return 1;
        };
        return 2;
    }

    function getUserRealName($id = "")
    {
        if (!isActiveUser()) return "Anonymous";
        $link = getDBConnection();
        if (mysqli_select_db($link, getDBName()))
        {
            if (isAdmin() && ($id != ""))
                $currentId = intval($id);
            else
                $currentId = intval(getActiveUserID());
            $query = mysqli_query($link, "SELECT name FROM users WHERE id = $currentId");
            $res = mysqli_fetch_assoc($query);
            return $res['name'];
        }
    }
    function getUserSurname($id = "")
    {
        if (!isActiveUser()) return "Anonymous";
        $link = getDBConnection();
        if (mysqli_select_db($link, getDBName()))
        {
            if (isAdmin() && ($id != ""))
                $currentId = intval($id);
            else
                $currentId = intval(getActiveUserID());
            $query = mysqli_query($link, "SELECT surname FROM users WHERE id = $currentId");
            $res = mysqli_fetch_assoc($query);
            return $res['surname'];
        }
    }
    function getUserPatronymic($id = "")
    {
        if (!isActiveUser()) return "Anonymous";
        $link = getDBConnection();
        if (mysqli_select_db($link, getDBName()))
        {
            if (isAdmin() && ($id != ""))
                $currentId = intval($id);
            else
                $currentId = intval(getActiveUserID());

            $query = mysqli_query($link, "SELECT patronymic FROM users WHERE id = $currentId");
            $res = mysqli_fetch_assoc($query);
            return $res['patronymic'];
        }
    }

    function getUsersList($ordered = false, $all = false)
    {
        $link = getDBConnection();
        mysqli_select_db($link, getDBName());
        $q = "SELECT * FROM `users` ";
        if (!$all)
            $g .= " WHERE `group` != 'banned'";
        if ($ordered)
            $q .= " ORDER BY login";
        $data = array();
        if ($query = mysqli_query($link, $q))
        {
            while ($row = mysqli_fetch_assoc($query))
                $data[] = $row;
            mysqli_free_result($query);
        }
        return $data;
    }

    function getUsersWithStrategies($tournament)
    {
        $tournament = intval($tournament);
        $link = getDBConnection();
        mysqli_select_db($link, getDBName());
        $q = "SELECT users.id as id, users.login as login FROM users "
             . " INNER JOIN strategies ON users.id = strategies.user "
             . " WHERE `group` != 'banned' AND strategies.tournament = $tournament"
             . " GROUP BY id ORDER BY login";
        $data = array();
        if ($query = mysqli_query($link, $q))
        {
            while ($row = mysqli_fetch_assoc($query))
                $data[] = $row;
            mysqli_free_result($query);
        }
        return $data;
    }
    
?>
