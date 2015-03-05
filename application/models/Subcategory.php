<?php
class Model_Subcategory extends Zend_Db_Table_Abstract
{
	public $_name = 'sub_category';
	protected $_primary = 'id';

	public function selectOptions($ctg_id = -1)
	{
		if (!$ctg_id) {
			$ctg_id = -1;
		}
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->select()->from($this->_name)->where("category_id=?",$ctg_id);
	  	$result = $db->fetchAll($sql);
		
		$options = array();
		foreach ($result as $subcategory) {
			$options[$subcategory['id']] = $subcategory['title'];
		}
		
		return $options;
	}
}