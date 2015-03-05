<?php

class AuthController extends Zend_Controller_Action
{
	private $_arrParam;
    public function init(){
        parent::init();        
        $this->_arrParam = $this->_request->getParams();
        $this->view->arrParam = $this->_arrParam;
    }
    

    public function loginAction()
    {
        if($this->_request->isPost()){
           	//check validate error tu form
            $auth = new Vqt_System_Auth();
            
            if($auth->login($this->_arrParam)){
            	if($uri_callback = $this->_request->getParam('uri_callback') ){
            		$this->_redirect($uri_callback);
            	}else{
            		$this->_redirect("/admin/report");
            	}
            }else{
            	$this->view->system_msg = "Username or password is wrong!";
            }
        }
    }
    
    public function logoutAction()
    {
        $auth = new Vqt_System_Auth();
        $auth->logout();
        $this->_redirect('/');
    }
}
		