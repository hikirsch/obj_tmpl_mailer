<?php

class TemplateEmail {
	var $NEW_LINE = "\n";
	var $TEMP_DIR = null;

	var $options = null;
	var $templateData = null;
	var $template = null;
	var $templatePath = null;
	var $templateBasePath = null;
	var $files = null;
	var $compiledTXT = null;
	var $compiledHTML = null;

	function __construct( $options ) {
		$this->initTemp();

		$this->options = $options;

		$this->templateBasePath = $this->detectTemplateBasePath();
		$this->files = $this->detectUploads();

		$this->to = $this->normalizeEmails( $this->getOption( "to" ) );
		$this->cc = $this->normalizeEmails( $this->getOption( "cc" ) );
		$this->bcc = $this->normalizeEmails( $this->getOption( "bcc" ) );

		$this->subject = $this->getOption( "subject", "(no subject)" );
		$this->body = $this->getOption( "body" );

		$this->templatePath = $this->getOption( "bodyTemplate" );
		$this->templateData = $this->getOption( "data" );

		if( $this->templatePath != null ) {
			$this->compiledHTML = $this->compile( 'html' );
			$this->compiledTXT = $this->compile( 'txt' );
		}

		$this->sendEmail();

		$this->deleteTemp();
	}

	function getOption( $name, $default = null ) {
		if( array_key_exists( $name, $this->options ) ) {
			return $this->options[ $name ];
		}

		return $default;
	}

	function normalizeEmails( $val ) {
		if( is_array( $val ) ) {
			return $val;
		}

		return array( $val );
	}

	function sendEmail() {
		foreach( $this->to as $to ) {
			$headers = array(
				'From' => SMTP_FROM_NAME . "<" . SMTP_FROM . ">",
				'To' => $to,
				'Subject' => $this->subject
			);

			$mime = new Mail_mime( $this->NEW_LINE );

			if( $this->body == null ) {
				if( $this->compiledTXT != null && strlen( $this->compiledTXT ) > 0 ) {
					$mime->setTXTBody( $this->compiledTXT );
				}

				if( $this->compiledHTML != null && strlen( $this->compiledHTML ) > 0 ) {
					$mime->setHTMLBody( $this->compiledHTML );
				}
			} else {
				$mime->setTXTBody( $this->body );
			}

			foreach( $this->cc as $email ) {
				$mime->addCc( $email );
			}

			foreach( $this->bcc as $email ) {
				$mime->addBcc( $email );
			}

			if( is_array( $this->files ) && count( $this->files ) > 0 ) {
				foreach( $this->files as $file ) {
					$mime->addAttachment( $file[ "path" ], $file[ "content-type" ] );
				}
			}

			$body = $mime->get();
			$headers = $mime->headers( $headers );

			$string = "";
			foreach( $headers as $key => $value ) {
				$string .= "$key: $value\r\n";
			}

			$smtpOptions = array(
				'host' => SMTP_HOST,
				'port' => SMTP_PORT,
			);

			if( defined( "SMTP_USER_NAME" ) && defined( "SMTP_PASSWORD" ) ) {
				$smtpOptions[ 'auth' ] = true;
				$smtpOptions[ 'username' ] = SMTP_USER_NAME;
				$smtpOptions[ 'password' ] = SMTP_PASSWORD;
			}

			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			$smtp = Mail::factory( 'smtp', $smtpOptions );

			$success = $smtp->send( $to, $headers, $body );

			if( $success !== true ) {
				throw new PEARErrorException( $success );
			}
		}
	}

	function compile( $ext ) {
		$this->template = implode( "", file( $this->templateBasePath . "/" . $this->templatePath . "." . $ext ) );

		$re = "/\\$\\{([A-Za-z0-9_-]*)}/";
		preg_match_all( $re, $this->template, $matches );

		if( $matches ) {
			$matches = $matches[ 1 ];

			$compiled = $this->template;

			foreach( $matches as $match ) {
				$regex = '/\$\{' . $match . '\}/';

				$value = ( array_key_exists( $match, $this->templateData ) )
					? $this->templateData[ $match ]
					: "";

				$compiled = preg_replace( $regex, $value, $compiled );
			}
		}

		return $compiled;
	}

	function detectTemplateBasePath() {
		return realpath( PROJECT_ROOT . "/" . TEMPLATE_BASE_FOLDER );
	}

	function detectUploads() {
		$files = array();

		foreach( $_FILES as $file ) {
			if( $file[ "size" ] > 0 ) {
				$newFileName = $this->TEMP_DIR . "/" . $file[ "name" ];

				rename( $file[ "tmp_name" ], $newFileName );

				array_push( $files, array(
					"path" => $newFileName,
					"content-type" => $file[ "type" ]
				) );
			}
		}

		return $files;
	}

	private function deleteTemp() {
		foreach( $this->files as $file ) {
			unlink( $file[ "path" ] );
		}

		rmdir( $this->TEMP_DIR );
	}

	private function initTemp() {
		$this->TEMP_DIR = tempdir();
	}
}

function send_template_email( $options ) {
	new TemplateEmail( $options );
}