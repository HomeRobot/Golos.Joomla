<?php defined('_JEXEC') or die( 'Restriction access' );

jimport( 'joomla.application.component.view');


if (!class_exists('JViewLegacy')){
    class JViewLegacy extends JView {

    }
}


class GregViewReg extends JViewLegacy
{
	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$pathway	= $app->getPathway();
		$params 	= $app->getParams();
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		
		if ( $user->guest ) {
			$params->set('page_title', JText::_('REGISTRATION_FORM_TITLE') );
		} else {			
			$params->set('page_title', JText::_('ALREADY_REGISTERED_FORM_TITLE') );
		}

        $document->setTitle( $params->get( 'page_title' ) );
        
		$pathway->addItem(JText::_('New'), '');
		
		JHTML::_('behavior.formvalidation');
		
		$this->assignRef('params', $params);
		parent::display($tpl);
	}
}
?>
