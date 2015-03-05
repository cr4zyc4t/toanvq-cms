<?php
class Model_SourceLink extends Zend_Db_Table_Abstract{
	public $_name = 'source_link';
	protected $_primary = "id";
	
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
	
	public function getDetail($id)
	{
		$model_template = new Model_SourceTemplate;
		$model_source = new Model_SourcePage;
		$model_subcategory = new Model_Subcategory;
		$model_category = new Model_Category;
		
		$db = $this->getDefaultAdapter();
		
		$query_link = $this->select()->where("id = ? ",$id);
		
		$query = $db->select()->from(array("l" => $query_link))
					->joinLeft( array("t"	 => $model_template->_name), "l.template_id = t.id")
					->joinLeft( array("sctg" => $model_subcategory->_name), "l.subcategory_id = sctg.id",array("subcategory" => "sctg.title", "sub_ctg_icon" => "sctg.icon", "category_id"))
					->joinLeft( array("ctg"	=> $model_category->_name), "sctg.category_id = ctg.id",array("category" => "ctg.title", "ctg_icon" => "ctg.icon"));
		return $db->fetchRow($query);
	}
}