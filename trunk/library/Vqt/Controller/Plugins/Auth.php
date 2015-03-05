<?php
class Vqt_Controller_Plugins_Auth extends Zend_Controller_Plugin_Abstract {

	public function preDispatch(Zend_Controller_Request_Abstract $request) {

		$moduleName = $request->getModuleName();
		$controllerName = $request->getControllerName();
		
		$need_auth = TRUE;
		
		$white_list = array(
			"default" => array("auth", "test"),
			"error" => array()
		);
		
		if ( in_array($moduleName, array_keys($white_list) ) ) {
			if ((count($white_list[$moduleName]) == 0) || in_array($controllerName, $white_list[$moduleName])) {
				$need_auth = FALSE;
			}
		}
		
		if($need_auth){
			$auth = Zend_Auth::getInstance();
			if (!$auth->hasIdentity()) {
				$request->setParam('uri_callback', $request->getRequestUri() );

				$request->setModuleName('default');
				$request->setControllerName('auth');
				$request->setActionName('login');
			}
		}
	}

}
