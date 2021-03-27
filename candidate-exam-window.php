<?php
require('components/header.php');
require('components/candidate-nav.php');

require('components/formhandler.php');
require('components/dbhandler.php');

?>
<br />
<main>
    
<?php
// Check if user has arrived through correct procedures.
if (!isset($_SESSION['current_exam'])){
    echo '<div id="errorMsg">No request recieved. Please return to homepage.</div>';
    exit();
}

$exam_id = $_SESSION['current_exam'];
$max_questions = $_SESSION['max_questions'];

// Check if examination has been expired.
$end_time = strtotime($_SESSION['end_time']);
if ($end_time < strtotime(date('Y-M-d H:i:s'))){
    echo '<div id="errorMsg">Exam has been expired.</div>';
    exit();
}

// Calculate remaining time of examination end.
$r_time = $end_time - strtotime(date('Y-M-d H:i:s'));
$r_mins = floor($r_time / 60);
$r_seconds = floor($r_time % 60);
$remaining_time = $r_mins . ':' . $r_seconds;

// Convert session Question Bank IDs string into array.
if (isset($_SESSION['question_bank_ids'])){
    $qbi = str_replace(' ', '', $_SESSION['question_bank_ids']);
    $question_bank_IDs = explode(',', $qbi);
}

// If wrong format of Question Bank IDs
if (!isset($question_bank_IDs)){
    echo '<div id="errorMsg">Error - Wrong format of <b><u>Question Bank IDs</u></b> by examiner.<br /><br />'
    . 'Kindly contact Examiner for resolution.</div>';
    exit();
}

// *** Check if Candidate is allowed or not for the examination.

$obj = a_query('SELECT Suspended_Result From current_exams WHERE Exam_ID=? AND Candidate_ID=?', [$exam_id, $id]);
 
if($obj->rowCount() > 0){
    $row = $obj->fetch();
    if ($row['Suspended_Result'] == True){
        echo '<div id="errorMsg"><br />'
        . 'You are not allowed to appear in examination.<br />Kindly contact Examiner.<br /><br /></div>';
        exit();
    }
}else{
    echo '<div id="errorMsg">Error in processing your request.</div>';
    exit();
}


// *** For the first time of SESSION or error - Set a random SESSION question id.
if (!isset($_SESSION['question_id'])){
    
    
    // Pick random Question Bank ID from array.
    $question_bank_ID_Index = array_rand($question_bank_IDs, 1);
        
    $obj = a_query('SELECT ID From questions WHERE Question_Bank_ID=? ORDER BY RAND() LIMIT 1', [$question_bank_IDs[$question_bank_ID_Index]]);
     
    if($obj->rowCount() > 0){
        $row = $obj->fetch();
        $question_id = $row['ID'];
        $_SESSION['question_id'] = $question_id;
    }else{
        echo '<div id="errorMsg">Refresh by pressing F5 key.</div>';
        // Prevent from error if there is no question in a question bank
        unset($_SESSION['question_id']);
        exit();
    }
    
}else{
    // *** If it is not first time.
    $question_id = $_SESSION['question_id'];
}

$questions_answered = 0;
// *** If POST request to save answer.
if (a_server('REQUEST_METHOD') == 'POST'){
    //Interference Finding
    $interf = a_post('rec');
    if($interf == 'rec'){
        // Interference is there
        a_exec('UPDATE current_exams SET Interference=Interference+1 '
                 . 'WHERE Exam_ID=? AND Candidate_ID=?', [$exam_id, $id]);
    }
    // Fetch data into variables from POST method.
    $qid = a_post('questionid');
    // Only valid if qid matches with question id from session - to validate single request.
    if ($qid == $question_id){
        $correct_option = $_SESSION['Correct_Option'];
        $selection = a_post('selection');
        // Save answer in questions_answered table
        // Pick random Question Bank ID from array.
        $question_bank_ID_Index = array_rand($question_bank_IDs, 1);
        
        a_exec('INSERT INTO questions_answered(Exam_ID, Candidate_ID, Question_ID, Option_Choosen, Correct_Option) '
                            . 'VALUES(?, ?, ?, ?, ?)', [$exam_id, $id, $qid, $selection, $correct_option]);
        
         // Increment Questions_Answered in current_exams table.
         a_exec('UPDATE current_exams SET Questions_Answered=Questions_Answered+1 '
                 . 'WHERE Exam_ID=? AND Candidate_ID=?', [$exam_id, $id]);
         
         // Generate random question id
         $obj = a_query('SELECT ID From questions WHERE Question_Bank_ID=? ORDER BY RAND() LIMIT 1', [$question_bank_IDs[$question_bank_ID_Index]]);
         
        if($obj->rowCount() > 0){
            $row = $obj->fetch();
            $question_id = $row['ID'];
            $_SESSION['question_id'] = $question_id;
        }else{
            echo '<div id="errorMsg">Refresh by pressing F5 key.</div>';
            // Prevent from error if there is no question in a question bank
            unset($_SESSION['question_id']);
            exit();
        }
    }
}else{
    // Count as interference
        a_exec('UPDATE current_exams SET Interference=Interference+1 '
                 . 'WHERE Exam_ID=? AND Candidate_ID=?', [$exam_id, $id]);
}


// *** Check if Questions_Answered is less than Max_Questions or not.
    $obj = a_query('SELECT Questions_Answered From current_exams WHERE Exam_ID=? AND Candidate_ID=?', [$exam_id, $id]);

    if ($obj->rowCount() > 0){
        $row = $obj->fetch();
        $questions_answered = $row['Questions_Answered']; 
        if ($questions_answered >= $max_questions){
            echo '<div id="successMsg"><br />You have answered maximum questions.<br />Your exam is complete.<br /><br /></div>';
            // Set Submitted to True.
            a_exec('UPDATE current_exams SET Submitted=True WHERE Exam_ID=? AND Candidate_ID=?', [$exam_id, $id]);
            exit();
        }
    }else{
        echo '<div id="errorMsg">Error in processing your request.</div>';
        exit();
    }

// Show question with question_id
$obj = a_query('SELECT ID, Question, Option1, Option2, Option3, Option4, Correct_Option'
        . ' From questions WHERE ID=?', [$question_id]);
if($obj->rowCount() > 0){
    $row = $obj->fetch();
    echo ''
            . '<div><span style="float:left;"><b>Questions Answered: </b></span>'. $questions_answered . ' of ' . $max_questions .''
            .'</b><span style="float:right;"><span id="timer" style="font-size:1.5em;"><b>'.$r_mins.':'.$r_seconds.'</b></span></span></div><br />
        <form class="form2" method="POST" action="'. a_server('PHP_SELF') .'">
            ';
    $option1 = '<input name="selection" id="sel1" type="radio" value="1" required="required" /><label for="sel1">' . $row['Option1'] .'</label>';
    $option2 = '<input name="selection" id="sel2" type="radio" value="2" required="required" /><label for="sel2">' . $row['Option2'] .'</label>';
    $option3 = '<input name="selection" id="sel3" type="radio" value="3" required="required" /><label for="sel3">' . $row['Option3'] .'</label>';
    $option4 = '<input name="selection" id="sel4" type="radio" value="4" required="required" /><label for="sel4">' . $row['Option4'] .'</label>';
    // Store options in an array.
    $options = [$option1, $option2, $option3, $option4];
    // Shuffle options array.
    shuffle($options);
    echo $row['Question']. '<br /><br /><fieldset>'
            .$options[0]. '<br /><br />'
            .$options[1]. '<br /><br />'
            .$options[2]. '<br /><br />'
            .$options[3]. '<br /></fieldset>'
            . '<input name="questionid" type="hidden" value="'. $row['ID'] .'" />'
            . '<input name="rec" id="rec" type="hidden" value="gig" />' // To record Interference
            . '<br /><input type="Submit" value="Submit" /></form>'
            . '<span style="float:left;">Online Examination Window (<b>' . $_SESSION['exam_name'] . '</b>)</span>'
            . '<span style="float:right;">End Time: <br /><b>'. date('Y-M-d <\b\\r />h:i:s a', $end_time) .'</b></span><br /><br />';
    $correct_option = $row['Correct_Option']; 
    $_SESSION['Correct_Option'] = $correct_option;
}else{
    echo '<div id="errorMsg">Refresh by pressing F5 key.</div>';
    // Prevent from error if there is no question in a question bank
        unset($_SESSION['question_id']);
    exit();
}





?>

</main>

<script>
  $( function() {
     
      $(window).focus(function() {
        $( "#rec" ).val("rec");
    });
      
    $( "input" ).checkboxradio();
    
  } );
  
// *** Timer Javascript
  var minutes = <?php echo $r_mins ?>;
  var seconds = <?php echo $r_seconds ?>;
  
// Update the count down every 1 second
var x = setInterval(function() {
    
  seconds = seconds - 1;
  if(seconds < 0){
      seconds = 59;
      minutes = minutes - 1;
  }
  
  var sec = "" + seconds;
  if(seconds<10){
      sec = "0" + seconds;
  }

  // Display the result in the element with id="demo"
  document.getElementById("timer").innerHTML = "<b>" + minutes + ":" + sec + "</b>";

  // If the count down is finished, write some text 
  if (minutes < 0) {
    clearInterval(x);
    document.getElementById("timer").innerHTML = "EXPIRED";
  }
}, 1000);
  </script>