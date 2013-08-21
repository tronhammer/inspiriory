<?php
	$pvars = array(
	
	);
	$pfunc = array(
		'create_question' => 'create_question',
		'delete_question' => 'delete_question'
	);
	
	function create_question($args, $vars, $lib){
		$db = new DBHandler($lib);
		$ret = $db->create('questions', $args);
		return $lib['wrap']( $ret );
	}
	
	function delete_question($args, $vars, $lib){
		$db = new DBHandler($lib);
		$ret = $db->delete('questions', $args);
		return $lib['wrap']( $ret );
	}