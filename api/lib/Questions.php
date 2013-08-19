<?php
	
	abstract class Questions /* extends table */ {
		static public $data = array(
			'id' => 'questions',
			'name' => 'Questions',
			'fields' => array(
				'name'=>array(
					'id'=>'name',
					'name'=>'Question Name',
					'type'=>'string',
					'length'=>80,
					'required'=>1,
					'validate'=>'validate_name'
				),
				'body'=>array(
					'id'=>'body',
					'name'=>'Question Body Content',
					'type'=>'string',
					'length'=>255,
					'required'=>1,
					'validate'=>'validate_body'
				),
				'type'=>array(
					'id'=>'type',
					'name'=>'Question Type',
					'type'=>'int',
					'default'=>1,
					'validate'=>'validate_type'
				)
			)
		);
		
		static public function validate_name($val){
			return true;
		}
		
		static public function validate_body($val){
			return true;
		}
		
		static public function validate_type($val){
			return true;
		}
	}