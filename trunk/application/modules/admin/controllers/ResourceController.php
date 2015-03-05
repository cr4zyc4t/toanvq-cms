<?php

class Admin_ResourceController extends Zend_Controller_Action
{
	public function init()
	{
		$option=array(
	        "layout" => "admin",
	        "layoutPath" => APPLICATION_PATH."/layouts/scripts/"
      	);
      	Zend_Layout::startMvc($option);
        $this->view->pageTitle = "Amobi - toanvq";
        include 'toanvq_helper.php';
	}
	
	public function pagesAction()
	{
		$this->view->navibar = navibar(2,1);
		$this->view->pageHeader = "Resource Page";
		
		Zend_Loader::loadClass("admin_models_SourcePage");
		$model = new Admin_Models_SourcePage;
		$this->view->tableData = $model->tableData();
	}
	
	public function addpageAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		
		$id = $this->_request->getParam('id');
		
		$model = new Model_SourcePage;
		
		$this->view->navibar = navibar(2,1);
		$this->view->pageHeader = "Resource Page";
		if ($id) {
			$this->view->pageSubtitle = "Edit";
			$detail = $model->getById($id);
		}else{
			$this->view->pageSubtitle = "Add New";
			$detail = $_POST;
		}
		
		Zend_Loader::loadClass("admin_forms_AddPage");
		$form = new Admin_Forms_AddPage($detail);
		
		if($this->_request->isPost()){
            if($form->isValid($_POST)){
            	$error = array();
				//UPLOAD ICON
				$uploaded = '';
				
				$folder_relative = "/icon/page/";
            	$folder = APPLICATION_PATH."/../public".$folder_relative;
            	if(!is_dir($folder)){
            		mkdir($folder);
            	}
				
            	$uploader = new Zend_File_Transfer;
				$uploader->addValidator('Extension', false, array('jpg','png','jpeg','bmp'));

				if( $uploader->isUploaded("icon") ){
					$files = $uploader->getFileInfo("icon");
					$ext = getFileExtension($files['icon']['name']);
					$unique_name = uniqid(time()."_");
					
					$uploader->addFilter('Rename',
						array(
							'target' => $folder.$unique_name.".".$ext,
                         	'overwrite' => true
							)
						);
					if($uploader->receive()){
						// $model->update(array('icon' => $folder_relative.$unique_name.".".$ext), "id=$id");
						$uploaded = $folder_relative.$unique_name.".".$ext;
					}else{
						$error = array_merge($error, $uploader->getMessages());
					}
				}
				
				//FORM DATA
				$formData = $form->getValues();
				unset($formData['id']);
				unset($formData['icon']);
				if ($uploaded) {
					$formData['icon'] = $uploaded;
				}
				if (!checkhttp($formData['homepage'])) {
					$formData['homepage'] = '';
				}
				
				if ($id) {
					$model->update($formData, "id=$id");
				} else {
					$zend_validate = new Zend_Validate_Db_NoRecordExists(array('table' => $model->_name, 'field' => 'title'));
					
					if ($zend_validate->isValid($formData['title'])) {
						$id = $model->insert($formData);
					}else{
						if ($uploaded && file_exists($folder.$unique_name.".".$ext)) {
							unlink($folder.$unique_name.".".$ext);
						}
						$error[] = "Conflicted title";
					}
				}
				
				if (count($error)) {
					echo implode("<br/>", $error);
				}else{
					$this->_redirect('/admin/resource/pages');
				}
            }
        }
		
		echo $form; 
	}

	public function templateAction()
	{
		$this->view->navibar = navibar(2,2);
		$this->view->pageSubtitle = "Source Template";
		$this->view->pageHeader = "Templates";
		
		Zend_Loader::loadClass("admin_models_SourceTemplate");
		$model = new Admin_Models_SourceTemplate;
		$this->view->tableData = $model->tableData();
	}
	
	public function addTemplateAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		
		$link_id = $this->_request->getParam('id');
		
		$this->view->navibar = navibar(2,2);
		$this->view->pageHeader = "Source Template";
		if ($link_id) {
			$this->view->pageSubtitle = "Edit";
		}else{
			$this->view->pageSubtitle = "Add New";
		}
		
		$link = new Model_SourceTemplate;
		if ($link_id && ($link_id != -1)) {
			$link_detail = $link->getById($link_id);
		}else{
			$link_detail = $_POST;
		}
		
		Zend_Loader::loadClass("admin_forms_AddTemplate");
		$form = new Admin_Forms_AddTemplate($link_detail);
		
		if($this->_request->isPost()){
            if($form->isValid($_POST)){
            	$data = $form->getValues();
				unset($data['id']);
				
				if ($link_id) {
					$link->update($data, "id=$link_id");
				} else {
					$zend_validate = new Zend_Validate_Db_NoRecordExists(array('table' => $link->_name, 'field' => 'title'));
					if ($zend_validate->isValid($data['title'])) {
						$link->insert($data);
					}
				}
				
                $this->_redirect('/admin/resource/template');
            }
        }
		
		echo $form; 
	}

	public function linksAction()
	{
		$this->view->navibar = navibar(2,3);
		$this->view->pageSubtitle = "Links Get News";
		$this->view->pageHeader = "Links";
		
		Zend_Loader::loadClass("admin_models_SourceLink");
		$model = new Admin_Models_SourceLink;
		$this->view->tableData = $model->tableData();
	}
	
	public function addlinkAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		
		$id = $this->_request->getParam('id');
		
		$this->view->navibar = navibar(2,3);
		$this->view->pageSubtitle = "Source Link";
		$this->view->pageHeader = "Add New";
		
		$link = new Model_SourceLink;
		$link_detail = $_POST;
		if ($id) {
			$link_detail = $link->getDetail($id);
			$this->view->pageHeader = "Edit";
		}
		
		Zend_Loader::loadClass("admin_forms_AddLink");
		$form = new Admin_Forms_AddLink($link_detail);
		
		if($this->_request->isPost()){
			$form = new Admin_Forms_AddLink($_POST);
            if($form->isValid($_POST)){
            	$data = $form->getValues();
				if (checkhttp($data['url'])) {
					unset($data['id']);
					unset($data['category_id']);
					
					if ($id) {
						$link->update($data, "id=$id");
					} else {
						$zend_validate = new Zend_Validate_Db_NoRecordExists(array('table' => $link->_name, 'field' => 'url'));
						if ($zend_validate->isValid($data['url'])) {
							$link->insert($data);
						}
					}
					
	                $this->_redirect('/admin/resource/links');
				} else {
					echo '<span class="col-sm-offset-2">* Invalid URL</span>';
				}
            }
        }
		
		echo $form; 
	}
}