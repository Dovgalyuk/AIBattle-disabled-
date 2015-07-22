<?php
    include_once('procedures.php');
    $val = 0;
    $val += setUserRealName($_POST['newName'], 
        (isAdmin() && isset($_GET['id'])) ? $_GET['id'] : getActiveUserID());
    $val += setUserSurname($_POST['newSurname'],
        (isAdmin() && isset($_GET['id'])) ? $_GET['id'] : getActiveUserID());
    $val += setUserPatronymic($_POST['newPatronymic'],
        (isAdmin() && isset($_GET['id'])) ? $_GET['id'] : getActiveUserID());
    if (isAdmin())
    {
        if (isset($_POST['group']))
        {
            $val += setUserGroup($_POST['group'], $_GET['id']);
        }
    };
    if ($val == 0)
        echo "Информация успешно обновлена!";
    else
        echo "При обновлении информации возникли ошибки: $val"
?>
