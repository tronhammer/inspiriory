<?php
	$gvars = array(
		'home_uri' => 'http://inspiriory.com',
		'api_uri' => 'http://inspiriory.com/api/'
	);
	$gfunc = array(
		'questions' => 'list_questions',
	);
	
	function list_questions($args, $vars, $lib){
		$db = new DBHandler($lib);
		$rows = $db->get('questions', 'id,name,body,created');
		if (count($rows)){
			return $lib['wrap']( $rows );
		} else {
			return $lib['wrap']( array(), 1, 'No questions returned from database!' );
		}
	}
