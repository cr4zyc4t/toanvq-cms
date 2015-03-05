<?php

class Admin_ManageController extends Zend_Controller_Action
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
	
	public function deleteitemsAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout()->disableLayout();
		
		$reply = array();
		$reply['success'] = false;
		$reply['desc'] = "Delete failed";

		$id = $this->_request->getParam('id');

		$ids = $this->_request->getParam('ids');
		$type = $this->_request->getParam('type');
		if (!is_array($ids)) {
			$ids = array();
			if ($id) {
				$ids[] = $id;
			}
		}
		
		$reply['count'] = 0;

		$model = null;

		switch ($type) {
			case 'page':
				$model = new Model_SourcePage;
				break;
			case 'template':
				$model = new Model_SourceTemplate;
				break;
			case 'link':
				$model = new Model_SourceLink;
				break;
			default:
				
				break;
		}
		if ($model) {
			foreach ($ids as $item_id) {
				$reply['count'] += $model->delete("id=$item_id");
			}
			if ($reply['count']) {
				$reply['success'] = TRUE;
				$reply['desc'] = "Delete Success";
			}
		}
		
		echo json_encode($reply);
	}
	
	public function subcategoryselectAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout()->disableLayout();
		
		$category_id = $this->_request->getParam('ctg');
		$subcategory = new Model_Subcategory;
		$options = $subcategory->selectOptions($category_id);
		
		$html = "";
		foreach ($options as $key => $value) {
			$html.= "<option value=\"$key\" label=\"$value\">$value</option>";
		}
		echo $html;
	}
	
	public function sourceAction()
	{
		$type = $this->_request->getParam('type');
		if ($type) {
			$bigtype = new Model_Bigtype();
			$bigtype = $bigtype->getById($type);
			
			$this->view->navibar = navibar(3,$type);
			$this->view->pageSubtitle = "List category";
			$this->view->pageHeader = $bigtype['title'];
			
			$category = new Model_Category;
			$this->view->tableData = $category->tableData($type);
			$this->view->type = $type;
		}
	}
	
	public function addcategoryAction()
	{
		$type = $this->_request->getParam('type');
		$category_id = $this->_request->getParam('category_id');
		if ($type) {
			if ($category_id) {
				$category = new Model_Category;
				$formData = $category->detail($category_id);
				if ($formData) {
					$this->view->formData = $formData;
				}
			}
			$this->view->navibar = navibar(3,$type);
			$this->view->pageHeader = "Add Category";
			$this->view->type = $type;
			
			$bigtype = new Model_Bigtype();
			$bigtype = $bigtype->getById($type);
			// $this->_helper->viewRenderer->setNoRender();
			$this->view->pageSubtitle = $bigtype['title'];
			
		}
	}
	
	public function doaddcategoryAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		
		$type = $this->_request->getParam('type');
		$category_id = $this->_request->getParam('id');
		if ($type) {
			$data = array();
			$data['title'] = $this->_request->getParam('title');
			$data['type_id'] = $this->_request->getParam('type');
			$data['sort'] = $this->_request->getParam('sort');
			
			//UPLOAD AVATAR
			$path = "assets/upload/";
			foreach ($_FILES as $input_name => $file) {
				if ($file['name']) {
					try{
						$icon_path = "";
						$valid_formats = array("jpg", "png", "bmp","jpeg");
						$name = $file['name'];
						$size = $file['size'];

						$name_arr = explode(".", $name);
						$ext = array_pop($name_arr);
						if (in_array($ext, $valid_formats)) {
							$actual_image_name = uniqid(time()).".". $ext;
							$tmp = $file['tmp_name'];
							if (move_uploaded_file($tmp, $path . $actual_image_name)) {
								$icon_path = "/".$path.$actual_image_name;
								switch ($input_name) {
									case 'img_icon':
										$data['icon'] = $icon_path;
										
										
										break;
									case 'img_avatar1':
										$data['avatar1'] = $icon_path;
										break;
									case 'img_avatar2':
										$data['avatar2'] = $icon_path;
										break;
									case 'img_avatar3':
										$data['avatar3'] = $icon_path;
										break;
									default:
										
										break;
								}
								
							}
						}
	                }catch (Exception $e){
	                	echo $e;
	                }
				}
			}
			
			$category = new Model_Category;
			if ($category_id) {
				$category->update($data, "id=$category_id");
			}else{
				$category->insert($data);
			}
			$this->_redirect("/admin/manage/source?type=$type");
		}
	}
	
	public function deletecategoryAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout()->disableLayout();
		
		$reply = array();
		$reply['code'] = 907;
		$reply['desc'] = "Unknown Error";
		
		$category_id = $this->_request->getParam('category_id');
		if ($category_id) {
			$category = new Model_Category;
			$category->delete("id=$category_id");
			
			$reply['code'] = 0;
			$reply['desc'] = "Success";
		}
		echo json_encode($reply);
	}
	
	
	
	//SUB CATEGORY
	public function subcategoryAction()
	{
		$category_id = $this->_request->getParam('category_id');
		if ($category_id) {
			$category = new Model_Category;
			$category = $category->detail($category_id);
			
			$bigtype = new Model_Bigtype();
			$bigtype = $bigtype->getById($category['type_id']);
			
			$this->view->navibar = navibar(3,$category['type_id']);
			$this->view->pageSubtitle = $category['title'];
			$this->view->pageHeader = $bigtype['title'];
			
			$sub_category = new Model_Subcategory;
			$this->view->tableData = $sub_category->tableData($category_id);
			$this->view->category_id = $category_id;
		}
	}
	
	public function addsubcategoryAction()
	{
		$category_id = $this->_request->getParam('category_id');
		$subcategory_id = $this->_request->getParam('id');
		if ($category_id) {
			$category = new Model_Category;
			$category = $category->detail($category_id);
			
			$type = $category['type_id'];
			
			$bigtype = new Model_Bigtype();
			$bigtype = $bigtype->getById($type);
			
			$this->view->category_id = $category_id;
			$this->view->type_id = $category['type_id'];
			if($subcategory_id){
				$subcategory = new Model_Subcategory;
				$subcategory = $subcategory->detail($subcategory_id);
				
				$this->view->formData = $subcategory;
			}
			
			$this->view->navibar = navibar(3,$category['type_id']);
			$this->view->pageHeader = $category['title'];
			$this->view->pageSubtitle = "Add sub category";
		}
	}
	
	public function doaddsubcategoryAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		
		$category_id = $this->_request->getParam('category_id');
		$subcategory_id = $this->_request->getParam('id');
		if ($category_id) {
			$data = array();
			$data['title'] = $this->_request->getParam('title');
			$data['category_id'] = $this->_request->getParam('category_id');
			$data['type_id'] = $this->_request->getParam('type_id');
			$data['sort'] = $this->_request->getParam('sort');
			$data['target_date'] = $this->_request->getParam('target_date');
			if(!$data['target_date']){
				$data['target_date'] = date("Y-m-d");
			}
			
			//UPLOAD AVATAR
			$path = "assets/upload/";
			foreach ($_FILES as $input_name => $file) {
				if ($file['name']) {
					try{
						$icon_path = "";
						$valid_formats = array("jpg", "png", "bmp","jpeg");
						$name = $file['name'];
						$size = $file['size'];

						$name_arr = explode(".", $name);
						$ext = array_pop($name_arr);
						if (in_array($ext, $valid_formats)) {
							$actual_image_name = uniqid(time()).".". $ext;
							$tmp = $file['tmp_name'];
							if (move_uploaded_file($tmp, $path . $actual_image_name)) {
								$icon_path = "/".$path.$actual_image_name;
								switch ($input_name) {
									case 'img_icon':
										$data['icon'] = $icon_path;
										break;
									case 'img_avatar1':
										$data['avatar1'] = $icon_path;
										break;
									case 'img_avatar2':
										$data['avatar2'] = $icon_path;
										break;
									case 'img_avatar3':
										$data['avatar3'] = $icon_path;
										break;
									default:
										
										break;
								}
								
							}
						}
	                }catch (Exception $e){
	                	echo $e;
	                }
				}
			}
			
			$subcategory = new Model_Subcategory;
			if ($subcategory_id) {
				$subcategory->update($data, "id=$subcategory_id");
			}else{
				$subcategory->insert($data);
			}
			$this->_redirect("/admin/manage/subcategory?category_id=$category_id");
			// redirect("/admin/manage/subcategory?category_id=$category_id");
		}
	}
	
	public function deletesubcategoryAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout()->disableLayout();
		
		$reply = array();
		$reply['code'] = 907;
		$reply['desc'] = "Unknown Error";
		
		$id = $this->_request->getParam('id');
		if ($id) {
			$model = new Model_Subcategory;
			$model->delete("id=$id");
			
			$reply['code'] = 0;
			$reply['desc'] = "Success";
		}
		echo json_encode($reply);
	}
	
	//CONTENT
	public function listcontentAction()
	{
		$subcategory_id = $this->_request->getParam('subcategory_id');
		if ($subcategory_id) {
			$subcategory = new Model_Subcategory;
			$subcategory = $subcategory->detail($subcategory_id);
			
			$category = new Model_Category;
			$category = $category->detail($subcategory['category_id']);
			
			$bigtype = new Model_Bigtype();
			$bigtype = $bigtype->getById($category['type_id']);
			
			$this->view->navibar = navibar(3,$category['type_id']);
			$this->view->pageHeader = "<a href=\"/admin/manage/subcategory?category_id=$category[id]\">$category[title]</a>";
			$this->view->pageSubtitle = "<a href=\"/admin/manage/listcontent?subcategory_id=$subcategory[id]\">$subcategory[title]</a>";
			
			$contents = new Model_News;
			$this->view->tableData = $contents->tableData($subcategory_id);
			$this->view->subcategory_id = $subcategory_id;
		}
	}
	
	public function prefixformAction()
	{
		$this->_helper->layout()->disableLayout();
	}
	
	public function fixorderAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout()->disableLayout();
		
		$reply = array();
		$reply['code'] = 907;
		$reply['desc'] = "Unknown Error";
		
		$id = $this->_request->getParam('id');
		
		$ids = $this->_request->getParam('ids');
		
		if (!is_array($ids)) {
			$ids = array();
			if ($id) {
				$ids[] = $id;
			}
		}
		$reply['count'] = 0;
		foreach ($ids as $item_id) {
			$model = new Model_Contents;
			$content = $model->detail($item_id);
			if (count($content) > 0) {
				if ($chapter = extractNumber($content['title'])) {
					$model->update(array("sort" => $chapter), "id=$content[id]");
					$reply['code'] = 0;
					$reply['sort'] = $chapter;
					$reply['desc'] = "Success";
				}
			}
		}
		echo json_encode($reply);
	}
	
	public function fixnameAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout()->disableLayout();
		
		$reply = array();
		$reply['code'] = 907;
		$reply['desc'] = "Unknown Error";
		
		$id = $this->_request->getParam('id');
		
		$ids = $this->_request->getParam('ids');
		$prefix = $this->_request->getParam('prefix');
		$option = $this->_request->getParam('option');
		
		if (!is_array($ids)) {
			$ids = array();
			if ($id) {
				$ids[] = $id;
			}
		}
		$reply['count'] = 0;
		foreach ($ids as $item_id) {
			$model = new Model_Contents;
			$content = $model->detail($item_id);
			if (count($content) > 0) {
				$newname = '';
				if ($option) {
					if (stripos(trim($content['title']), trim($prefix)) == 0) {
						// $newname = substr($content['title'], strlen(trim($prefix)));
						$newname = str_ireplace($prefix, '', $content['title']);
					}
				} else {
					$newname = $prefix.$content['title'];
				}
				if ($newname != '') {
					$model->update(array('title' => $newname), "id=$content[id]");
					$reply['count']++;
				}
			}
			
			$reply['code'] = 0;
			$reply['desc'] = "Success";
		}
		echo json_encode($reply);
	}
	
	public function addcontentAction()
	{
		$subcategory_id = $this->_request->getParam('subcategory_id');
		$content_id = $this->_request->getParam('id');
		if ($subcategory_id) {
			$subcategory = new Model_Subcategory;
			$subcategory = $subcategory->detail($subcategory_id);
				
			$category = new Model_Category;
			$category = $category->detail($subcategory['category_id']);
			
			$type = $category['type_id'];
			
			
			$this->view->subcategory_id = $subcategory_id;
			if($content_id){
				$content = new Model_News;
				$content = $content->getNewsDetail($content_id, FALSE);
				
				$this->view->formData = $content;
			}
			// if ($category['type_id'] == 2) { //VIDEO
				// $this->view->noCKeditor = TRUE;
			// }
			
			$tag_model = new Model_Tag;
			$tag_arr = array();
			foreach ($tag_model->getAll() as $key => $value) {
				$tag_arr[] = $value['title'];
			}
			$this->view->contenttags = $tag_arr;
			
			$this->view->navibar = navibar(3,$category['type_id']);
			$this->view->pageHeader = "<a href=\"/admin/manage/subcategory?category_id=$category[id]\">$category[title]</a>";
			$this->view->pageSubtitle = "<a href=\"/admin/manage/listcontent?subcategory_id=$subcategory[id]\">$subcategory[title]</a>";
		}else{
			$this->_redirect("/admin");
		}
	}
	
	public function doaddcontentAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$subcategory_id = $this->_request->getParam('subcategory_id');
		$content_id = $this->_request->getParam('id');
		if ($subcategory_id) {
			$data = array();
			
			$db_col = array('url','title', 'description','author','subcategory_id','sort','time','content','view','like','rank_hot','rank_interest','rank_best','next_content','prev_content','emotion','sexual','human_career', 'created_time');
			foreach ($db_col as $col) {
				$data[$col] = $this->_request->getParam($col);
			} 
			$data['url'] = str_replace("\r\n", '', strip_tags($this->_request->getParam('url')));
			//VALIDATE DATA
			// $subcategory = new Model_Subcategory;
			// $subcategory = $subcategory->detail($subcategory_id);
			// if ($subcategory['type_id'] == 2) {
				// // exit;
				// $data['url'] = $data['content'];
			// }
			if(!$data['url']){
				$data['url'] = $data['content'];
			}
			
			$tag_model = new Model_Tag;
			
			$tags = $this->_request->getParam('tags');
			if (is_array($tags)) {
				$data['tag'] = implode(",", $tags);
				foreach ($tags as $tag) {
					$check = new Zend_Validate_Db_NoRecordExists(array("table"=> $tag_model->_name,'field'=> 'title'));
					if ($check->isValid($tag)) {
						$tag_model->insert(array('title' => $tag));
					}
				}
			}
			
			$sys_tags = $this->_request->getParam('sys_tags');
			if (is_array($sys_tags)) {
				$data['sys_tag'] = implode(",", $sys_tags);
				
				foreach ($sys_tags as $tag) {
					$check = new Zend_Validate_Db_NoRecordExists(array("table"=> $tag_model->_name,'field'=> 'title'));
					if ($check->isValid($tag)) {
						$tag_model->insert(array('title' => $tag));
					}
				}
			}
			
			//UPLOAD AVATAR
			$path = "assets/upload/";
			foreach ($_FILES as $input_name => $file) {
				if ($file['name']) {
					try{
						$icon_path = "";
						$valid_formats = array("jpg", "png", "bmp","jpeg","webp");
						$name = $file['name'];
						$size = $file['size'];

						$name_arr = explode(".", $name);
						$ext = array_pop($name_arr);
						if (in_array($ext, $valid_formats)) {
							$actual_image_name = uniqid(time()).".". $ext;
							$tmp = $file['tmp_name'];
							if (move_uploaded_file($tmp, $path . $actual_image_name)) {
								$icon_path = "/".$path.$actual_image_name;
								switch ($input_name) {
									case 'img_icon':
										$data['icon'] = $icon_path;
										break;
									case 'img_avatar1':
										$data['avatar1'] = $icon_path;
										break;
									case 'img_avatar2':
										$data['avatar2'] = $icon_path;
										break;
									case 'img_avatar3':
										$data['avatar3'] = $icon_path;
										break;
									default:
										break;
								}
								
							}
						}
	                }catch (Exception $e){
	                	echo $e;
	                }
				}
			}
			
			$content = new Model_News;
            $log_model = new Model_Log;
			if ($content_id) {
				$content->update($data, "id=$content_id");
			}else{
                $auth = Zend_Auth::getInstance();
                $auth_info = $auth->getIdentity();
                $log = array();
                $log['user_id'] = $auth_info->id;
                $log['type'] = 0;
                $log['count'] = 1;

				$log['content_id'] = $content->insert($data);

                $log_model->insert($log);
			}
			$this->_redirect("/admin/manage/listcontent?subcategory_id=$subcategory_id");
		}
		
		// arr_dump($_POST);
	}
	
	public function availabletagsAction()
	{
		$tag_model = new Model_Tag;
		$tag_arr = array();
		foreach ($tag_model->getAll() as $key => $value) {
			$tag_arr[] = $value;
		}
		echo json_encode($tag_arr);
	}
	
	public function importyoutubeAction()
	{
		$subcategory_id = $this->_request->getParam('subcategory_id');
		$content_id = $this->_request->getParam('id');
		if ($subcategory_id) {
			$subcategory = new Model_Subcategory;
			$subcategory = $subcategory->detail($subcategory_id);
				
			$category = new Model_Category;
			$category = $category->detail($subcategory['category_id']);
			
			$type = $category['type_id'];
			
			// $bigtype = new Model_Bigtype();
			// $bigtype = $bigtype->getById($type);
			
			$this->view->subcategory_id = $subcategory_id;
			// $this->view->id = $subcategory_id;
			if($content_id){
				$content = new Model_News;
				$content = $content->getNewsDetail($content_id);
				
				$this->view->formData = $content;
			}
			if ($category['type_id'] == 2) { //VIDEO
				$this->view->noCKeditor = TRUE;
			}
			
			$this->view->navibar = navibar(3,$category['type_id']);
			$this->view->pageHeader = $category['title'];
			$this->view->pageSubtitle = $subcategory['title'];
		}
	}
	
	public function moveformAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout()->disableLayout();
		
		Zend_Loader::loadClass("admin_forms_MoveContent");
		echo $form = new Admin_Forms_MoveContent();
	}
	
	public function movecontentAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout()->disableLayout();
		
		$reply = array();
		$reply['code'] = 907;
		$reply['desc'] = "Unknown Error";
		
		$subcategory_id = $this->_request->getParam('subcategory_id');
		$ids = $this->_request->getParam('ids');
		
		foreach ($ids as $item_id) {
			$model = new Model_Contents;
			$model->update(array("subcategory_id" => $subcategory_id), "id=$item_id");
			
			$reply['redirect'] = "/admin/manage/listcontent?subcategory_id=$subcategory_id";
			$reply['code'] = 0;
			$reply['desc'] = "Success";
		}
		echo json_encode($reply);
	}
	
	public function reordercontentAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout()->disableLayout();
		
		$order_data = json_decode($this->_request->getParam('order_data'));
		
		$content_model = new Model_Contents;
		
		foreach ($order_data as $order) {
			// arr_dump($order);
			$content_model->update(array("sort" => $order->order), "id=$order->id");
		}
	}
	
	public function importphotoformAction()
	{
		$this->_helper->layout()->disableLayout();
		$subcategory_id = $this->_request->getParam('subcategory_id');

		if ($subcategory_id) {
			$this->view->subcategory_id = $subcategory_id;
		}else{
			$this->_helper->viewRenderer->setNoRender();
		}
	}
	
	public function importphotoAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$reply = array();
		$reply['code'] = 907;
		$reply['desc'] = "Unknown Error";
		
		//UPLOAD AVATAR
		$path = "assets/upload/";
		$uploaded = "";
		$uploaded_path = "";
		
		$count = 0;
		$image_formats = array("jpg", "png", "bmp","jpeg");
		$audio_formats = array("mp3", "wav", "flac","aac");
		$video_formats = array("mp4", "3gp", "avi", "wmv");
		if (isset($_FILES['pictures'])) {
			$file = $_FILES['pictures'];
			foreach ($file['name'] as $key => $name) {
				try{
					$size = $file['size'][$key];

					$name_arr = explode(".", $name);
					$ext = array_pop($name_arr);
					if (in_array($ext, $image_formats)) {
						$actual_image_name = uniqid(time())."_$key.". $ext;
						$tmp = $file['tmp_name'][$key];
						if (move_uploaded_file($tmp, $path . $actual_image_name)) {
							// $uploaded.= "/".$path.$actual_image_name.";<br/>";
							$uploaded.= "<img height=\"200px\" src=\"/".$path.$actual_image_name."\" /><br/>";
							$uploaded_path.= "http://content.amobi.vn/".$path.$actual_image_name.";";
							$count++;
							$reply['code'] = 0;
							$reply['desc'] = "Uploaded $count files";
						}
					} elseif (in_array($ext, $audio_formats) || in_array($ext, $video_formats)) {
						$actual_image_name = uniqid(time())."_$key.". $ext;
						$tmp = $file['tmp_name'][$key];
						if (move_uploaded_file($tmp, $path . $actual_image_name)) {
							// $uploaded.= "/".$path.$actual_image_name.";<br/>";
							$uploaded.= "http://content.amobi.vn/".$path.$actual_image_name.";<br/>";
							$uploaded_path.= "http://content.amobi.vn/".$path.$actual_image_name.";<br/>";
							$count++;
							$reply['code'] = 0;
							$reply['desc'] = "Uploaded $count files";
						}
					}else{
						$reply['code'] = 1;
						$reply['desc'] = "Invalid file format";
					}					
                }catch (Exception $e){
                	$reply['error'] = var_dump($e);
                }
			}
			$reply['uploaded'] = $uploaded;
			$reply['uploaded_path'] = $uploaded_path;
		}
		
		echo json_encode($reply);
	}
}
