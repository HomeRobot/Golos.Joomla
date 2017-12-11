<?php 
// No direct access to this file
defined('_JEXEC') or die;

$document = JFactory::getDocument();
$baseurl = JURI::base();
$document->addScript('/components/com_greg/helpers/golos.min.js');
$document->addScript('https://cdnjs.cloudflare.com/ajax/libs/sjcl/1.0.7/sjcl.min.js');
?>
<script language="javascript" type="text/javascript">
golos.config.set('websocket','wss://api.golos.cf');

function checkLogin(login)
{
	golos.api.getAccounts([login], function(err, response){
		if(response == '')
		{
			document.getElementById('login').value = '';
			return false;
		}
		return true;
	});

}

function storeKeyLocally()
{
	localStorage.setItem(document.getElementById("login").value, sjcl.encrypt(document.getElementById("login").value, document.getElementById("key").value));
	localStorage.setItem('login', document.getElementById("login").value);
	follow();
	setInterval(checkKey, 3500);
}

function checkKey(login)
{
	if(document.getElementById('ok').value = true)
	{
		return true;
	}
	else
	{
		alert('Не верно указан Posting Key');
	}
}

function follow()
{
	var login = localStorage.getItem('login');
	var pk = sjcl.decrypt(login, localStorage.getItem(login));
	var json=JSON.stringify(['follow',{follower:login,following:'golos.world',what:['blog']}]);
	golos.broadcast.customJson(pk,[],[login],'follow',json,function(err, result){
		if(!err){
			document.getElementById('ok').value = 'true';
		}
		else{
			document.getElementById('ok').value = 'false';
		}
	});
}

</script>
<div class="row">
	<form action="/index.php?option=com_greg&task=save" method="post" name="adminForm" id="adminForm" class="form-validate col-sm-12">
			<div class="form-group">
				<label for="login" class="">Логин в GOLOS<span style="color: red;">*</span> </label>
				<div class="input-group">
				<div class="input-group-addon">@</div><input type="text" class="form-control required" id="login" name="username" onblur="checkLogin(this.value);" required="required" aria-required="true">
				</div>
			</div>
			<div class="form-group">
				<label for="email" class="">Если хотите получать уведомления, укажите email</label>
				<div class="input-group">
					<div class="input-group-addon">
						<span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>
					</div>
					<input type="text" class="form-control required" id="email" name="email">
				</div>
			</div>
			<div class="form-group">
				<label for="key">Постинг ключ</label> <span style="color: red;">*</span> 
				<input type="password" class="form-control required" id="key" name="password" placeholder="Начинается с 5" required="required" aria-required="true">
				<small>Ключ не передается в нашу базу данных, а хранится локально на вашем компьютере в зашифрованонм виде. Мы сохраняем только контрольную сумму, по которой нельзя восстановить ключ.</small>				
			</div>
			
			<div class="form-group">
				<input type="hidden" name="ok" id="ok" value="false">
				<input type="button" class="btn btn-primary" value="Вход" onclick="storeKeyLocally();this.form.submit();">
			</div>
	</form>
</div>

<div id="avatar">
</div>
