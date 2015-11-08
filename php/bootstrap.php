<?php

if( !ini_get( "file_uploads" ) ) {
	die( "file_uploads option must be enabled" );
}

set_include_path( ini_get( "include_path" ) . ":" . dirname( __FILE__ ) );

define( "PROJECT_ROOT", realpath( dirname( __FILE__ ) . "/.." ) );

require_once( "config.php" );
require_once( "Mail.php" );
require_once( "Mail/mime.php" );
require_once( "ask/utils.php" );
require_once( "ask/TemplateEmail.php" );

