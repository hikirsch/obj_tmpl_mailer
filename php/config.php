<?php

// Where the template folders are, relative to the PROJECT_ROOT
define( "TEMPLATE_BASE_FOLDER", "tmpl" );

/***** GMAIL CONFIG: *****/
//define( "SMTP_HOST", "ssl://smtp.gmail.com" );
//define( "SMTP_PORT", '465' );
//define( "SMTP_FROM_NAME", "Adam's Web Form" );
//define( "SMTP_FROM", "email@gmail.com" );
//define( "SMTP_USER_NAME", SMTP_FROM ); // use the same username as FROM for gmail (or others too)
//define( "SMTP_PASSWORD", "" );

/********* EITHER THE TOP OR THE BOTTOM ****************/

// Run the following to test locally:
// python -m smtpd -n -c DebuggingServer localhost:1025
//define( "SMTP_HOST", "localhost" );
//define( "SMTP_PORT", '1025' );
//define( "SMTP_FROM_NAME", "Adam's Web Form" );
//define( "SMTP_FROM", "me@fake.com" );