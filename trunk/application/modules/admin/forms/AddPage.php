<?php
class Admin_Forms_AddPage extends Zend_Form {
	function __construct($options = null) {
		$this->setAction('')
				->setMethod('POST')
				->setAttrib('class','form-horizontal')
				->setAttrib('enctype','multipart/form-data');
		$id = $this->createElement('hidden', 'id',array(
														'value' => @$options['id'],
														)
									);
		$title = $this->createElement('text', 'title',array(
															'value' => @$options['title'],
															'label' => "Title",
															'class' => 'form-control'
															)
									);
		$title->setRequired ( true );
		$homepage = $this->createElement('text', 'homepage',array(
															'value' => @$options['homepage'],
															'label' => "Homepage",
															'class' => 'form-control'
															)
									);
		$icon = $this->createElement('file', 'icon',array(
															'label' => "Icon",
															'accept' => ".png,.jpg,.jpeg,.bmp",
															'class' => 'form-control'
															)
									);
		
		$submit = $this->createElement('submit','login',array ('label' => 'Submit' ,'class'	=>'btn btn-primary'));
		
		
		$this->addElement($id)
			 ->addElement($title)
			 ->addElement($homepage)
			 ->addElement($icon)
			 ->addElement($submit);
			 
		$this->setDecorators(array('FormElements',array('HtmlTag',array('tag'	=> 'div','width'=>'100%')),'form'));
	}
}