<?php 
class Form {
	// form data members
 	private $data = array(); // values it expects to be inputted
 	private $reqFields = array(); // list of mandatory fields
	private $alerts = array(); // contains type, message
	private $errors = array();

	// CONSTRUCTOR - sets fields as blank entries in the data area, keys as name/id
	public function __construct($fields, $required) {
		$this->reqFields = $required;
		foreach($fields as $field) {$this->data[$field] = '';}
	} // end contruct
	
	// SET ALERT - sets type and message to $alert
	public function setAlert($type, $msg) {
		$this->alerts['type'] = $type;
		$this->alerts['msg'] = $msg;
	} // end setAlert
	
	// DISPLAY ERROR - adds class to html, called in displayForm
	public function displayError($var) {
		if (isset($this->errors) && in_array($var, $this->errors)) {return ' class="error"';}
	} // end displayError
	
	// DISPLAY ALERTS -
	public function displayAlerts() {
		$v = '<div class="alert-box">';
		if (!empty($this->alerts['msg'])) {
			$v .= '<p class="' . $this->alerts['type'] . '">' . $this->alerts['msg'];
			if (!empty($this->errors)) {			
				$eNum = count($this->errors);
				if ($eNum == 1) {
					if ($this->errors[0] != 'email') { $v .= ' The following field has been highlighted for you: ' . $this->errors[0] . '.';}
				} elseif ($eNum == 2) {
					$v .= ' The following fields have been highlighted for you: ' . $this->errors[0] . ' and ' . $this->errors[1] . '.';
				} else {
					for ($i = 0; $i < $eNum; $i++) {
						switch($i) {
							case(0);
							$v .= ' The following fields have been highlighted for you: ' . $this->errors[$i] . ', ';
							break;
					
							case($eNum - 1);
							$v .= 'and ' . $this->errors[$i] . '.';
							break;
					
							default;
							$v .= $this->errors[$i] . ', ';
							break;
						}
					}
				}
			}				
			$v .= '</p>';
		}
		$v .= '</div>';
		return $v;
	} // end displayAlerts
	
	// DISPLAY FORM - creates the form for display and echoes
	public function displayForm() {
		$v = $this->displayAlerts();
		$v .= '<form action="' . $SERVER['PHP_SELF'] . '#contact" method="post">';
		$v .= '<fieldset><ul>';
		$v .= '<li><label for="name"' . $this->displayError('name') . '>Name:</label><input type="text" id="name" name="name" value="' . $this->data['name'] .'"></li>';
		$v .= '<li><label for="email"' . $this->displayError('email') . '>Email:</label><input type="email" id="email" name="email" value="' . $this->data['email'] .'"></li>';
		$v .= '<li><label for="msg"' . $this->displayError('msg') . '>Message:</label><textarea id="msg" name="msg">' . $this->data['msg'] .'</textarea></li>';
		$v .= '<li class="singular"><label for="robots"' . $this->displayError('robots') . '>The word for 12-1 is </label><input type="text" id="robots" name="robots" value="' . $this->data['robots'] . '"> .</li>';
		$v .= '</ul>';
		$v .= '<input type="submit" id="submit" name="submit" value="Send Email">';
		$v .= '</fieldset></form>';
		echo $v;
	} // end displayForm
	
	// GET DATA - returns the data array
	public function getData() {
		return $this->data;
	} // end getData
	
	// GATHER CLEAN DATA - takes expected values stored in $_POST and sets their cleaned vals to $data
	public function gatherCleanData() {
		if (!empty($_POST)) {
			foreach(array_keys($this->data) as $key) {
				switch ($key) {
					// phone? date? password?
					case ('email');
					$this->data[$key] = filter_var(trim($_POST[$key]), FILTER_SANITIZE_EMAIL);
					break;
					
					case('name');
					$this->data[$key] = filter_var(trim($_POST[$key]), FILTER_SANITIZE_STRING);
					break;
					
					case('robots');
					$this->data[$key] = filter_var(trim($_POST[$key]), FILTER_SANITIZE_STRING);
					break;
					
					default;
					$this->data[$key] = htmlentities(trim($_POST[$key]));
					break;
				} // switch
			} // foreach
			return true;
		} else {return false;}
	} // end gatherCleanData
	
	// TEST REQUIRED FIELDS - tests whether required fields have been filled.
	public function testReqFields() {
		if (!empty($this->reqFields)) {
			for ($i = 0; $i < count($this->reqFields); $i++) {
				$key = $this->reqFields[$i];
				if (empty($this->data[$key])) {
					array_push($this->errors, $key);
				} // end if
			} // end for
			if (empty($this->errors)) {return true;} else {return false;}
		} else {return true;} // end if empty
	} // end testReqFields
	
	// VALIDATE EMAIL - checks to see if email addressed supplied is valid filter_var(, FILTER_VALIDATE_EMAIL)
	public function validateEmail() {
		if (filter_var($this->data['email'], FILTER_VALIDATE_EMAIL)) {
			return true;
		} else {array_push($this->errors, 'email'); return false;}
	}
	
	// VALIDATE ROBOTS - checks to see if the robots is right.
	public function robotCheck() {
		if ($this->data['robots'] == 'eleven') {
			return true;
		} else {array_push($this->errors, 'robots'); return false;}
	}
}

class Email {
	private $headers;
	private $to;
	public $replyto;
	private $from;
	private $body;
	private $subject;
	
	// SEND EMAIL -
	public function sendE() {
		if (mail($this->to, $this->subject, $this->body, $this->headers)) {return true;} else {return false;}
	}
	public function buildHeaders($data) {
		if ( is_array($data) && array_key_exists('email', $data)) {$this->replyto = $data['email'];}
		$this->headers = 'MIME-Version: 1.0' . "\r\n";
		$this->headers .= 'Content-Type: text/html; charset=iso-8859-1' . "\r\n";
		//$this->headers .= 'From: No Reply <' . $this->to . "> \r\n";
		$this->headers .= 'Reply-To: '  . $this->replyto . "\r\n";
	} // end buildHeaders
	
	// BUILD EMAIL - called by constructor to massage data
	public function buildBody($data) {
		
		$this->body = '<html><body style="margin-left: 20px; width:420px;">';
		$this->body .= '<h1 style="margin-bottom: 20px;">' . $this->subject . '</h1><table cellpadding="10px">';
		$this->body .= '<tr><th style="font-weight:normal; text-align:left; vertical-align:top;">Name:</th><td>';
		$this->body .= $data['name'] . '</td></tr>';
		$this->body .= '<tr><th style="font-weight:normal; text-align:left; vertical-align:top;">Email:</th><td>';
		$this->body .= '<a href="mailto:' . $this->replyto . '?subject=' . $this->subject . '">' . $data['email'] . '</a></td></tr>';
		$this->body .= '<tr><th style="font-weight:normal; text-align:left; vertical-align:top;">Message:</th><td>';
		$this->body .= nl2br($data['msg']) . '</td></tr></table></body></html>';
	} // end buildE
	
	// CONSTRUCTOR - passed an array which builds the body?
	public function __construct($to, $data, $subject) {
		$this->to = filter_var($to, FILTER_SANITIZE_EMAIL);
		$this->from = $this->to;
		$this->subject = filter_var($subject, FILTER_SANITIZE_STRING);
		$this->buildHeaders($data);
		$this->buildBody($data);
	} // end constructor
	
} // end of EMAIL class

?>