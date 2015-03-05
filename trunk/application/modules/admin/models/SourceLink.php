<?php
class Admin_Models_SourceLink extends Model_SourceLink{
	
	public function tableData()
	{
		$headers = array("&nbsp;", "Title", "Source","URL","Category", "Sub Category","Control");
		
		$model_template = new Model_SourceTemplate;
		$model_source = new Model_SourcePage;
		$model_subcategory = new Model_Subcategory;
		$model_category = new Model_Category;
		
		$db = $this->getDefaultAdapter();
		$sql = $db->select()->from(array("l" => $this->_name))
					->joinLeft( array("t"	 => $model_template->_name), "l.template_id = t.id",array() )
					->joinLeft( array("s"	 => $model_source->_name), "t.source_id = s.id",array("source" => "s.title", "source_icon" => "s.icon", "homepage"))
					->joinLeft( array("sctg" => $model_subcategory->_name), "l.subcategory_id = sctg.id",array("subcategory" => "sctg.title", "sub_ctg_icon" => "sctg.icon"))
					->joinLeft( array("ctg"	=> $model_category->_name), "sctg.category_id = ctg.id",array("category" => "ctg.title", "ctg_icon" => "ctg.icon"))
				;
	  	$result = $db->fetchAll($sql);
		
		$rows = array();
		foreach ($result as $key => $value) {
			$control = "<a class=\"nextStep\" href=\"/admin/resource/addlink?id=$value[id]\" >Edit</a>";
			$control.= " | <a onClick=\"deleteRow(this,'link',$value[id])\" class=\"deleteRowBtn\" >Delete</a>";
			
			$row = array();
			$row['noClick col-selector'] = "<input type=\"checkbox\" class=\"simple hidden inline-checkbox\" value=\"$value[id]\">";
			$row['title'] = $value['title'];
			$row['source'] = $value['source'];
			$row['url'] = $value['url'];
			$row['category'] = $value['category'];
			$row['sub_category'] = $value['subcategory'];
			$row['control'] = $control;

			$rows[] = $row;
		}
		
		return array('headers' => $headers, 'rows' => $rows);
	}
}