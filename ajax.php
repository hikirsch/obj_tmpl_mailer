<?php

// i load other classes, set some paths for convenience, etc.
require_once( "php/bootstrap.php" );

// PHP does not make this easy, this is a util I made
$json = get_json();

// i am declaring this formData object from the JS, you may want to send
// up other information along with the form
$template_data = $json[ "formData" ];

try {
	// this is the main function, an exception will be thrown if this fails.
	send_template_email( array(
		"to" => "accounts@adamskirschner.com",
//		"cc" => array( "fake@gmail.com", "fake2@gmail.com" ),
		"bcc" => null,
		"subject" => "This is the subject",
		"bodyTemplate" => "email_template",
		"data" => $template_data
	) );

	// handle the success
	JSONResponse( array(
		"message" => "Email sent successfully."
	) );
} catch( PEARErrorException $e ) {
	$error = $e->error;
	$message = str_replace( "\n", " ", $error->message );
	JSONResponse( array(
		"error" => $message
	), false );
}
