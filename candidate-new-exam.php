<?php 
require('components/header.php');
require('components/candidate-nav.php'); 

require('components/formhandler.php');
require('components/dbhandler.php');

$exam_id = $exam_pass_word = $exam_roll_number = '';

if(a_server('REQUEST_METHOD') == 'POST'){
    // Fetch data into variables from POST method.
    $exam_id = a_post('examid');
    $examiner_id = a_post('examinerid');
    $exam_pass_word = a_post('exampassword');
    $exam_roll_number = a_post('examrollnumber');
    
// Check if any of above is empty and display appropriate errors.
    $errorMsg = '';
    if(empty($exam_id)){
        $errorMsg .= '* Exam ID not valid.<br />';
    }
    if(empty($examiner_id)){
        $errorMsg .= '* Examiner ID not valid.<br />';
    }
    if(empty($exam_pass_word)){
        $errorMsg .= '* Exam Password not valid.<br />';
    }
    if(empty($exam_roll_number)){
        $errorMsg .= '* Exam Roll Number not valid.<br />';
    }
    if(!empty($errorMsg)){
        // If any errors display them to the user.
        echo '<div id="errorMsg"><u>Errors: </u><br />'. $errorMsg . '</div>';
    }else{
        $permit = False;    // Permission to appear in exam
        $interference = False;  // For determining interferece in exam window.
        
        // Check if there is already an entry in current_exams, if it is then set permit True.
        $query = 'SELECT ID FROM current_exams WHERE Exam_ID=? AND Examiner_ID=? AND Submitted=False';
        // Prepare and Execute SQL query.
        $stmt = $conn->prepare($query);
        $stmt->execute([$exam_id, $examiner_id]);
        if($stmt->rowCount()>0){
            $permit = True;
            $interference = True;
        }

        // Check if exam information is correct.
        $query = 'SELECT ID, Exam_Name, Additional_Instructions,'
                . ' Question_Bank_IDs, Max_Questions, Start_Time, Duration FROM exams WHERE ID=? AND Examiner_ID=? AND Exam_Password=?';
        // Prepare and Execute SQL query.
        $stmt = $conn->prepare($query);
        $stmt->execute([$exam_id, $examiner_id, $exam_pass_word]);
        // If exam information is correct
        if ($stmt->rowCount()>0){
            // *** Check if exam has expired or not
            $row = $stmt->fetch();
            $duration = $row['Duration'];
            $start_time = $row['Start_Time'];
            $exam_name = $row['Exam_Name'];
            $additional_instructions = $row['Additional_Instructions'];
            $question_bank_ids = $row['Question_Bank_IDs'];
            $max_questions = $row['Max_Questions'];

            $e_time = (!empty($start_time)?(date('Y-M-d H:i:s', strtotime('+'.$duration.' minutes', strtotime($start_time)))):'');
            // Check if exam has expired.
            if (strtotime($e_time) < strtotime(date('Y-M-d H:i:s'))){
                // Exam has been expired.
                echo '<div id="errorMsg">Exam has been expired.</div>';
                exit();
            }
            if (!$permit){
                // Insert a new entry into current_exams with provided information.
                $query = "INSERT INTO current_exams(Exam_ID, Examiner_ID, Candidate_ID, Exam_Roll_Number) "
                    . "VALUES(?, ?, ?, ?)";
                // Prepare and Execute SQL query.
                 $conn->prepare($query)->execute([$exam_id, $examiner_id, $id, $exam_roll_number]);
                 echo '<div id="successMsg">Login Successful.</div>';
                 // Update Candidates Enrolled in exams table
                 $query = "UPDATE exams SET Candidates_Enrolled=Candidates_Enrolled+1";
                 $conn->exec($query);
                 $permit = True;
            }
            if($permit){
                // Update Current_Exam in candidate_info (To ensure that candidate is appearing in one exam at a time.)
                a_exec('UPDATE candidate_info SET Current_Exam=? WHERE ID=?', [$exam_id, $id]);
                if($interference){
                    // Increment Interference in current_exams with Candidate_ID
                    a_exec('UPDATE current_exams SET Interference=Interference+1 WHERE Candidate_ID=?', [$id]);
                }
                // Redirect to the exam window setting important variables in SESSION.
                 $_SESSION['current_exam'] = $exam_id;
                 $_SESSION['exam_roll_number'] = $exam_roll_number;
                 $_SESSION['exam_name'] = $exam_name;
                 $_SESSION['additional_instructions'] = $additional_instructions;
                 $_SESSION['end_time'] = $e_time;
                 $_SESSION['question_bank_ids'] = $question_bank_ids;
                 $_SESSION['max_questions'] = $max_questions;
                 header('refresh:1; url= /candidate-intermediate.php');
                 exit();
            }else{
                // If exam information is not correct
                echo '<div id="errorMsg">Wrong information provided.</div>';
            }
        }else{
            // If exam information is not correct
            echo '<div id="errorMsg">Wrong information provided.</div>';
        }
                
    }
}


echo '
<h1>Candidate - New Exam</h1>
<main>
<form class="form1" method="POST" action="'. a_server('PHP_SELF') .'">
Exam ID:<br />
<input name="examid" type="text" /><br />
Examiner ID:<br />
<input name="examinerid" type="text" /><br />
Exam Password:<br />
<input name="exampassword" type="password" /><br />
Exam Roll Number:<br />
<input name="examrollnumber" type="text" /><br />
<input type="submit" value="Proceed" />
</form>
</main>
';

require('components/footer.php');
