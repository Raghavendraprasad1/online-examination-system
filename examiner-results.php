<?php 
require('components/header.php');
require('components/examiner-nav.php');
require('components/formhandler.php');
require('components/dbhandler.php');

$exam_id = '';
// Get exam_id from GET request
if (a_server('REQUEST_METHOD') == 'GET'){
    // Fetch data into variables from GET method.
    $exam_id = a_get('result');
    if (empty($exam_id)){
        echo '<div id="errorMsg"><br />No request recieved.<br /><br /></div>';
        exit();
    }
    
    // Check if this exam_id belongs to logged examiner or not.
    $obj = a_query('SELECT ID, Exam_Name, Duration, Additional_Instructions, Question_Bank_IDs, '
            . 'Max_Questions, Start_Time, End_Time, Candidates_Enrolled'
                . ' FROM exams WHERE ID=? AND Examiner_ID=?', [$exam_id, $id]);
            if ($obj->rowCount()>0){
                $row = $obj->fetch();
                // Store exam information for further use.
                $exam_name = $row['Exam_Name'];
                $duration = $row['Duration'];
                $additional_instructions = $row['Additional_Instructions'];
                $question_bank_ids = $row['Question_Bank_IDs'];
                $max_questions = $row['Max_Questions'];
                $start_time = $row['Start_Time'];
                $end_time = $row['End_Time'];
                $candidates_enrolled = $row['Candidates_Enrolled'];
            }else{
                echo '<div id="errorMsg">This exam information is no longer available.</div>';
                exit();
            }
}

// Exit if examination is still active.
if ($end_time > strtotime(date('Y-M-d H:i:s'))){
    echo '<div id="errorMsg"><br />'
    . 'Examination is still active.<br />Cannot generate results until examination ends.<br /><br /></div>';
}

// To check if result is already declared or not.
$declared = false;

echo '
    <h1>Results (<u>Exam ID: '. $exam_id .'</u>)</h1>
        <a href="'. a_server('PHP_SELF') .'?result='.$exam_id.'" id="reloadBtn">Reload</a>

    <div id="content2">';

echo '<table class="tbl1">'
        . '<tr>'
        . '<th>S.<br />No.</th>'
        . '<th>Candidate<br />ID</th>'
        . '<th>Exam<br />Roll<br />Number</th>'
        . '<th>Name</th>'
        . '<th>Sex</th>'
        . '<th>Contact<br />Number</th>'
        . '<th>Email</th>'
        . '<th>Correct<br />Answers</th>'
        . '<th>Max<br />Questions</th>'
        . '<th>Percentage</th>'
        . '</tr>';
// Display result if decalared and set declared to true, otherwise not.

    $obj = a_query('SELECT a.Correct_Answers, a.Max_Questions, a.Percentage,'
            . ' b.First_Name, b.Last_Name, b.Sex, b.Contact_Number, b.Email,'
            . ' c.Candidate_ID, c.Exam_Roll_Number'
            . ' FROM candidates_result AS a'
            . ' INNER JOIN candidate_info AS b ON a.Candidate_ID=b.ID'
            . ' INNER JOIN current_exams AS c ON a.Candidate_ID=c.Candidate_ID'
                . ' WHERE a.Exam_ID=? AND c.Exam_ID=? AND c.Suspended_Result=False'
            . ' ORDER BY a.Percentage DESC', [$exam_id, $exam_id]);
    if ($obj->rowCount()>0){
        $declared = true;
        $i = 0;
        while ($row = $obj->fetch()){
            $i++;
            echo '<tr>'
                    .'<td>'. $i . '</td>'
                    .'<td>'. $row['Candidate_ID'] . '</td>'
                    .'<td>'. $row['Exam_Roll_Number'] . '</td>'
                    .'<td>'. $row['First_Name'] . ' '. $row['Last_Name'] . '</td>'
                    .'<td>'. $row['Sex'] . '</td>'
                    .'<td>'. $row['Contact_Number'] . '</td>'
                    .'<td>'. $row['Email'] . '</td>'
                    .'<td>'. $row['Correct_Answers'] . '</td>'
                    .'<td>'. $row['Max_Questions'] . '</td>'
                    .'<td>'. $row['Percentage'] . '</td>'
                    .'</tr>';
        }
    }

echo '</table>';

// *** Generate result into candidates_result table.
if (!$declared){

    $obj = a_query('SELECT Candidate_ID'
                . ' FROM current_exams WHERE Exam_ID=?', [$exam_id]);
    if ($obj->rowCount()>0){
        while ($row = $obj->fetch()){
            $candidate_id = $row['Candidate_ID'];
            // *** Inner Loop for calculating result.
            $obj1 = a_query('SELECT ID FROM questions_answered WHERE Candidate_ID=? AND Exam_ID=? '
                    . 'AND Option_Choosen=Correct_Option', [$candidate_id, $exam_id]);
            
            $correct_answers = $obj1->rowCount();
            // Insert this information into candidates_result
            $percentage = ($correct_answers * 100) / $max_questions;
            
            $result = a_exec('INSERT INTO candidates_result'
                    . '(Exam_ID, Candidate_ID, Max_Questions, Correct_Answers, Percentage)'
                    . ' VALUES(?, ?, ?, ?, ?)', [$exam_id, $candidate_id, $max_questions, $correct_answers, $percentage]);
        }
    }else{
        echo '<div id="errorMsg">No Candidates enrolled in this examination.</div>';
        exit();
    }
}

?>


</div>
<?php require('components/footer.php'); ?>

