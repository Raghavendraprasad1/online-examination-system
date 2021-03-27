<?php require('components/header.php');

require('components/formhandler.php');
require('components/dbhandler.php');

session_start();
if(isset($_SESSION['candidate_id'])){
    header('refresh:0; url= /candidate-dashboard.php');
    exit();
}

$id = $pass_word = '';

// Sanitize and validate every input using filters.
if(a_server('REQUEST_METHOD') == 'POST'){
	
    // Fetch data into variables from POST method.
    $id = a_post('candidateid');
    $pass_word = a_post('candidatepassword');
    
    // Check if any of above is empty and display appropriate errors.
    $errorMsg = '';
    if(empty($id)){
        $errorMsg .= '* Candidate ID not valid.<br />';
    }
    if(empty($pass_word)){
        $errorMsg .= '* Candidate Password not valid.<br />';
    }
    
    if(!empty($errorMsg)){
        // If any errors display them to the user.
        echo '<div id="errorMsg"><u>Errors: </u><br />'. $errorMsg . '</div>';
    }else{
        $obj =a_query('SELECT ID, First_Name, Last_Name From candidate_info Where ID=? AND Pass_Word=?', [$id, $pass_word]);
        
        if($obj->rowCount() > 0){
            $row = $obj->fetch();
            echo '<div id="successMsg">Found</div>';
            $name = $row['First_Name'] . ' ' . $row['Last_Name'];
            $_SESSION['candidate_id'] = $id;
            $_SESSION['candidate_name'] = $name;
            header('refresh:0; url= /candidate-dashboard.php');
            exit();
        }else{
            echo '<div id="errorMsg">Authentication Failed.</div>';
        }
    }
}

echo '
<h1>Candidate - Login</h1>
<main>
<form class="form1" method="POST" action="'. a_server('PHP_SELF') .'">
Candidate ID:<br />
<input name="candidateid" type="text" /><br />
Candidate Password:<br />
<input name="candidatepassword" type="password" /><br />
<input type="submit" value="Log In" />
<br /><br /><hr />
<a href="candidate-signup.php" class="btn">Register</a>
</form>
</main>
';

require('components/footer.php'); ?>