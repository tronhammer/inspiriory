<?php
	
	abstract class Questions /* extends table */ {
		static public $data = array(
			'id' => 'questions',
			'name' => 'Questions',
			'fields' => array(
				'id'=>array(
					'id'=>'id',
					'name'=>'Question ID',
					'type'=>'int',
					'length'=>2,
					'required_to'=>array(DBHandler::DELETE),
					'hidden_to'=>array(DBHandler::CREATE),
					'validate'=>'validate_id'
				),
				'name'=>array(
					'id'=>'name',
					'name'=>'Question Name',
					'type'=>'string',
					'length'=>80,
					'required_to'=>array(DBHandler::CREATE),
					'hidden_to'=>array(DBHandler::DELETE),
					'validate'=>'validate_name'
				),
				'body'=>array(
					'id'=>'body',
					'name'=>'Question Body Content',
					'type'=>'string',
					'length'=>255,
					'required_to'=>array(DBHandler::CREATE),
					'hidden_to'=>array(DBHandler::DELETE),
					'validate'=>'validate_body'
				),
				'type'=>array(
					'id'=>'type',
					'name'=>'Question Type',
					'type'=>'int',
					'hidden_to'=>array(DBHandler::DELETE),
					'validate'=>'validate_type'
				)
			)
		);
		
		static public function validate_id($val){
			return true;
		}
		
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