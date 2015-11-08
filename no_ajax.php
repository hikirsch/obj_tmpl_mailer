<?php

// i load other classes, set some paths for convenience, etc.
require_once( "php/bootstrap.php" );

// this is the entire form, $_REQUEST["name"] will be the name, if you need
// more stuff here, add a hidden input.
$template_data = $_REQUEST;

try {
	// this is the main function, an exception will be thrown if this fails.
	send_template_email( array(
		"to" => "accounts@adamskirschner.com",
		"cc" => array( "fake@gmail.com", "fake2@gmail.com" ),
		"bcc" => null,
		"subject" => "This is the subject",
		"bodyTemplate" => "email_template",
		"data" => $template_data
	) );

	//redirect with no ajax
	header( "Location: test.html?noajax=true&success=true" );
} catch( PEARErrorException $e ) {
	// This exception class is a wrapper for receiving the Pear error
	// object when the email could not be sent.
	$error = $e->error;

	// if the error has a newline, it will break, seems google will return with newlines for invalid user/pass
	$message = str_replace( "\n", " ", $error->message );

	// THIS IS A POST SUBMIT ERROR, you will most likely want to redirect, you may
	// want to redirect to a PHP page and use $_SESSION to transfer the error string
	header( "Location: test.html?error=" . $message );
}
