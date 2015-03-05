<?php

function removeDoubleSpace($string){
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

function getFileExtension($filename)
{
	return array_pop(explode(".", $filename ));
}

function urlTitle($str) {
	$str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
	$str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
	$str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
	$str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
	$str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
	$str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
	$str = preg_replace("/(đ)/", 'd', $str);
	$str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
	$str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
	$str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
	$str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
	$str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
	$str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
	$str = preg_replace("/(Đ)/", 'D', $str);
	$str = preg_replace("/(\?)/", '', $str);
	$str = preg_replace("/(\")/", '', $str);
	$str = preg_replace("/(\/)/", '-', $str);
	$str = preg_replace("/(\%)/", '', $str);
	$str = str_replace(" ", "-", str_replace("&*#39;","",$str));
	return $str;
}

function dynamicTime($time)
{
	$timestamp = strtotime($time);
	if ($timestamp <= 86400) {
		return date("Y-m-d H:i:s", strtotime(date("Y-m-d")) + $timestamp);
	}elseif (($timestamp % 86400) == 0) {
		return date("Y-m-d", $timestamp);
	}else {
		return date("Y-m-d H:i:s", $timestamp);
	}
}

function timePass($time)
{
	$date = new Zend_Date($time,"yyyy-MM-dd HH:mm:ss");
	$date_now =new Zend_Date();
	$diff=$date_now->sub($date)->toValue();
	$days = floor($diff/60/60/24);
	$hours=floor(($diff-$days*24*60*60)/60/60);
	$minutes=floor(($diff-$days*24*60*60-$hours*60*60)/60);
	$seconds=floor(($diff-$days*24*60*60-$hours*60*60-$minutes*60));
	if($days) $title=$days." ngày trước";
	elseif($hours) $title=$hours. " giờ trước";
	elseif($minutes) $title=$minutes. " phút trước";
	elseif($seconds) $title=$seconds. " giây trước";
	
	return $title;
}

function extractNumber($string, $only_first = TRUE)
{
	if ($only_first) {
		preg_match('!\d+!', $string, $matches);
		if (count($matches)) {
			return $matches[0];
		}
	} else {
		preg_match_all('!\d+!', $string, $matches);
		return $matches;
	}
	
	return FALSE;
}

function imgResize($file,$width = -1, $height = -1, $output = '')
{
	Zend_Loader::loadClass("simpleImage");
	
	$image = new SimpleImage();
	
   	$image->load(".$file");
	if (($width != -1) && ($height != -1)) {
		$image->resize($width, $height);
	}elseif ($width = -1) {
		$image->resizeToHeight($height);
	}elseif ($height = -1) {
		$image->resizeToWidth($width);
	}
	
	if ($output == '') {
		$output = "/assets/download/".uniqid(time(), TRUE).".".array_pop(explode(".", $file));
	}
   	$image->save(".$output");
	return $output;
}

function getFile($url, $save_path = "/icon/")
{
	// $url = 'http://www.youtube.com/yt/brand/media/image/YouTube-icon-full_color.png';
	if ($save_path == "/assets/download/") {
		$save_path .= uniqid(time(), TRUE).".jpg";
	}
	
	$file_got = file_get_contents($url);
	if ($file_got) {
		if(file_put_contents(".$save_path", $file_got)){
			return $save_path;
		}
	}
	return FALSE;
}

function getHtmlNode($url, $agent_mode = "desktop")
{
	require_once 'default_simple_html_dom.php';
	
	$user_agents = array(
					"desktop" => "Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36",
					"mobile" => "Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_0 like Mac OS X; en-us) AppleWebKit/528.18 (KHTML, like Gecko) Version/4.0 Mobile/7A341 Safari/528.16",
					);
	$client = new Zend_Http_Client($url);
	$client->setConfig(array(
        "useragent"		=> $user_agents[$agent_mode], 
        "timeout"		=> 10, 
        "maxredirects"	=> 1
	));
	$body = $client->request("GET")->getBody();
	$html_text = @mb_convert_encoding($body, 'utf-8', mb_detect_encoding($body));
	$html_text = @mb_convert_encoding($html_text, 'html-entities', 'utf-8');
	$html_text = @mb_convert_encoding($html_text, 'utf-8','html-entities');
	return str_get_html($html_text);
}

function redirect($uri = '', $method = 'location', $http_response_code = 302)
{
	switch($method)
	{
		case 'refresh'	: header("Refresh:0;url=".$uri);
			break;
		default			: header("Location: ".$uri, TRUE, $http_response_code);
			break;
	}
	exit;
}

function navibar($main_selected = -1, $sub_selected = -1)
{
	$selected = array($main_selected, $sub_selected);
	$items = array(
						array(  'title' => 'Dashboard',
	                            'url'  => '/announcement',
	                            'icon'  => 'fa-dashboard',
	                            'mark'  => 1,
	                          ),
                      	array(  'title' => 'Resources',
	                            'url'  => '#',
	                            'icon'  => 'fa-table',
	                            'mark'  => 2,
	                            'items' => array(
                                    array("url" => "/admin/resource/pages", "title" => 'Resource Pages', 'mark' => 1),
                                    array("url" => "/admin/resource/template", "title" => 'Resource Template', 'mark' => 2),
                                    array("url" => "/admin/resource/links", "title" => 'Resource Links', 'mark' => 3),
                                )
	                          ),
                        array(  'title' => 'Tracking Report',
                            'url'  => '#',
                            'icon'  => 'fa-bar-chart-o',
                            'mark'  => 4,
                            'items' => array(
//                                array("url" => "/admin/report/daily", "title" => 'Daily', 'mark' => 1),
                                array("url" => "/admin/report/weekly", "title" => 'Weekly', 'mark' => 2),
                              	array("url" => "/admin/report/monthly", "title" => 'Monthly', 'mark' => 3),
                            )
                        ),
                    );
	
	$navbar = '<ul class="sidebar-menu">';
	foreach ($items as $nav) {
		$style = "";
		$sub_menu_indicator = "";
		$sub_menu = "";
		if (isset($nav['items'])) {
			$style.= "treeview";
			$sub_menu_indicator.= "<i class=\"fa fa-angle-left pull-right\"></i>";
			$sub_menu.= "<ul class=\"treeview-menu\">";
			foreach ($nav['items'] as $sub_item) {
				$sub_menu_style = "";
				if (($nav['mark'] == $selected[0]) && ($sub_item['mark'] == $selected[1])) {
					$sub_menu_style = "active";
				}
				$sub_menu.= "<li class=\"$sub_menu_style\"><a href=\"$sub_item[url]\"><i class=\"fa fa-angle-double-right\"></i> $sub_item[title]</a></li>";
			}
			$sub_menu.= "</ul>";
		}
	
		if ($nav['mark'] == $selected[0]) {
			$style.= " active ";
		}
	    $navbar.=
	    "<li class=\"$style\">
	        <a href=\"$nav[url]\">
	            <i class=\"fa $nav[icon]\"></i> <span>$nav[title]</span> $sub_menu_indicator
	        </a>
	        $sub_menu
	    </li>";
	}
	
	$navbar.= '</ul>';
	
	return $navbar;
}

function arr_dump($arr){
	echo "<pre>";
	print_r($arr);
	echo "</pre>";
}

function checkhttp($url) {
	$url_parts = parse_url($url);
	if (isset($url_parts['host'])) {
		return TRUE;
	}
    return FALSE;
}

function buildURL($absolute, $relative) {
    $p = parse_url($relative);
    if(@$p["host"])return $relative;
    
    extract(parse_url($absolute));
    
    $path = dirname($path); 

    if($relative{0} == '/') {
        $cparts = array_filter(explode("/", $relative));
    }
    else {
        $aparts = array_filter(explode("/", $path));
        $rparts = array_filter(explode("/", $relative));
        $cparts = array_merge($aparts, $rparts);
        foreach($cparts as $i => $part) {
            if($part == '.') {
                $cparts[$i] = null;
            }
            if($part == '..') {
                $cparts[$i - 1] = null;
                $cparts[$i] = null;
            }
        }
        $cparts = array_filter($cparts);
    }
    $path = implode("/", $cparts);
    $url = "";
    if(@$scheme) {
        $url = "$scheme://";
    }
    if(@$user) {
        $url .= "$user";
        if($pass) {
            $url .= ":$pass";
        }
        $url .= "@";
    }
    if(@$host) {
        $url .= "$host/";
    }
    $url .= $path;
    return $url;
}

function getParamFromUrl($url, $param)
{
	$url_parsed = parse_url($url);
	if (isset($url_parsed['query'])) {
		parse_str($url_parsed['query'], $url_part);
		if (isset($url_part[$param])) {
			return $url_part[$param];
		}
	}
	return FALSE;
}

function httpRequest($url, $useragent = 'desktop', $timeout = 10, $max_redirect = 1)
{
	$useragents = array(
		'desktop' => "Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36",
		// 'desktop' => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36",
		'mobile' => "Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_0 like Mac OS X; en-us) AppleWebKit/528.18 (KHTML, like Gecko) Version/4.0 Mobile/7A341 Safari/528.16", 
		// 'mobile' => "Mozilla/5.0 (iPhone; CPU iPhone OS 7_0 like Mac OS X; en-us) AppleWebKit/537.51.1 (KHTML, like Gecko) Version/7.0 Mobile/11A465 Safari/9537.53" 
	);
	$client = new Zend_Http_Client($url);
		$config = array(
	        "useragent"		=> $useragent, 
	        "timeout"		=> $timeout, 
	        "maxredirects"	=> $max_redirect
		);
	$client->setConfig($config);
	
	return $body = $client->request("GET")->getBody();
}