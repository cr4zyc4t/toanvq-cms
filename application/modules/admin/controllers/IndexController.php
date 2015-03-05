<?php

class Admin_IndexController extends Zend_Controller_Action
{
	public function init(){
		$option=array(
	        "layout" => "admin",
	        "layoutPath" => APPLICATION_PATH."/layouts/scripts/"
      	);
      	Zend_Layout::startMvc($option);
        $this->view->pageTitle = "Amobi - toanvq";
        $this->view->pageHeader = "QplayVN";
        include_once 'toanvq_helper.php';
	}
	public function indexAction()
	{
		$this->view->navibar = navibar(2,4);
		$this->view->pageSubtitle = "Links Get Content";
		$this->view->pageHeader = "QplayVN";
		
		$category = new Model_SourceLink;
		$this->view->tableData = $category->tableData();
	}
	
	public function addlinkAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		
		$link_id = $this->_request->getParam('id');
		
		$this->view->navibar = navibar(2,4);
		$this->view->pageHeader = "Links get content - QplayVN";
		$this->view->pageSubtitle = "Add New";
		
		$link = new Model_LinkGetContentLegacy;
		if ($link_id && ($link_id != -1)) {
			$link_detail = $link->detail($link_id);
			
			$this->view->pageSubtitle = "Edit link";
		}else{
			$link_detail = $_POST;
		}
		
		Zend_Loader::loadClass("qplayvn_forms_AddLink");
		$form = new Qplayvn_Forms_AddLink($link_detail);
		
		if($this->_request->isPost()){
            if($form->isValid($_POST)){
                try{
                	// arr_dump($_POST);
                	$data = $form->getValues();
					// arr_dump($data);
					unset($data['id']);
					unset($data['category_id']);
					
					if ($link_id) {
						$link->update($data, "id=$link_id");
					} else {
						$zend_validate = new Zend_Validate_Db_NoRecordExists(array('table' => $link->_name, 'field' => 'url'));
						if ($zend_validate->isValid($data['url'])) {
							$link->insert($data);
						}
					}
					
                    $this->_redirect("/qplayvn/index/addlink?id=$link_id");
                }catch (Exception $e){
                	echo $e;
                }
            }
        }
		
		echo $form; 
	}

	// public function importAction()
	// {
		// $model_category = new Model_Category();
		// $model_subcategory = new Model_Subcategory();
// 
		// $db = Zend_Db_Table::getDefaultAdapter();
// 
		// $query = $db->select()->from("old_category");
// 
		// $old_categories = array();
		// foreach($db->fetchAll($query) as $record){
			// $category = $model_category->getByTitle($record['name']);
			// if($category){
				// $new_id = $category['id'];
			// }else{
				// $new_record = array();
				// $new_record['title'] = $record['name'];
				// $new_record['type_id'] = 1;
				// $new_record['icon'] = getFile("http://cafe24h.vn".$record['background']);
				// if ($new_record['icon']) {
					// $new_record['avatar1'] = imgResize($new_record['icon'], -1, 128);
					// $new_record['avatar2'] = imgResize($new_record['icon'], -1, 250);
					// $new_record['avatar3'] = imgResize($new_record['icon'], -1, 300);
				// }
				// $new_record['sort'] = $record['sort'];
// 
				// $new_id = $model_category->insert($new_record);
			// }
// 
			// $record['new_id'] = $new_id;
			// $old_categories[$record['id']] = $record;
		// }
		// arr_dump($old_categories);
// 
		// $query2 = $db->select()->from("old_sub_category");
		// foreach($db->fetchAll($query2) as $record){
			// $subcategory = $model_subcategory->getByTitle($record['name']);
// 
			// if($subcategory){
				// arr_dump($subcategory);
			// }else{
				// $new_record = array();
				// $new_record['title'] = $record['name'];
				// $new_record['target_date'] = date('Y-m-d');
				// $new_record['type_id'] = 1;
				// $new_record['icon'] = getFile("http://cafe24h.vn".$record['background']);
				// if ($new_record['icon']) {
					// $new_record['avatar1'] = imgResize($new_record['icon'], -1, 128);
					// $new_record['avatar2'] = imgResize($new_record['icon'], -1, 250);
					// $new_record['avatar3'] = imgResize($new_record['icon'], -1, 300);
				// }
				// $new_record['category_id'] = $old_categories[$record['category_id']]['new_id'];
				// arr_dump($new_record);
				// $model_subcategory->insert($new_record);
			// }
		// }
// 
	// }
// 
	// public function fixlinkAction(){
		// $model_linkgetold = new Model_LinkGetContentLegacy();
		// $model_subcategory = new Model_Subcategory();
// 
		// foreach($model_linkgetold->fetchAll() as $linkget ){
			// $db = Zend_Db_Table::getDefaultAdapter();
			// $query = $db->select()->from("old_sub_category")->where("id=?",$linkget['sub_category_id']);
			// $old_ctg = $db->fetchRow($query);
			// arr_dump($old_ctg);
			// $new_ctg = $model_subcategory->fetchRow("title='$old_ctg[name]'");
			// arr_dump($new_ctg);
			// $model_linkgetold->update(array("sub_category_id" => $new_ctg['id']),"id='$linkget[id]'");
// //			break;
		// }
	// }
// 
	// public function fixlinksourceAction(){
		// $model_linkgetold = new Model_LinkGetContentLegacy();
		// $model_source = new Model_NewsSource();
		// $db = Zend_Db_Table::getDefaultAdapter();
// 
		// $query = $db->select()->from("old_source");
// 
		// $old_sources = array();
		// foreach($db->fetchAll($query) as $record){
			// $new_source = $model_source->getByTitle($record['name']);
			// if($new_source){
				// $new_id = $new_source['id'];
			// }else{
				// $new_record = array();
				// $new_record['title'] = $record['name'];
				// $new_record['module_name'] = "tonghop";
				// $new_record['icon'] = getFile("http://cafe24h.vn".$record['square_icon']);
// 
				// $new_id = $model_source->insert($new_record);
			// }
			// $record['new_id'] = $new_id;
			// $old_sources[$record['id']] = $record;
		// }
// 
		// arr_dump($old_sources);
		// foreach($model_linkgetold->fetchAll() as $linkget ){
			// $model_linkgetold->update(array("source_id" => $old_sources[$linkget['source_id']]['new_id']),"id='$linkget[id]'");
// //			break;
		// }
	// }
}