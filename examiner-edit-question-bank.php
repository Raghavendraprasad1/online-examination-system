<?php 
require('components/header.php');
require('components/examiner-nav.php');

require('components/formhandler.php');
require('components/dbhandler.php');

$maxQuestions = 30;

$questions = '';
$i = 1;
$errorMsg = '';

// Get Question_Bank_ID from GET request.
if(a_server('REQUEST_METHOD') == 'GET'){
    // Fetch data into variables from GET method.
    $question_bank_id = a_get('id');
    if(empty($question_bank_id)){
        exit();
    }
}


// If POST request to save the questions.
if(a_server('REQUEST_METHOD') == 'POST'){
    // Get Question_Bank_ID
    $question_bank_id = a_post('qID');
    // First delete previous records
    a_exec('DELETE FROM questions WHERE Question_Bank_ID=?', [$question_bank_id]);
        
    // Now insert new records into database.
    $query = 'INSERT INTO questions(Question_Bank_ID, Question, Option1, Option2, Option3, Option4, Correct_Option) '
                . 'VALUES(?, ?, ?, ?, ?, ?, ?)';
    // Prepare SQL query
    $stmt = $conn->prepare($query);
        
    for($j=1; $j<=$maxQuestions; $j++){
    $question = a_post("question$j");
    $option1 = a_post("option1_$j");
    $option2 = a_post("option2_$j");
    $option3 = a_post("option3_$j");
    $option4 = a_post("option4_$j");
    $selection = a_post("selection$j");
    
    if (!empty($question) && !empty($option1) && !empty($option2) && !empty($option3) && !empty($option4) && !empty($selection)){
        // Only if question is in correct format.
        try{
         // Execute SQL query.
        $stmt->execute([$question_bank_id, $question, $option1, $option2, $option3, $option4, $selection]);
        echo '<div id="successMsg">Questions saved successfully.</div>';
        }catch(PDOException $e){
            // Print SQL query and error message in case of error.
            echo $query. "<br />". $e->getMessage();
        }
    }
    }
    
}

if(!empty($question_bank_id)){
    echo '
        <br /><br /><br />
        <h1>Examiner - Edit Question Bank</h1>
        <a href="'. a_server('PHP_SELF') .'?id='.$question_bank_id.'" id="reloadBtn">Reload</a>
        <table class="tbl1">
        <tr>
        <th>ID</th>
        <th>Question_Bank_Name</th>
        <th>Subject</th>
        <th>Description</th>
        <th>Created</th>
        </tr>
     ';
    
    $obj = a_query('SELECT ID, Question_Bank_Name, Subject, Description, '
            . 'Created FROM question_bank WHERE ID=?', [$question_bank_id]);
    while($row = $obj->fetch()){
           $rowId = $row['ID'];
           echo '<tr><td>'
                     .$rowId.'</td>'   
                   . '<td><span class="btn3">' .$row['Question_Bank_Name'].'</span></td>'
                   . '<td>'.$row['Subject'].'</td>'
                   . '<td>'.$row['Description'].'</td>'
                   . '<td>'.$row['Created'].'</td>'
                   . '</tr>';
    }
 
echo '</table><br /><br />';
        
// Fetch questions from database, if present
        echo '
        <main>
        <div>
        <b><u>Instructions:</u></b><br />
                <ul>
				<li><b>Save your session within 50 minutes.</b></li>
                <li>Maximum 30 questions allowed.</li>
                <li>No need to fill all questions.</li>
                <li>Reordering of questions is not required in case vacant inputs are left.</li>
				<li>Mandatory to fill all options of a questions. You may write NA for empty option.</li>
                </ul>
        <form class="form2" method="POST" action="'
        . a_server('PHP_SELF') . '?id=' .$question_bank_id
                .'">
        ';
        
        
            $obj = a_query('SELECT Question, Option1, Option2, Option3, Option4, Correct_Option FROM'
                . ' questions WHERE Question_Bank_ID=?', [$question_bank_id]);
            
            while ($row = $obj->fetch()){
                $correct_option = $row['Correct_Option'];
            echo '
            <h2>Question : '. $i .'</h2>    
            <br />
            <textarea name="question'. $i .'" placeholder="Question">'. $row['Question'] .'</textarea><br /><br />
            <input name="selection'. $i .'" type="radio" value="1" '. ($correct_option=='1'?'checked="checked"':'') .' />
            <input name="option1_'. $i .'" type="text" placeholder="Option 1" value="'. $row['Option1'] .'" /><br /><br />

            <input name="selection'. $i .'" type="radio" value="2" '. ($correct_option=='2'?'checked="checked"':'') .' />
            <input name="option2_'. $i .'" type="text" placeholder="Option 2" value="'. $row['Option2'] .'"  /><br /><br />

            <input name="selection'. $i .'" type="radio" value="3" '. ($correct_option=='3'?'checked="checked"':'') .' />
            <input name="option3_'. $i .'" type="text" placeholder="Option 3" value="'. $row['Option3'] .'"  /><br /><br />

            <input name="selection'. $i .'" type="radio" value="4" '. ($correct_option=='4'?'checked="checked"':'') .' />
            <input name="option4_'. $i .'" type="text" placeholder="Option 4" value="'. $row['Option4'] .'"  /><br /><br /><br /><br /><br /><br /><hr />
            ';
            $i++;
        }
        
        
        for ($i; $i<=$maxQuestions; $i++){
        echo '
        <h2>Question : '. $i .'</h2>    
        <br />
        <textarea name="question'. $i .'" placeholder="Question"></textarea><br /><br />
        <input name="selection'. $i .'" type="radio" value="1" />
        <input name="option1_'. $i .'" type="text" placeholder="Option 1" value="" /><br /><br />

        <input name="selection'. $i .'" type="radio" value="2" />
        <input name="option2_'. $i .'" type="text" placeholder="Option 2" value=""  /><br /><br />

        <input name="selection'. $i .'" type="radio" value="3" />
        <input name="option3_'. $i .'" type="text" placeholder="Option 3" value=""  /><br /><br />

        <input name="selection'. $i .'" type="radio" value="4" />
        <input name="option4_'. $i .'" type="text" placeholder="Option 4" value=""  /><br /><br /><br /><br /><br /><br /><hr />
        ';
    }

    echo '
    <input name="qID" type="hidden" value="'. $question_bank_id .'" />
    <input type="submit" value="Save" />
    </form>
    <br /><br /><br />
    </div>
    </main>
    ';
        

}



require('components/footer.php'); ?>

