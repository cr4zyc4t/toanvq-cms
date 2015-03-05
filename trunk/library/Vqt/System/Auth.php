<?php
class Vqt_System_Auth{

	private $_messages;
    
    public function login($arrParam =  null, $options = null){
            //1. Lay ket noi voi database
            $db = Zend_Db_Table::getDefaultAdapter();
            
            //2. 
            $authAdapter = new Zend_Auth_Adapter_DbTable($db);
            $authAdapter->setTableName('users')
                        ->setIdentityColumn('username')
                        ->setCredentialColumn('password');
            //3.
            $authAdapter->setIdentity($arrParam['username']);
            $password = md5($arrParam['password']);
            $authAdapter->setCredential($password);
            
            //4.
            $select = $authAdapter->getDbSelect();
            $select->where("status = 1");
            
            //5.
            $auth = Zend_Auth::getInstance();
            $result = $auth->authenticate($authAdapter);
            $flag = false;
            
            if($result->isValid()){                
                $returnColumns = array('id','username','name','email','password', 'role');
                $omitColumns = array('password');
                $data = $authAdapter->getResultRowObject(null , $omitColumns);
                $auth->getStorage()->write($data);
                $flag = true;
            }else{
                $this->_messages = $result->getMessages();
            }
        return $flag;
    }
    
    public function getMessages(){
        return $this->_messages;
    }
    
    public function logout(){
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
    }
}
