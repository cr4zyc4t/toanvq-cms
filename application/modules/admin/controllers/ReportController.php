<?php

class Admin_ReportController extends Zend_Controller_Action
{
	public function init()
	{
		$option=array(
	        "layout" => "admin",
	        "layoutPath" => APPLICATION_PATH."/layouts/scripts/"
      	);
      	Zend_Layout::startMvc($option);
        $this->view->pageTitle = "Amobi - toanvq";
        $this->view->pageHeader = "Report";
        include 'toanvq.php';
	}
	
	public function indexAction()
	{
		// $this->view->navibar = navibar(4,1);
// 		
		// $this->view->pageSubtitle = "Daily";

		// $this->_redirect("/admin/manage/source?type=1");
		$this->weeklyAction();
	}

    public function generalAction()
    {
        $this->view->navibar = navibar(4,1);

        $this->view->pageSubtitle = "Daily";

        foreach (Model_Tracking::All() as $key => $value) {
            // echo $key."<br/>";
            arr_dump($value['IP']);

            // echo $value['name'];
            // var_dump($value);
            // echo $value['asrgdsrg'];
        }
    }

    public function export($limit = 7) {
        $model_count = new Model_AccessLog;
        $mongo = new MongoClient();

        $db = $mongo->cms;

        $collection = $db->tracking;

        $date_report = @$_GET['date'];
        if(!$date_report){
        	$lastDate_info = $model_count->lastDateReport();
			$date_report = $lastDate_info['date'];
        }
		if (!$date_report) {
			$date_report = date("Y-m-d", strtotime(date("Y-m-d")) - 86400*$limit) ;
		}
		
        $date_stamp = strtotime($date_report);
        while($date_stamp < strtotime(date("Y-m-d")) ){
            $thisdate = date("Y-m-d", $date_stamp);

            $record = $model_count->getByDate($thisdate);
            if(count($record) == 5){
//                arr_dump($record);
            }else{
                $access = $collection->aggregate(
                    array(
                        '$match'=> array(
                            "Timestamp"=> array(
                                '$gte'=> $date_stamp,
                                '$lte' => $date_stamp + 86400,
                            )
                        )
                    ),
                    array(
                        '$group' => array(
                            "_id" => array("tags" => '$IP')
                        )
                    )
                );
                $play = $collection->find(
                    array(
                        "Timestamp"=> array(
                            '$gte'=> $date_stamp,
                            '$lte' => $date_stamp + 86400,
                        ),
                        '$or' => array(
                            array("Request-Params.action" => "play"),
                            array("Request-Params.action" => "getnewsdetail")
                        )
                    )
                );

                $new_record = array(
                    "date" => $thisdate,
                    "user" => count($access['result']),
                    "play" => $play->count()
                );
                $model_count->insert($new_record);
//                arr_dump($new_record);
            }

            $date_stamp+= 86400;
        }
    }

    public function dailyAction()
    {
        $this->view->navibar = navibar(4,1);

        $this->view->pageSubtitle = "Daily";

        // $this->_redirect("/admin/manage/source?type=1");
    }

    public function weeklyAction()
    {
        $this->view->navibar = navibar(4,2);

        $this->view->pageSubtitle = "Weekly";
		$this->export();
        // $this->_redirect("/admin/manage/source?type=1");
    }

	public function monthlyAction()
    {
        $this->view->navibar = navibar(4,3);

        $this->view->pageSubtitle = "Monthly";
		$this->export(30);
        // $this->_redirect("/admin/manage/source?type=1");
    }

    public function weeklychartdataAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $model_access = new Model_AccessLog;
        echo json_encode($model_access->chartData());
    }
	
	public function monthlychartdataAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $model_access = new Model_AccessLog;
        $chartData = $model_access->chartData(30);
		
		$x_labels = array();
		$value_user = array();
		$value_play = array();
		foreach ($chartData as $key => $data) {
			array_push($x_labels,$data['date']);
			array_push($value_user,intval($data['user']));
			array_push($value_play,intval($data['play']));
		}
		
		$response = array(
			"labels" => array_reverse($x_labels),
			"series" => array(
				array(
					"name" => "User Access",
					"data" => array_reverse($value_user)
				),
				array(
					"name" => "Play",
					"data" => array_reverse($value_play)
				),
			)
		);
		echo json_encode($response);
    }
	
	public function highchartdataAction()
	{
		$this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $model_access = new Model_AccessLog;
        $chartData = $model_access->chartData();
		
		$x_labels = array();
		$value_user = array();
		$value_play = array();
		foreach ($chartData as $key => $data) {
			array_push($x_labels,$data['date']);
			array_push($value_user,intval($data['user']));
			array_push($value_play,intval($data['play']));
		}
		
		$response = array(
			"labels" => array_reverse($x_labels),
			"series" => array(
				array(
					"name" => "User Access",
					"data" => array_reverse($value_user)
				),
				array(
					"name" => "Play",
					"data" => array_reverse($value_play)
				),
			)
		);
		echo json_encode($response);
	}
}