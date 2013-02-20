<?php
require_once('includes/init.php');

if ($inc == TRUE) {
// included
	$fields = $req = array('name', 'email', 'msg', 'robots');
	$form = new Form($fields,$req);
	
	if ( isset($_POST['submit']) ) {
		if ($form->gatherCleanData()) {
			if ($form->testReqFields()) {
				if ($form->validateEmail()) {
					if ($form->robotCheck()) {
						$data = $form->getdata();
						$email = new Email('dana@cupofteacreations.com', $data, 'Website Form Submission');
						if ($email->sendE()) {$form->setAlert('confirm', 'Email sent. You should hear back from us shortly!');} else {$form->setAlert('error', 'Email not sent. Please contact the domain administrator at dana@cupofteacreations.com');}
					} else {
						$form->setAlert('error', 'I think you might be a robot.');
					}
				} else {
					$form->setAlert('error', 'The email supplied is invalid. We have highlighted the field for you.');
				}
			} else {$form->setAlert('error', 'Required fields were left blank.');}
		}
	}
	$form->displayForm();

	
} else {
// individual page
	$bodyid = 'contact';
	$title = 'Form Me';
	
	require_once('includes/header.php');
	echo '<section>';
	echo '<h1>Need a longer form?</h1>';
	$form = new Form();
	$form->displayForm();
	echo '</section>';
	require_once('includes/footer.php');
} ?>