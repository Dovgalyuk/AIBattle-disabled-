<?php include_once('procedures.php'); ?>

<?php include("top.php"); ?>
<?php $id = (isset($_GET['id']) && isAdmin()) ? $_GET['id'] : getActiveUserID();?>

<script>
	$(window).load
	(
		function()
		{
			document.getElementById('submitDiv').style.display = 'none';
		}
	)
</script>

<script>
	function checkPasswordStatus()
	{
		var status = "";
		
		if ($("#newPassword").val() == $("#tryAnotherOnePassword").val())
		{
			if ($("#newPassword").val() != "")
			{
				$("#passwordStatus").removeClass().addClass('greenColored').html('Пароли совпадают!');
				document.getElementById('submitDiv').style.display = 'block';
				return true;
			}
			else
			{
				$("#passwordStatus").removeClass().addClass('redColored').html('Пароли пусты!');
				document.getElementById('submitDiv').style.display = 'none';
				return false;
			}
		}
		else
		{
			$("#passwordStatus").removeClass().addClass('redColored').html('Пароли не совпадают!');
			document.getElementById('submitDiv').style.display = 'none';
			return false;
		}
	}
	
<?php if (isActiveUser()) { ?>
	function changePassword()
	{
		if (checkPasswordStatus())
		$.post
		(
			'jqueryUpdateUserPassword.php<?php if (isAdmin() && isset($_GET['id'])) echo "?id=".$_GET['id']?>', 
			{ 'newPassword' : $("#newPassword").val()},
			function(data) 
			{
				showModalAlert(data);
				window.location.reload();
			}
		)
  };

    function changeInfo()
    {
        $.post
        (
            'jqueryUpdateUserInfo.php<?php if (isAdmin() && isset($_GET['id'])) echo "?id=".$_GET['id']?>',
        {
            'newName' : $('#newName').val(),
            'newSurname' : $('#newSurname').val(),
            'newPatronymic' : $('#newPatronymic').val()
            <?php if(isAdmin()) { ?>, 'group' : $('#group').val()<?php } ?>
        },
        function (data)
        {
            showModalAlert(data);
            window.location.reload();
        }
    )
  }

<?php } ?>
</script>

<div class = "container content">
  <div class="titleName">Пользователь: <?php echo getNicknameById((isAdmin() && isset($_GET['id'])) ? $_GET['id'] : getActiveUserID()) ?></div>
	<div class="titleName">Изменение пароля</div>
	<form class="form-signin" role="form" method="POST">
		<input class="form-control" id = "newPassword" type="password" required="" placeholder="Новый пароль" onchange = "checkPasswordStatus()"> 
		<input class="form-control" id = "tryAnotherOnePassword" type="password" required="" placeholder="Повторный набор пароля" onchange = "checkPasswordStatus();">
		<div>
			<p id = "passwordStatus" class = ""></p>
		</div>
		<br>
		<div id = "submitDiv">
<?php if (isActiveUser()) echo '			<button type="submit" id = "submitButton" class="btn btn-lg btn-primary btn-block" onclick = "changePassword(); return false;">Изменить пароль</button>'; ?>
		</div>
  </form>
	<div class="titleName">Изменение информации</div>
	<form class="form-signin" role="form" method="POST">
<input class="form-control" id = "newSurname" value="<?php
echo getUserSurname((isAdmin() && isset($_GET['id'])) ? $_GET['id'] : "");
?>" type="text" placeholder="Фамилия">
  <input class="form-control" id = "newName" value="<?php
echo getUserRealName((isAdmin() && isset($_GET['id'])) ? $_GET['id'] : ""); 
?>" type="text" placeholder="Имя"> 
    <input class="form-control" id = "newPatronymic" value="<?php
echo getUserPatronymic((isAdmin() && isset($_GET['id'])) ? $_GET['id'] : "");
?>" type="text" placeholder="Отчество">
<?php
    if (isAdmin() && ($id != getActiveUserID()))
    {
?>
<br /> 
Роль: <select id="group" class="form-control">
<option value="user" <?php if (isUserInGroup('user', $id)) echo "selected"?> >Пользователь</option>
<option value="moder" <?php if (isUserInGroup('moder', $id)) echo "selected"?> >Модератор</option>
<option value="news" <?php if (isUserInGroup('news', $id)) echo "selected"?> >Создатель новостей</option>
<option value="admin" <?php if (isUserInGroup('admin', $id)) echo "selected"?> >Администратор</option>
<option value="banned" <?php if (isUserInGroup('banned', $id)) echo "selected"?> >Заблокированный</option>
</select>
<?php } else { ?>
Роль:<br />
<?php 
      if (isUserInGroup('user', $id)) echo "Пользователь";
      if (isUserInGroup('moder', $id)) echo "Модератор";
      if (isUserInGroup('news', $id)) echo "Создатель новостей";
      if (isUserInGroup('admin', $id)) echo "Администратор";
}; 
?>
 
		<div>
			<p id = "passwordStatus" class = ""></p>
		</div>
		<br>
		<div id = "submitDiv2"><button type="submit" id = "submitButton2" onclick="changeInfo(); return false;" class="btn btn-lg btn-primary btn-block">Изменить информацию</button>
		</div>
	</form>

</div>

<?php
if (!isActiveUser()) echo '<script>location.replace("userAuthorization.php")</script>';
?>

<?php include("bottom.php"); ?>
