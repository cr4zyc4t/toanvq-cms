<?php
class Model_SourcePage extends Zend_Db_Table_Abstract
{
	public $_name = 'source_page';
	protected $_primary = 'id';

	public function getById($id)
	{
		$id = intval($id);
	  	return $this->fetchRow("id=$id")->toArray();
	}
	
	public function selectOptions($include_none = FALSE)
	{
		$options = array();
		if ($include_none) {
			$options[0] = "---";
		}
		foreach ($this->fetchAll()->toArray() as $subcategory) {
			$options[$subcategory['id']] = $subcategory['title'];
		}
		
		return $options;
	}
}