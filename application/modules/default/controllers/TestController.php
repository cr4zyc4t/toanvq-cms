<?php

class TestController extends Zend_Controller_Action
{
	public function init()
	{
		$option=array(
	        "layout" => "admin",
	        "layoutPath" => APPLICATION_PATH."/layouts/scripts/"
      	);
      	Zend_Layout::startMvc($option);
        $this->view->headTitle("QHOnline - Zend Layout");

		require_once "toanvq_helper.php";
	}
	
	public function ttsAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout()->disableLayout();
		
		$text = "Vũ Quốc Toàn";
		
		$query = array(
			"INPUT_TEXT" => $text,
			"INPUT_TYPE" => "TEXT",
			"OUTPUT_TYPE" => "AUDIO",
			"LOCALE" => "vi",
			"AUDIO" => "WAVE_FILE"
		);
		$url = "http://cafe24h.vn:59125/process";
		
		$client = new Zend_Http_Client($url);
		
		$client->setConfig(array(
	        "timeout"		=> 60, 
	        "maxredirects"	=> 1
		));
		$client->setParameterPost($query);
		
		try {
			$body = $client->request("POST")->getBody();
			$file_name = uniqid(FALSE) . ".wav";
			$date = Zend_Date::now()->toString("yyyy-MM-dd");
			if(!file_exists(PUBLIC_PATH . "/temp/" . $date)){
				echo mkdir(PUBLIC_PATH . "/temp/" . $date,0777);
			}
            $file_path = PUBLIC_PATH . "/temp/" . $date . "/" . $file_name;
            $file_path_display = "/temp/" . $date . "/" . $file_name;
			file_put_contents($file_path, $body);
			$this->view->file_path = $file_path_display;
		} catch (Exception $e) {
			arr_dump($e);
		}
		
	}
	
	public function importSourceAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$dbOptions = array(
			"host" => "localhost",
			"username" => "root",
			"password" => "",
			"dbname" => "news_getter",
			"charset" => "utf8"
		);
        $db = Zend_Db::factory("PDO_MYSQL", $dbOptions);
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
		
		
		$query = $db->select()->from("source");
		
		$model_page = new Model_SourcePage;
		
		foreach ($db->fetchAll($query) as $key => $value) {
			$zend_validate = new Zend_Validate_Db_NoRecordExists(array('table' => $model_page->_name, 'field' => 'title'));
			if ($zend_validate->isValid($value['title'])) {
				$name = uniqid(time()."_");
				$icon = getFile("http://content.amobi.vn".$value['icon'], "/icon/page/$name.jpg");
				if ($icon) {
					$new_record = array();
					$new_record['title'] = $value['title'];
					$new_record['icon'] = $icon;
					$new_record['homepage'] = $value['homepage'];
					
					$model_page->insert($new_record);
				}
				
			}
			
		};
	}

	public function importTemplateAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$dbOptions = array(
			"host" => "localhost",
			"username" => "root",
			"password" => "",
			"dbname" => "news_getter",
			"charset" => "utf8"
		);
        $db = Zend_Db::factory("PDO_MYSQL", $dbOptions);
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
		
		$model_page = new Model_SourcePage;
		$model_template = new Model_SourceTemplate;
		
		foreach ($model_page->fetchAll()->toArray() as $key => $value) {
			$get_old_page = $db->select()->from("source")->where("title=?", $value['title']);
			$old_page = $db->fetchRow($get_old_page);
			
			$get_old_link = $db->select()->from("link_get_content_legacy")->where("source_id=?", $old_page['id']);
			$old_link = $db->fetchRow($get_old_link);
			if($old_link){
				$old_link['title'] = $old_link['name'];
				unset($old_link['name']);
				unset($old_link['home_page']);
				$old_link['source_id'] = $value['id'];
				unset($old_link['url']);
				$old_link['title_from_list'] = 1;
				if ($old_link['title_from_content']) {
					$old_link['title_from_list'] = 0; 
				}
				unset($old_link['title_from_content']);
				unset($old_link['icon_position']);
				unset($old_link['create_time']);
				unset($old_link['processed']);
				unset($old_link['tag_from_desktop_version']);
				unset($old_link['sub_category_id']);
				unset($old_link['partner_category_id']);
				unset($old_link['icon_xpath']);
				
				// $model_template->insert($old_link);
			}
		};
	}
}
		