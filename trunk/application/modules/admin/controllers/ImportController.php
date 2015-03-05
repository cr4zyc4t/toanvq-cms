<?php

class Admin_ImportController extends Zend_Controller_Action
{
	
	private function removeDoubleSpace($string){
    	mb_internal_encoding('UTF-8');		
    	$string = str_replace(chr("0xC2").chr("0xA0")," ",$string);
    	$string = str_replace("&nbsp;", " ", $string);
    	$string = str_replace("&nbsp", " ", $string);
    	$string = preg_replace("{[ \t]+}", ' ',$string);	
    	$string = trim($string);
    	$string = preg_replace("/<br[^>]*>([ \t])*/","<br/>",$string);
    	$string = preg_replace("/<br\/>(<br\/>)+/","<br/>",$string);
    	$string = preg_replace("/^<br\/>/","",$string);
//    	$string = str_replace("<br/><imgsunnet>", "<imgsunnet>", $string);
//    	$string = str_replace("</imgsunnet><br/>", "</imgsunnet>", $string);
    	return trim($string);
    }
	
	function checkhttp($url) {
		$pos = strpos ($url ,"http" );
	    if ($pos === false || $pos !== 0 ) {
	        return false;
	    }
	    return true;
	}
	
	public function indexAction()
	{
		echo "Getting news for 'Món ngon mỗi ngày'<br/>";
		// Zend_Loader::loadFile(APPLICATION_PATH."/..\library\simple_html_dom.php");
		require 'default_simple_html_dom.php';
		
		$link_get_content_model = new Model_LinkGetContent;
		$news_model = new Model_News;
		$news_error_model = new Model_NewsError;
		
		$link_get_contents = $link_get_content_model->getLink($source_id = 2);
		
		$count = 0;
		
		foreach ($link_get_contents as $link_get_content) {
			$client = new Zend_Http_Client($link_get_content['url']);
				$config = array(
			        // "useragent"		=> "Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_0 like Mac OS X; en-us) AppleWebKit/528.18 (KHTML, like Gecko) Version/4.0 Mobile/7A341 Safari/528.16", 
			        "useragent"		=> "Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36", 
			        "timeout"		=> 10, 
			        "maxredirects"	=> 1
				);
			$client->setConfig($config);
			
			try{
				$body = $client->request("GET")->getBody();
				$html_text = @mb_convert_encoding($body, 'utf-8', mb_detect_encoding($body));
				$html_text = @mb_convert_encoding($html_text, 'html-entities', 'utf-8');
				$html_text = @mb_convert_encoding($html_text, 'utf-8','html-entities');
				$html = str_get_html($html_text);
				
				$nodes = $html->find("article.recipe-result");
				foreach ($nodes as $node) {
					$data = array();
					$data['source_id'] = $link_get_content['source_id'];
					$data['category_id'] = $link_get_content['category_id'];
					
					//GET TITLE
					$title_node = $node->getElementByTagName('a');
					$title = $title_node->text();
					$data['title'] = $this->removeDoubleSpace($title);
					
					// //GET LINK
					$data['url'] = $title_node->getAttribute('href');
					if (!$this->checkhttp($data['url'])){
						$data['url'] = $link_get_content['home_page'] . $data['url'];
					}
// 					
					// //GET COVER
					$data['icon'] = $title_node->getElementByTagName("figure.image > img")->getAttribute("src");
// 					
					// //GET NODE CONTENT START
					$validator = new Zend_Validate_Db_NoRecordExists(array("table"	=> "news",
																			'field' => 'url'
					));
					$validator_error_link = new Zend_Validate_Db_NoRecordExists(array("table"	=> "news_error",
																						'field' => 'url'
					));
					if ($validator_error_link->isValid($data['url'])){
						if ($data['url'] && $data['title'] && $validator->isValid($data['url'])){
							$news_detail_client = new Zend_Http_Client($data['url']);
							$news_detail_client->setConfig(array(
						        // "useragent"		=> "Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_0 like Mac OS X; en-us) AppleWebKit/528.18 (KHTML, like Gecko) Version/4.0 Mobile/7A341 Safari/528.16", 
						        "useragent"		=> "Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36", 
						        "timeout"		=> 10, 
						        "maxredirects" => 2
							));
							
							try {
								$news_body = $news_detail_client->request("GET")->getBody();
								$news_text = @mb_convert_encoding($news_body, 'utf-8', mb_detect_encoding($news_body));
								$news_text = @mb_convert_encoding($news_text, 'html-entities', 'utf-8');
								$news_text = @mb_convert_encoding($news_text, 'utf-8','html-entities');
			
								$news = str_get_html($news_text);
								
								$news_node = $news->getElementByTagName("div.c25l");

								//TITLE FROM CONTENT
								$title_node = $news_node->getElementByTagName("div.recipe-content-header h1");
								if ($title_node){
									$data['title'] = preg_replace("/<[^>]*>/","",$this->removeDoubleSpace($title_node->text()));
								}
								
								//DESCRIPTION
								$data['description'] = "";
								//TIME
								$data['time'] = date("Y-m-d H:i:s");
								
								//CONTENT
								$content_node = $news_node->find("div.subcl",1);
								$content_node->find(".c25r",0)->clear();
								if ($content_node){
									$data['content'] = $content_node->innertext();
									$data['content'] = $this->removeDoubleSpace($data['content']);
								}
								try {
									if ($data['content']){
										$news_model->insert($data);
										$count++;
									}else{
										$news_error_model->insert(array("url"	=> $data['url']));
									}
								} catch (Exception $e) {
									echo "3|Loi khi luu nội dung bài báo";
									die;
								}
							}catch(exception $e){
								echo "4|Loi khi load trang bao chi tiết";
								die;
							}
						}
					}
					echo "<pre>";
					print_r($data);
					echo "</pre>";
				}
				
			}catch(exception $e){
				echo "<br/>Error:<br/>".$e;
			}
		}
		echo "$count article imported";
	}
}