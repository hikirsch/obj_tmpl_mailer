<?php

function get_json() {
	$inputJSON = file_get_contents( 'php://input' );
	$input = json_decode( $inputJSON, TRUE );

	return $input;
}

function JSONResponse( $data = null, $success = true ) {
	header( "Content-type: application/json" );

	echo json_encode( array(
		"success" => $success,
		"data" => $data
	) );
}

function tempdir() {
	$tempfile = tempnam( sys_get_temp_dir(), '' );

	if( file_exists( $tempfile ) ) {
		unlink( $tempfile );
	}

	$tempfile .= "_";

	mkdir( $tempfile );

	if( is_dir( $tempfile ) ) {
		return $tempfile;
	}
}

function get_request_content_type() {
	return getallheaders()[ "Content-Type" ];
}

class PEARErrorException extends Exception {
	var $error = null;
	public function __construct( $error ) {
		parent::__construct();

		$this->error = $error;
	}
}

