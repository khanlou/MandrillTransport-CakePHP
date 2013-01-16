<?php
/**
 * Mandrill Transport
 *
 */

App::uses('AbstractTransport', 'Network/Email');
App::uses('HttpSocket', 'Network/Http');

/**
 * MandrillTransport
 *
 * This class is used for sending email messages
 * using the Mandrill API http://mandrillapp.com/
 *
 */
class MandrillTransport extends AbstractTransport {

/**
 * CakeEmail
 *
 * @var CakeEmail
 */
	protected $_cakeEmail;

/**
 * Variable that holds Mandrill connection
 *
 * @var HttpSocket
 */
	private $__mandrillConnection;

/**
 * CakeEmail headers
 *
 * @var array
 */
	protected $_headers;

/**
 * Configuration to transport
 *
 * @var mixed
 */
	protected $_config = array();

/**
 * Sends out email via Mandrill
 *
 * @return array Return the Mandrill
 */
	public function send(CakeEmail $email) {
		// CakeEmail
		$this->_cakeEmail = $email;

		$this->_config = $this->_cakeEmail->config();
		$this->_headers = $this->_cakeEmail->getHeaders(array('from', 'to', 'cc', 'bcc', 'replyTo', 'subject'));

		// Setup connection
		$this->__mandrillConnection = &new HttpSocket();

		// Build message
		$message = $this->__buildMessage();
		
		// Build request
		$request = $this->__buildRequest();

		$message_send_uri = $this->_config['uri'] . "messages/send.json";
		
		// Send message
		$returnMandrill = $this->__mandrillConnection->post($message_send_uri, json_encode($message), $request);
		
		// Return data
		$result = json_decode($returnMandrill, true);
				
		$headers = $this->_headersToString($this->_headers);

		return array_merge(array('Mandrill' => $result), array('headers' => $headers, 'message' => $message));
	}

/**
 * Build message
 *
 * @return array
 */
	private function __buildMessage() {
		// Message
		
		$json = array();
		$json["key"] = $this->_config['key'];

		$message = array();
				
		// From
		$message['from_email'] = $this->_headers['From'];

		// To
		$message["to"] = array(
			array("email" => $this->_headers['To'])
		);


		// Subject
		$message['subject'] = mb_decode_mimeheader($this->_headers['Subject']);


		// HtmlBody
		if ($this->_cakeEmail->emailFormat() === 'html' || $this->_cakeEmail->emailFormat() === 'both') {
			$message['html'] = $this->_cakeEmail->message('html');
		}

		// TextBody
		if ($this->_cakeEmail->emailFormat() === 'text' || $this->_cakeEmail->emailFormat() === 'both') {
			$message['text'] = $this->_cakeEmail->message('text');
		}
		
		
		$json["message"] = $message;


		return $json;
	}


/**
 * Build request
 *
 * @return array
 */
	private function __buildRequest () {
		$request = array(
			'header' => array(
				'Accept' => 'application/json',
				'Content-Type' => 'application/json',
			)
		);

		return $request;
	}

}
