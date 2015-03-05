<?php
class Model_Category extends Zend_Db_Table_Abstract
{
	public $_name = 'category';
	protected $_primary = 'id';
	
	public function selectOptions($include_none = FALSE)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->select()
				  ->from($this->_name);
	  	$result = $db->fetchAll($sql);
		
		$options = array();
		if ($include_none) {
			$options[0] = "---";
		}
		foreach ($result as $subcategory) {
			$options[$subcategory['id']] = $subcategory['title'];
		}
		
		return $options;
	}
	
	// public function getList()
	// {
		// $db = Zend_Db_Table::getDefaultAdapter();
		// $sql = $db->select()
				  // ->from($this->_name);
	  	// return $db->fetchAll($sql);
	// }
	
	public function detail($category_id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->select()
				  ->from($this->_name)
				  ->where('id = ?',$category_id);
	  	return $db->fetchRow($sql);
	}
	
	public function tableData($type)
	{
		$headers = array("Title", "Created Time","Sort", "Control");
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->select()
				  ->from($this->_name)
				  ->where('type_id = ?',$type)
				  ->order('sort asc');
				  // ->where('parent_id = ?',$parent_id);
	  	$result = $db->fetchAll($sql);
		
		$rows = array();
		foreach ($result as $key => $value) {
			$action = "<a class=\"nextStep\" href=\"/admin/manage/subcategory?category_id=$value[id]\" class=\"\" >Categories</a>";
			$action.= " | <a href=\"/admin/manage/addcategory?category_id=$value[id]&type=$value[type_id]\" class=\"\" >Edit</a>";
			$action.= " | <a href=\"/admin/manage/deletecategory?category_id=$value[id]\" class=\"deleteRowBtn\" >Delete</a>";
			
			$row = array();
			$row[] = $value['title'];
			$row[] = $value['created_time'];
			$row[] = $value['sort'];
			$row[] = $action;
			
			$rows[] = $row;
		}
		return array('headers' => $headers, 'rows' => $rows);
	}

	public function getListCategory($type = -1, $sort = -1){
		$db = Zend_Db_Table::getDefaultAdapter();
		$mySql = $db->select()
					->from($this->_name)
					->where("type_id = ?", $type)
					->where("sort >= ?", $sort)
					->order("sort asc");
		$result = $db->fetchAll($mySql);
		return $result;
	}

	public function getByTitle($title){
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->select()
			->from($this->_name)
			->where('title = ?',$title);
		return $db->fetchRow($sql);
	}
}