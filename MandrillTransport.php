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
		$this->_cakeEmail = $email;

		$this->_config = $this->_cakeEmail->config();
		$this->_headers = $this->_cakeEmail->getHeaders(array('from', 'to', 'cc', 'bcc', 'replyTo', 'subject'));

		// Setup connection
		$this->__mandrillConnection = &new HttpSocket();

		$message = $this->__buildMessage();
		
		$request = array(
			'header' => array(
				'Accept' => 'application/json',
				'Content-Type' => 'application/json',
			)
		);
		
		if ($this->_cakeEmail->template) {
			$message_send_uri = $this->_config['uri'] . "messages/send-template.json";
		} else {
			$message_send_uri = $this->_config['uri'] . "messages/send.json";
		}
		
		//perform the http connection
/* 		$returnMandrill = $this->__mandrillConnection->post($message_send_uri, json_encode($message), $request); */
		
		
		//parse mandrill results
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
		
		$json = array();
		$json["key"] = $this->_config['key'];
		if ($this->_cakeEmail->template) {
			$json["template_name"] = $this->_cakeEmail->template;
			$json["template_content"] = array();
		}

		$message = array();
		
		$_merge_vars = array();
		foreach ($this->_cakeEmail->viewVars as $key => $mergeVar) {
			$_merge_vars[] = array("name" => $key, "content" => $mergeVar);
		}
		
		$message["merge_vars"] = array();
		$message["merge_vars"][] = array("rcpt" => $this->_headers['To'],
										"vars" => $_merge_vars);


				
		$message['from_email'] = $this->_headers['From'];

		$message["to"] = array(
			array("email" => $this->_headers['To'])
		);


		$message['subject'] = mb_decode_mimeheader($this->_headers['Subject']);


		if ($this->_cakeEmail->emailFormat() === 'html' || $this->_cakeEmail->emailFormat() === 'both') {
			$message['html'] = $this->_cakeEmail->viewVars["MESSAGE_TEXT"];
		}

		if ($this->_cakeEmail->emailFormat() === 'text' || $this->_cakeEmail->emailFormat() === 'both') {
			$message['text'] = $this->_cakeEmail->viewVars["MESSAGE_TEXT"];
		}
		
		$json["message"] = $message;


		return $json;
	}



}
