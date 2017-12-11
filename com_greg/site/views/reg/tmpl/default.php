<?php defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
$baseurl = JURI::base();
$document->addScript('/media/com_greg/js/golos.min.js');
?>
<script language="javascript" type="text/javascript">
function submitbutton(pressbutton)
{
	varEmail1 = $('email').value;

	if ( varEmail1 == "" ) {
		alert( "Неверно указан email" );
	} else if (! document.formvalidator.isValid(form) ) {
		var msg = 'Некоторые значения указаны неверно. Попробуйте еще раз.';
		if($('email').hasClass('invalid')){msg += '\n\n\t* Некорректный Email';}
		alert(msg);
	} else {
		submitform( pressbutton );
	}
}

function checkLogin(login)
{
	steem.api.getAccounts([login], function(err, response){
	if(response == '')
	{
		document.getElementById('login').value = '';
		return false;
	}
	return true;
});
}

</script>

<style type="text/css">
	.invalid {border: red;color:red;}
</style>

<?php if ( $this->params->def( 'show_page_title', 1 ) ) : ?>
	<h1 class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
		<?php echo JText::_('REGISTRATION_FORM_TITLE'); ?>
	</h1>
<?php endif; ?>

<!-- div names and label classes are equivalent to those of com_user registration form -->
<div class="joomla">
	<div class="registration">
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-validate col-sm-6 col-sm-offset-3" enctype="multipart/form-data">
			<div class="form-group">
				<label for="email"><?php echo JText::_( 'EMAIL' ); ?> <span style="color: red;">*</span></label>
				<input type="email" class="form-control validate-email required" id="email" name="email" placeholder="Email" required="required">
			</div>
			<div class="form-group">
				<label for="login"><?php echo JText::_('COM_GREG_LOGIN'); ?> <a href="https://golos.io" target="_blank">GOLOS</a> <span style="color: red;">*</span> <a href="https://golos.io/create_account" target="_blank"><?php echo JText::_('COM_GREG_REG'); ?></a></label>
				<div class="input-group">
				<div class="input-group-addon">@</div><input type="text" class="form-control required" id="login" name="login" onBlur="checkLogin(this.value);" required="required">
				</div>
			</div>
			<div class="form-group">
				<label for="key"><?php echo JText::_('COM_GREG_POSTING_KEY'); ?></label> <span style="color: red;">*</span> </label>
				<input type="text" class="form-control required" id="key" name="key" placeholder="" required="required">
				<?php echo JText::_('COM_GREG_SAVE_KEY'); ?>				
			<button class="btn btn-info" data-toggle="collapse" data-target="#hide-me"><?php echo JText::_('COM_GREG_FIND_KEY'); ?></button>
			<div id="hide-me" class="collapse">
				<?php echo JText::_('COM_GREG_FIND_KEY_INSTRUCTION'); ?>
			</div>
			</div>
			
			<div class="form-group">
				<input type="submit" class="btn btn-primary" value="<?php echo JText::_('REGISTRATION_FORM_TITLE'); ?>" >
			</div>
			<input type="hidden" name="option" value="com_greg" />
			<input type="hidden" name="task" value="save" />
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
	</div>
</div>
