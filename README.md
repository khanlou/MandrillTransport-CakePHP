# Mandrill CakePHP Plugin

This enables using CakeEmail from CakePHP 2.0 with Mandrill.

## Installation

1. Add an email configuration for the Mandrill Transport protocol. Add this to `/app/Config/email.php`. You may find it named `email.php.default`.

		public $mandrill = array(
			'transport' => 'Mandrill',
			'uri' => 'https://mandrillapp.com/api/1.0/',
			'key' => 'your-key-here'
		);

	Be sure to update the API Key to your own key.

2. Copy the `MandrillTransport.php` file to `/app/Lib/Network/Email/MandrillTransport.php`.

## Usage

Usage is based on the `CakeEmail` class and specification.

	$email = new CakeEmail();
	$email->config('mandrill');	
	$email->from('noreply@yourapp.com');
	$email->to('email@domain.com');
	$email->subject('Subject for Email');
	$result = $email->send('Here is some test content for the email.');
	
The `$result` object will contain information about the success or failure of the message sending.

It's very simple right now and doesn't support anything more complex, like multiple recipients or attachments.


© Soroush Khanlou 2013