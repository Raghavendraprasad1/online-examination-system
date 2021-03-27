<?php 
require('components/header.php');
require('components/formhandler.php');
require('components/dbhandler.php');

// Empty all strings
$institute = $first_name = $last_name = $sex = $contact_number = $email = $password = $confirm_pass_word = '';

// Sanitize and validate every input using filters.
if(a_server('REQUEST_METHOD') == 'POST'){
    $institute = a_post('institute');
    $first_name = a_post('firstname');
    $last_name = a_post('lastname');
    $sex = a_post('sex');
    $contact_number = a_post('contactnumber');
    $email = a_post('email');
    $pass_word = a_post('password');
    $confirm_pass_word = a_post('confirmpassword');
    
    // Check if any of above is empty and display appropriate errors.
    $errorMsg = '';
    if(empty($first_name)){
        $errorMsg .= '* First Name not valid.<br />';
    }
    if(empty($last_name)){
        $errorMsg .= '* Last Name not valid.<br />';
    }
    if(empty($sex)){
        $errorMsg .= '* Sex not valid.<br />';
    }
    if(empty($contact_number)){
        $errorMsg .= '* Contact Number not valid.<br />';
    }
    if(empty($email)){
        $errorMsg .= '* Email not valid.<br />';
    }
    if(empty($pass_word)){
        $errorMsg .= '* Password not valid.<br />';
    }
    if($pass_word != $confirm_pass_word){
        $errorMsg .= '* Passwords do not match.<br />';
    }
    
    if(!empty($errorMsg)){
        // If any errors display them to the user.
        echo '<div id="errorMsg"><u>Errors: </u><br />'. $errorMsg . '</div>';
    }else{
    $last_insert_id = a_exec('INSERT INTO candidate_info(First_Name, Last_Name, Sex, Contact_number, '
                . 'Email, Pass_Word) '
                . 'VALUES(?, ?, ?, ?, ?, ?)', [$first_name, $last_name, $sex, $contact_number, $email, $pass_word]);
    if($last_insert_id){
        echo '<div id="successMsg">Account created successfully. <br /><br />Please note down User ID.'
        . '<br /><u>Candidate User ID:</u> '. $last_insert_id .''
                . '<br /><br /><a href="candidate-login.php" class="btn1">Login</a><br /><br /></div>';
    }
    }
}

// Fill input details if form is already submitted otherwise not.
echo '
<h1>Candidate - Sign Up Form</h1>
<main>
<form class="form1" method="POST" action="'. a_server('PHP_SELF') .'">
First Name:<br />
<input name="firstname" type="text" value="'. $first_name .'" /><br />
Last Name:<br />
<input name="lastname" type="text" value="'. $last_name .'" /><br />
<fieldset>
<input name="sex" type="radio" id="rMale" value="Male" '. ($sex=='Male'?'checked="checked"':'') .' />
<label for="rMale">Male</label>
<input name="sex" type="radio" id="rFemale" value="Female" '. ($sex=='Female'?'checked="checked"':'') .' />
<label for="rFemale">Female</label>
<input name="sex" type="radio" id="rOther" value="Other" '. ($sex=='Other'?'checked="checked"':'') .' />
<label for="rOther">Other</label>
</fieldset>
<br />
Contact Number:<br />
<input name="contactnumber" type="text" value="'. $contact_number .'" /><br />
Email:<br />
<input name="email" type="text" value="'. $email .'" /><br />
Password:<br />
<input name="password" type="password" /><br />
Confirm Password:<br />
<input name="confirmpassword" type="password" /><br />
<input type="submit" value="Sign Up" />
</form>
</main>';

require('components/footer.php');