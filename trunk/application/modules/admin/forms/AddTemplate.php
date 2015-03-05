<?php
class Admin_Forms_AddTemplate extends Zend_Form {
	function __construct($options = null) {
		$source_model = new Model_SourcePage;
		
		$YesNo_Options = array("Fasle", "True");
		
		$this->setAction('')
				->setMethod('POST')
				->setAttrib('class','form-horizontal');
		$id = $this->createElement('hidden', 'id',array('value' => @$options['id']));
		$title = $this->createElement('text', 'template',array(
															'value' => @$options['template'],
															'label' => "Title",
															'class' => 'form-control'
															)
		);
		$pageType = $this->createElement("select","mobile_page",array(
																	"class" => "form-control",
			                                                        "label" => "Mobile Page",
			                                                        "value" => @$options['mobile_page'],
			                                                   		"multioptions"=> $YesNo_Options
																)
        );
		$sourcePage=$this->createElement("select","source_id",array(
														"class" => "form-control",
                                                        "label" => "Source Page",
                                                        "value" => @$options['source_id'],
                                                   		"multioptions"=> $source_model->selectOptions()
														)            
                        );
		$list_content_xpath = $this->createElement('text', 'news_list_xpath',array(
															'value' => @$options['news_list_xpath'],
															'label' => "News List Xpath",
															'class' => 'form-control'
															)
									);
		$title_xpath = $this->createElement('text', 'title_xpath',array(
															'value' => @$options['title_xpath'],
															'label' => "Title Xpath",
															'class' => 'form-control'
															)
									);
		$title_from_list = $this->createElement("select","title_from_list",array(
														"class" => "form-control",
														"id" => "title_from_content_select",
                                                        "label" => "Title from List",
                                                        "value" => @$options['title_from_list'],
                                                   		"multioptions"=> $YesNo_Options
														)
                        );
		$description_xpath = $this->createElement("text","description_xpath",array(
															"class" => "form-control",
	                                                        "label" => "Description Xpath",
	                                                        "value" => @$options['description_xpath'],
														)
                        );
		$desc_from_list = $this->createElement("select","description_from_list",array(
														"class" => "form-control",
														"id" => "title_from_content_select",
                                                        "label" => "Description from List",
                                                        "value" => @$options['description_from_list'],
                                                   		"multioptions"=> $YesNo_Options
														)
                        );
		$time_xpath = $this->createElement('text', 'time_xpath',array(
															'value' => @$options['time_xpath'],
															'label' => "Time Xpath",
															'class' => 'form-control'
															)
									);
		$content_xpath = $this->createElement('text', 'content_xpath',array(
															'value' => @$options['content_xpath'],
															'label' => "Content Xpath",
															'class' => 'form-control'
															)
									);
		$tag_xpath = $this->createElement('text', 'tag_xpath',array(
															'value' => @$options['tag_xpath'],
															'label' => "Tag Xpath",
															'class' => 'form-control'
															)
									);
		$time_format = $this->createElement('text', 'time_format',array(
															'value' => @$options['time_format'],
															'label' => "Time Format",
															'class' => 'form-control'
															)
									);
		$remove_tag_xpath = $this->createElement('textarea', 'remove_tag_xpath',array(
															'value' => @$options['remove_tag_xpath'],
															'label' => "Remove Tag Xpath",
															'class' => 'form-control',
															"rows" => 5
															)
									);
		$submit = $this->createElement('submit','login',array ('label' => 'Submit' ,'class'	=>'btn btn-primary'));
		
		$this->addElement($id)
				->addElement($title)
				->addElement($sourcePage)
				->addElement($pageType)
				->addElement($list_content_xpath)
				->addElement($title_xpath)
				->addElement($title_from_list)
				->addElement($description_xpath)
				->addElement($desc_from_list)
				->addElement($time_xpath)
				->addElement($time_format)
				->addElement($content_xpath)
				->addElement($tag_xpath)
				->addElement($remove_tag_xpath)
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