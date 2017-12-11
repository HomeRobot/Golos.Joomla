<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Greg
 * @author     Joopiter.ru <support@joopiter.ru>
 * @copyright  2017 Joopiter.ru
 * @license    GNU General Public License версии 2 или более поздней; Смотрите LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Class GregController
 *
 * @since  1.6
 */
class GregController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean $cachable  If true, the view output will be cached
	 * @param   mixed   $urlparams An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController   This object to support chaining.
	 *
	 * @since    1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
        $app  = JFactory::getApplication();
        $view = $app->input->getCmd('view', 'reg');
		$app->input->set('view', $view);

		parent::display($cachable, $urlparams);

		return $this;
	}
	
	public function save()
	{
		$app  = JFactory::getApplication();
		
		jimport('joomla.application.component.helper');    
		jimport('joomla.filter.filterinput');
		jimport('joomla.mail.helper');
		jimport('joomla.user.helper');	
		
		JPluginHelper::importPlugin('user');
		$lang = JFactory::getLanguage();
		$lang->load('com_user');
		$lang->load('com_users');
		
		$filter = JFilterInput::getInstance();
		$username = $filter->clean( $app->input->getUsername('username', '') );
		$password = $app->input->getVar('password');
		$email = $app->input->getVar('email');
		if($email == '')
		{
			$email = $username . '@' . $_SERVER['SERVER_NAME'];
		}
		
		$db = JFactory::getDBO();
		$query = "select count(*) from `#__users` where username = '$username'";
		$result = $db->setQuery($query);   
		$n = $db->loadResult();
		if($n > 0) // пользователь существует
		{
			$credentials = array( 'username'=> $username, 'password'=> $password );
			//В этом массиве параметры авторизации! в данном случае это установка запоминания пользователя
			$options = array( 'remember'=>true );
			//выполняем авторизацию
			if( JFactory::getApplication()->login( $credentials, $options )){
				$url = JRoute::_( '/' );
				JControllerLegacy::setRedirect( $url, '');
				return true;
			}
			else
			{
				JError::raiseWarning('', 'Неверно указаны данные для входа');
				return false;				
			}	
			return;	
		}
		
		//добавляем пользователя
		$data = array();
		$data['name'] 		= $username;
		$data['username'] 	= $username;
		$data['email'] 		= $email;
		$data['email1'] 	= $email;
		$data['password'] 	= $password;
		$data['password1'] 	= $password;
		$data['password2'] 	= $password;
		$data['password_clear'] = $password;
		$data['sendEmail'] 	= 0;
		
		$activation = $this->registerUser($data);
				
		if($activation)
		{
			$query = "update `#__users` set block = 0, activation = '' where email = '$email'";
			$result = $db->setQuery($query);   
			$result = $db->execute();
			
			$query = "select * from `#__users` where email = '$email'";
			$result = $db->setQuery($query);   
			$user = $db->loadObject();
			$credentials = array( 'username' => $username, 'password' => $password );
			$options = array( 'remember' => true );
			if( JFactory::getApplication()->login( $credentials, $options )){
				$url = JRoute::_( '/' );
				JControllerLegacy::setRedirect( $url, '');
				return true;
			}
		}
		else
		{
			$message = 'Ошибка регистрации. Попробуйте ввести данные еще раз.';
			JFactory::getApplication()->enqueueMessage($message);
			return false;
		}
	}
	
	protected function registerUser($data)
	{
		jimport('joomla.application.component.helper');
		jimport('joomla.filter.filterinput');
		jimport('joomla.mail.helper');
		jimport('joomla.user.helper');		

		$config = JFactory::getConfig();
		$db		= JFactory::getDBO();
		$params = JComponentHelper::getParams('com_users');
		$group_id = $params->get('new_usertype');

		$lang = JFactory::getLanguage();
		$lang->load('com_user');
		$lang->load('com_users');
		
		// Initialise the table with JUser.
		$user = new JUser;
		
		// Bind the data.
		if (!$user->bind($data)) {
			$this->setError(JText::sprintf('COM_USERS_REGISTRATION_BIND_FAILED', $user->getError()));
			echo JText::sprintf('COM_USERS_REGISTRATION_BIND_FAILED', $user->getError());
			//die();
			return false;
		}

		// Load the users plugin group.
		JPluginHelper::importPlugin('user');

		// Store the data.
		if (!$user->save()) {
			$this->setError(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError()));
			echo JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError());
			//die();
			return false;
		}
		
		//add to group	
		$query = "select id from `#__users` where email = '".$data['email']."'";
		$result = $db->setQuery($query);   
		$uid = $db->loadResult();
		
		$query = "insert into `#__user_usergroup_map`(user_id, group_id) values ($uid, $group_id)";
		$result = $db->setQuery($query);  
		$result = $db->execute();
		
		// Compile the notification mail values.
		$data = $user->getProperties();
		$data['fromname']	= $config->get('fromname');
		$data['mailfrom']	= $config->get('mailfrom');
		$data['sitename']	= $config->get('sitename');
		$data['siteurl']	= JUri::root();

		//Send Notification mail to administrators //($params->get('useractivation') < 2) && 
		if (($params->get('mail_to_admin') == 1)) {
			$emailSubject = JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			$emailBodyAdmin = JText::sprintf(
				'COM_USERS_EMAIL_REGISTERED_NOTIFICATION_TO_ADMIN_BODY',
				$data['name'],
				$data['username'],
				$data['siteurl']
			);

			// get all admin users
			$query = 'SELECT name, email, sendEmail' .
					' FROM #__users' .
					' WHERE sendEmail=1';

			$db->setQuery( $query );
			$rows = $db->loadObjectList();

			// Send mail to all superadministrators id
			foreach( $rows as $row )
			{
				$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $row->email, $emailSubject, $emailBodyAdmin);

				// Check for an error.
				if ($return !== true) {
					$this->setError(JText::_('COM_USERS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED'));
					//return false;
				}
			}
		}
		
		return true;
	}
}
