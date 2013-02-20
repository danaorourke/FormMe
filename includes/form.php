<?php
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
		} // end build
		
		// CONSTRUCTOR - passed an array which builds the body?
		public function __construct($to, $data, $subject) {
			$this->to = filter_var($to, FILTER_SANITIZE_EMAIL);
			$this->from = $this->to;
			$this->subject = filter_var($subject, FILTER_SANITIZE_STRING);
			$this->buildHeaders($data);
			$this->buildBody($data);
		} // end constructor
		
	} // end of EMAIL class

	class Form {
	 	private $data = array(); // values it expects to be inputted
	 	private $reqFields = array(); // list of mandatory fields
		private $alerts = array(); // contains type, message
		private $errors = array(); // array of vars with errors
		
		
		// DISPLAY ERROR - adds class to html, called in displayForm
		public function displayError($var) {
			if (isset($this->errors) && in_array($var, $this->errors)) {echo ' class="error"';}
		} // end displayError
		
		// SET ALERT - sets type and message to $alert
		public function setAlert($type, $msg) {
			$this->alerts['type'] = $type;
			$this->alerts['msg'] = $msg;
		} // end setAlert
		
		// DISPLAY ALERT - displays values stored in $alert
		private function displayAlert() {
			echo '<div class="alert-box">';
			if (!empty($this->alerts)) {?>
				<p class="<?php echo $this->alerts['type']; ?>"><?php echo $this->alerts['msg']; 
					if(!empty($this->errors)) {
						$errorNum = count($this->errors);
						if ($errorNum == 1) {
							if ($this->errors === 'email'){} else { echo ' The following field has been highlighted for you: ', $this->errors[0], '.'; }
						} elseif ($errorNum == 2) {
							echo ' The following fields have been highlighted for you: ', $this->errors[0], ' and ', $this->errors[1], '.';
						} else {						
							for($i = 0; $i < $errorNum; $i++) {
								switch($i) {
									case (0);
									echo ' The following fields have been highlighted for you: ', $this->errors[$i], ', ';
									break;
									
									case ($errorNum - 1);
									echo 'and ', $this->errors[$i], '.';
									break;
									
									default;
									echo $this->errors[$i], ', ';
									break;
								} // end switch
							} // end for
						} // end if
					} // end if ?></p>
			<? } // end if
			echo '</div>';
		} // end displayAlert
		
		// GATHER CLEAN DATA - takes expected values stored in $_POST and sets their cleaned vals to $data
		public function gatherCleanData() {
			if (!empty($_POST)) {
				foreach(array_keys($this->data) as $key) {
					switch ($key) {
						case ('email');
						$this->data[$key] = filter_var(trim($_POST[$key]), FILTER_SANITIZE_EMAIL);
						break;
						
						case('name');
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

		// DISPLAY FORM - displays alert box and sticky form with error highlighting
		public function displayForm() {
			$this->displayAlert(); ?>
			<!-- form starts here -->
			<form action="<?php echo $SERVER['PHP_SELF']; ?>#contact" method="post">
				<fieldset>
					<span id="stamp"><?php echo date("n j Y"); ?></span> 
					<legend>Freelancer Post</legend>
					<div class="left">
						<label for="msg"<?php $this->displayError('msg'); ?>>Send me a message!</label>
						<textarea id="msg" name="msg"><?php echo $this->data['msg']; ?></textarea>
					</div>
					<div class="right">
						<ul>
							<li><label for="name"<?php $this->displayError('name'); ?>>Your name:</label><input type="text" id="name" name="name" value="<?php echo $this->data['name']; ?>"></li>
							<li><label for="email"<?php $this->displayError('email'); ?>>Your email address:</label><input type="email" id="email" name="email" value="<?php echo $this->data['email']; ?>"></li>
						</ul>
					</div>
					<input type="submit" id="submit" name="submit" value="Send Email">
				</fieldset>
			</form> <?
		} // end displayForm
		
		// GET DATA - returns the data array
		public function getData() {
			return $this->data;
		} // end getData

		// CONSTRUCTOR - sets fields as blank entries in the data area, keys as name/id
		public function __construct($fields, $required) {
			$this->reqFields = $required;
			foreach($fields as $field) {
				$this->data[$field] = '';
			}
		} // end contruct
	} // end of FORM class	
?>