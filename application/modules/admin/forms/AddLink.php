<?php
class Admin_Forms_AddLink extends Zend_Form {
	function __construct($options = null) {
		$model_template = new Model_SourceTemplate;
		$model_category = new Model_Category;
		$model_subcategory = new Model_Subcategory;
		$model_source = new Model_SourcePage;
		
		$YesNo_Options = array("Fasle", "True");
		
		$this->setAction('')
				->setMethod('POST')
				->setAttrib('class','form-horizontal');
		$id = $this->createElement( 'hidden', 'id',array( 'value' => @$options['id'] ) );
		$title = $this->createElement('text', 'title',array(
															'value' => @$options['title'],
															'label' => "Title",
															'class' => 'form-control'
															)
									);
		$url = $this->createElement('text', 'url',array(
															'value' => @$options['url'],
															'label' => "URL",
															'class' => 'form-control'
															)
									);
		$template = $this->createElement("select","template_id",array(
													"class" => "form-control",
		                                            "label" => "Template",
		                                            "value" => @$options['template_id'],
		                                       		"multioptions"=> $model_template->selectOptions()
													)           
                        );
		$category=$this->createElement("select","category_id",array(
														"class" => "form-control",
														"id" => "category_select",
                                                        "label" => "Category",
                                                        "value" => @$options['category_id'],
                                                   		"multioptions"=> $model_category->selectOptions(TRUE)
														)
                        );
        $subcategory=$this->createElement("select","subcategory_id",array(
														"class" => "form-control",
														"id" => "subcategory_select",
                                                        "label" => "Sub-Category",
                                                        "value" => @$options['subcategory_id'],
                                                   		"multioptions"=> $model_subcategory->selectOptions(@$options['category_id'])
														)
                        );
		$processed = $this->createElement("select","processed",array(
														"class" => "form-control",
														"id" => "processed",
                                                        "label" => "Processed",
                                                        "value" => @$options['processed'],
                                                   		"multioptions"=> array("-1" => "Error", "0" => "Not run yet", "1" => "Already run")
														)
                        );
		$submit = $this->createElement('submit','login',array ('label' => 'Submit' ,'class'	=>'btn btn-primary'));
		
		$this->addElement($id)
				->addElement($title)
				->addElement($template)
				->addElement($category)
				->addElement($subcategory)
				->addElement($url)
				->addElement($processed)
				->addElement($submit);
				
		$this->setDecorators(array('FormElements',array('HtmlTag',array('tag'	=> 'div','width'=>'100%')),'form'));
		$this->setElementDecorators(array(
										'ViewHelper',
										'Errors',
										'Description',
										array(	
											array('data'	=> 'HtmlTag'),
											array('tag'		=> 'div','class'	=> 'col-md-6')),
										array('label',
											array('class'	=> 'col-md-3 control-label')),
										array(
											array('row'	=> 'HtmlTag',),
											array('class'=>'form-group')
		        						)
									)
								);
		$submit->setDecorators(array(
								'ViewHelper',
								array(
									array('data'	=> 'HtmlTag'),
									array('tag'		=>	'div','class'	=> 'col-md-9 col-md-offset-3')
								),
								array(
									array('row'	=> 'HtmlTag'),
									array('tag'	=> 'div',"class"	=> "form-group"))
									));
	}
}