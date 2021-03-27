<?php 
require('components/header.php');
require('components/examiner-nav.php');

require('components/formhandler.php');
require('components/dbhandler.php');

$id = $_SESSION['examiner_id'];

$obj = a_query('SELECT Institute, First_Name, Last_Name, Sex, Contact_number, '
                . 'Email, Country, City, Pass_Word FROM examiner_info WHERE ID=?', [$id]);
$row = $obj->fetch();

// Fill variables with database values.
$institute = $row['Institute'];
$first_name = $row['First_Name'];
$last_name = $row['Last_Name'];
$sex = $row['Sex'];
$contact_number = $row['Contact_number'];
$email = $row['Email'];
$country = $row['Country'];
$city = $row['City'];
$pass_word = $row['Pass_Word'];

        
if(a_server('REQUEST_METHOD') == 'POST'){
    // Fetch data into variables from POST method.
    $institute = a_post('institute');
    $first_name = a_post('firstname');
    $last_name = a_post('lastname');
    $sex = a_post('sex');
    $contact_number = a_post('contactnumber');
    $country = a_post('country');
    $city = a_post('city');
    $current_pass_word = a_post('currentpassword');
    $new_pass_word = a_post('newpassword');
    $confirm_pass_word = a_post('confirmpassword');
    
    // Check if any of above is empty and display appropriate errors.
    $errorMsg = '';
    if(empty($institute)){
        $errorMsg .= '* Institute name not valid.<br />';
    }
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
    // ***Removed Email
    if(empty($country)){
        $errorMsg .= '* Country name not valid.<br />';
    }
    if(empty($city)){
        $errorMsg .= '* City name not valid.<br />';
    }
    // If password is empty or not matched.
    if(empty($current_pass_word) || ($current_pass_word != $pass_word)){
        $errorMsg .= '* Password not valid.<br />';
    }
    if($new_pass_word != $confirm_pass_word){
        $errorMsg .= '* Passwords do not match.<br />';
    }
    
    if(!empty($errorMsg)){
        // If any errors display them to the user.
        echo '<div id="errorMsg"><u>Errors: </u><br />'. $errorMsg . '</div>';
    }else{
        // Database Operations
        //Update password only if required.
        if($new_pass_word != '' && $new_pass_word == $confirm_pass_word){
            $query = 'UPDATE examiner_info'
                    . ' SET Institute=?, First_Name=?, Last_Name=?, Sex=?, Contact_number=?, '
                . 'Country=?, City=?, Pass_Word=?'
                . ' WHERE ID=?';
            $par1 = [$institute, $first_name, $last_name, $sex, $contact_number, $country, $city, $new_pass_word, $id];
        }else{
            $query = 'UPDATE examiner_info'
                    . ' SET Institute=?, First_Name=?, Last_Name=?, Sex=?, Contact_number=?, '
                . 'Country=?, City=?'
                . ' WHERE ID=?';
            $par1 = [$institute, $first_name, $last_name, $sex, $contact_number, $country, $city, $id];
        }
        
         // Prepare and Execute SQL query.
        $result = a_exec($query, $par1);
        if($result){
            echo '<div id="successMsg">Account details updated successfully. <br /></div>';
        }
    }
}

echo '
<body>

<h1>Examiner - Profile Editor</h1>
<main>
<form class="form1" method="POST" action="'. a_server('PHP_SELF') .'">
Institute:<br />
<input name="institute" type="text" value="'. $institute .'" /><br />
First Name:<br />
<input name="firstname" type="text" value="'. $first_name .'" /><br />
Last Name:<br />
<input name="lastname" type="text" value="'. $last_name .'" /><br />
<fieldset>
<input name="sex" type="radio" id="rMale" value="Male"'. ($sex=='Male'?'checked="checked"':'') .' /><label for="rMale">Male</label>
<input name="sex" type="radio" id="rFemale" value="Female" '. ($sex=='Female'?'checked="checked"':'') .' /><label for="rFemale">Female</label>
<input name="sex" type="radio" id="rOther" value="Other" '. ($sex=='Other'?'checked="checked"':'') .' /><label for="rOther">Other</label>
</fieldset>
<br />
Contact Number:<br />
<input name="contactnumber" type="text" value='. "$contact_number" .' /><hr />
<span name="email" type="text">Email : '. $email .'</div><span /><hr />
Country:<br />
<input name="country" type="text" value="'. $country .'" /><br />
City:<br />
<input name="city" type="text" value="'. $city .'" /><br />
Password:<br />
<input name="currentpassword" type="password" /><br />
<br />
<fieldset>
<legend>* Fill only if you want to change the password</legend>
New Password:<br />
<input name="newpassword" type="password" /><br />
Confirm Password:<br />
<input name="confirmpassword" type="password" /><br />
</fieldset>
<input name="submit" type="submit" value="Update" />
</form>
</main>
';
        
require('components/footer.php');