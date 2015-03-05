<?php

class IndexController extends Zend_Controller_Action
{
	public function init()
	{
		// $option=array(
	        // "layout" => "cafe24h",
	        // "layoutPath" => APPLICATION_PATH."/layouts/scripts/"
      	// );
      	// Zend_Layout::startMvc($option);
        // $this->view->headTitle("QHOnline - Zend Layout");
// 		
		// $model_category = new Model_Category;
		// $categories = $model_category->getListCategory(1,1000);
		// $categories = array_merge(
			// array(
				// array(
					// "id" => -1,
					// "title" => "Nổi bật",
					// "url" => "/noi-bat.html"
				// ),
				// array(
					// "id" => -2,
					// "title" => "Tin mới",
					// "url" => "/tin-moi.html"
				// )
			// ), $categories
		// );
		// $this->view->categories = $categories;
// 		
		require_once "toanvq_helper.php";
	}
	
	public function indexAction()
	{
		$this->_helper->viewRenderer->setNoRender();
	}
	
	public function readnewsAction()
	{
		$news_id = $this->_request->getParam('news_id');
		$model_content = new Model_Contents;
		$news = $model_content->getNewsDetail($news_id);
		if (count($news) > 1) {
			$this->view->selected_ctg_id = $news['category_id'];
			$this->view->news = $news;
		}
	}
	
	public function categoryAction()
	{
		$category_id = $this->_request->getParam('category_id');
		$this->view->selected_ctg_id = $category_id;
		
		$model_content = new Model_Contents;
		
		$this->view->newsList = $model_content->getByCategory($category_id);
		
		$this->renderScript("index/index.phtml");
	}
	
	public function tagAction()
	{
		$tag_name = urldecode($this->_request->getParam('tag_name'));
		
		$this->view->selected_ctg_id = -3;
		$this->view->categories = array(
			array(
				"id" => -3,
				"title" => $tag_name,
				"url" => Zend_Controller_Front::getInstance()->getRequest()->getRequestUri()
			),
		);
		
		$model_content = new Model_Contents;
		
		$this->view->newsList = $model_content->getByTag($tag_name);
		
		$this->renderScript("index/index.phtml");
	}
	
	public function loadMoreAction(){
    	$this->_helper->layout()->disableLayout();
		// $this->_helper->viewRenderer->setNoRender();
		
		$category_id = $this->_request->getParam('category_id');
		$page = $this->_request->getParam('page');
		
		$params = array(
			"category_id" => $category_id,
			"page" => $page
		);
		$this->view->params = $params;
		$model_content = new Model_Contents;
		
		if ($category_id == -1) {
			$this->view->list_news = $model_content->getHighLight(9, 9*($page-1));
		} elseif ($category_id == -2) {
			$this->view->list_news = $model_content->getNewest(9, 9*($page-1));
		} else {
			$this->view->list_news = $model_content->getByCategory($category_id,9, 9*($page-1));
		}
		
		
    	// Zend_Loader::loadClass("NewsModel");
    	// $newsModel=new NewsModel();
    	// $params=$this->_arrParam;
    	// if(!@$params['category_name']) $params['category_name']="noi-bat";
    	// $this->view->params=$params;
    	// $list_news=$newsModel->getList($params,$this->_partner);
//     	
    	// //Luu session list bai
    	// $params['category_name'] = preg_replace("/(-)/", '_', $params['category_name']);
    	// $list=new Zend_Session_Namespace("list_news");
    	// $str=$params['category_name']."_page_".$params['page'];
    	// $list->$str=$list_news;
//     	
    	// $this->view->list_news=$list_news;
    	// //admin
    	// $admin=new Zend_Session_Namespace("admin");
    	// if($admin->admin) $this->view->admin=1;
    	// arr_dump($this->_request->getParams());
    	
    	/* //ads amobi
    	$client = new Zend_Http_Client(LINK_CPC_AMOBI."/api/get-embed-banner-for-wap");
    	$_SERVER['SERVER_ADDR']="14.165.13.154";
    	$client->setParameterPost(array(
    			'ip'=>$_SERVER['SERVER_ADDR'],
    			'ua'=>$_SERVER['HTTP_USER_AGENT'],
    	));
    	$response=$client->request('POST');
    	$response=$response->getBody();
    	$cpc=json_decode($response);
    	$this->view->cpc=$cpc; */
    }
}