<?php
	$func = array(
		'wrap' => 'wrap',
		'std_return' => 'std_return'
	);
	
	function wrap($ret, $code = 0, $message = 'Success'){
		return json_encode(
			array(
				'data'=> $ret,
				'status'=> array(
					'code' => $code,
					'message' => $message
				)
			)
		);
	}
	
	function std_return($body){
		if (!$body->status->code){
			return wrap( $body->data );
		} else {
			return wrap( array(), $body->status->code, $body->status->message );
		}
	}
