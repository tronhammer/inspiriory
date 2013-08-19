<?php
	$pvars = array(
	
	);
	$pfunc = array(
		'create_question' => 'create_question',
	);
	
	function create_question($args, $vars, $lib){
		$db = new DBHandler($lib);
		$ret = $db->create('questions', $args);
		return $lib['wrap']( $ret );
	}