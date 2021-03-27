<?php
require('components/header.php');
require('components/examiner-nav.php');

require('components/formhandler.php');
require('components/dbhandler.php');

// Purpose pagination of records.
$offset = 0;
$rec_limit = 10;
$page = 1;
if(a_server('REQUEST_METHOD') == 'GET'){
    // Fetch data into variables from GET method.
    // Get page number of records - pagination.
    $pge = a_get('page');
    if(!empty($pge)){
        $page = $pge;
        $offset = ($page - 1) * $rec_limit;
    }
    
    // If request to delete a record
    $rec_del = a_get('del');
    if(!empty($rec_del)){
        // Database Operations - Delete a record from question_bank.
        $obj = a_exec('DELETE FROM exams WHERE ID=?', [$rec_del]);
        if($obj->rowCount() > 0){
            echo '<div id="successMsg">Exam deleted successfully.</div>';
        }
    }
    
    // If request to start an exam
    $rec_start = a_get('start');
    if(!empty($rec_start)){
        
        // Fetch exam duration
        $obj = a_query('SELECT Duration FROM exams WHERE ID=?', [$rec_start]);
        if($obj->rowCount()>0){
            $row = $obj->fetch();
            $duration = $row['Duration'];
        }else{
            echo '<div id="errorMsg">Unable to process your request. <br /></div>';
            exit();
        }

         // Prepare and Execute SQL query.
        $date = date('Y-m-d H:i:s');
        $end_time = date('Y-m-d H:i:s', strtotime('+'.$duration.' minutes', strtotime($date)));

        $result = a_exec('UPDATE exams SET Start_Time=IFNULL(Start_Time, ?),'
                . ' End_Time=IFNULL(End_Time, ?) WHERE ID=?', [$date, $end_time, $rec_start]);
        if($result){
            echo '<div id="successMsg">Exam started successfully. <br /></div>';
        }else{
            echo '<div id="errorMsg">Exam cannot be restarted. <br /></div>';
        }
    }
}

// Empty all strings
$exam_name = $exam_pass_word = $exam_duration = $additional_instructions = $question_bank_IDs
        = $max_questions = '';

// Sanitize and validate every input using filters.
if(a_server('REQUEST_METHOD') == 'POST'){
    // Fetch data into variables from POST method.
    $exam_name = a_post('examname');
    $exam_pass_word = a_post('exampassword');
    $exam_duration = a_post('examduration');
    $additional_instructions = a_post('additionalinstructions');
    $question_bank_IDs = a_post('questionbanks');
    $max_questions = a_post('maxquestions');
    
     // Check if any of above is empty and display appropriate errors.
    $errorMsg = '';
    if(empty($exam_name)){
        $errorMsg .= '* Exam Name not valid.<br />';
    }
    if(empty($exam_pass_word)){
        $errorMsg .= '* Exam Password not valid.<br />';
    }
    if(empty($exam_duration)){
        $errorMsg .= '* Exam Duration not valid.<br />';
    }
    if(empty($question_bank_IDs)){
        $errorMsg .= '* Question Bank IDs not valid.<br />';
    }
    if(empty($max_questions)){
        $errorMsg .= '* Maximum Questions not valid.<br />';
    }
    
    
    if(!empty($errorMsg)){
        // If any errors display them to the user.
        echo '<div id="errorMsg"><u>Errors: </u><br />'. $errorMsg . '</div>';
    }else{
        // Database Operations
        $result = a_exec('INSERT INTO exams(Exam_Name, Exam_Password, Duration, Additional_Instructions, '
                . 'Question_Bank_IDs, Max_Questions, Examiner_ID) '
                . 'VALUES(?, ?, ?, ?, ?, ?, ?)', [$exam_name, $exam_pass_word, $exam_duration, $additional_instructions,
            $question_bank_IDs, $max_questions, $id]);
        if($result){
            echo '<div id="successMsg">Exam created successfully.</div>';
        }
    }
}


echo '
<h1>Examiner - Exams</h1>
<a href="'. a_server('PHP_SELF') .'?page='.$page.'" id="reloadBtn">Reload</a>
<div id="content1">

<div id="tabs">
<ul>
<li><a href="#tabs-1">Active Exams</a></li>
<li><a href="#tabs-2">Manage Exams</a></li>
<li><a href="#tabs-3">Create an Exam</a></li>
</ul>

<div id="tabs-1">
<br /><br />
';

$date = date('Y-m-d H:i:s');

$obj = a_query('SELECT ID, Exam_Name, Exam_Password, Duration, Additional_Instructions, '
            . 'Question_Bank_IDs, Max_Questions, Start_Time, End_Time FROM exams WHERE Examiner_ID=?'
            . ' AND End_Time > ? ORDER BY ID DESC', [$id, $date]);
        if($obj->rowCount() > 0){
            echo '
                * Click on <b>Exam Name</b> for more details.<br /><br /><hr />
                <table class="tbl1">
                <tr>
                <th>ID</th>
                <th>Exam Name</th>
                <th>Exam Password</th>
                <th>Duration</th>
                <th>Additional Instructions</th>
                <th>Question Bank IDs</th>
                <th>Max Questions</th>
                <th>Start Time</th>
                <th>End Time</th>
                </tr>    
            ';
            while($row = $obj->fetch()){
                $rowId = $row['ID'];
                $duration = $row['Duration'];
                $start_time = $row['Start_Time'];
                $end_time = $row['End_Time'];
                echo '<tr ID="row'. $rowId .'">'
                          . '<td>'
                          .$rowId.'</td>'   
                        . '<td ID="rowD1'. $rowId .'">'
                        .'<span><a class="btn1" href="/examiner-active-exams.php?id='. $rowId .'" target="_blank">'
                        .$row['Exam_Name'].'</a></span></td>'
                        . '<td ID="rowD2'. $rowId .'">'.$row['Exam_Password'].'</td>'
                        . '<td ID="rowD3'. $rowId .'">'.$duration.'</td>'
                        . '<td ID="rowD4'. $rowId .'">'.$row['Additional_Instructions'].'</td>'
                        . '<td ID="rowD5'. $rowId .'">'.$row['Question_Bank_IDs'].'</td>'
                        . '<td ID="rowD6'. $rowId .'">'.$row['Max_Questions'].'</td>'
                        . '<td ID="rowD7'. $rowId .'">'.date('Y-M-d <\b\\r /><\b>h:i:s a</\b>', strtotime($start_time)).'</td>'
                        . '<td ID="rowD8'. $rowId .'">'.date('Y-M-d <\b\\r /><\b>h:i:s a</\b>', strtotime($end_time)).'</td>'
                        . '</tr>';
            }
            echo '</table>';
            echo '<hr />';
        }else{
            echo 'No Active Exams.';
        }

echo'
    </div>
  
<div id="tabs-2">
<a href="#" ID="btnDelete" class="btn1">Delete</a>
<a href="#" ID="btnStart" class="btn3">Start Now</a>
<br /><br />* Exams can only be started once and cannot be edited.<br />
* Deleting an exam will also loose reference to its Results.
<br />

<div id="dialogDelete">
<div>
</div>
<a ID="btnDel" class="btn1" href="#">Delete</a> 
<a ID="btnDelCancel" class="btn1" href="#">Cancel</a>
</div><br /><br />
';

$obj = a_query('SELECT ID, Exam_Name, Exam_Password, Duration, Additional_Instructions, '
                . 'Question_Bank_IDs, Max_Questions, Start_Time, End_Time FROM exams WHERE Examiner_ID=?'
        . " ORDER BY ID DESC LIMIT $offset, $rec_limit ", [$id]);
        if($obj->rowCount() > 0){
            echo '
                <table class="tbl1">
                <tr>
                <th>ID</th>
                <th>Exam Name</th>
                <th>Exam Password</th>
                <th>Duration</th>
                <th>Additional Instructions</th>
                <th>Question Bank IDs</th>
                <th>Max Questions</th>
                <th>Start Time</th>
                <th>End Time</th>
                </tr>    
            ';
            while($row = $obj->fetch()){
                $rowId = $row['ID'];
                $duration = $row['Duration'];
                $start_time = $row['Start_Time'];
                $end_time = $row['End_Time'];
                echo '<tr ID="row'. $rowId .'">'
                          . '<td><input type="radio" name="selection" value="'. $rowId .'" />'
                          .$rowId.'</td>'   
                        . '<td ID="rowC1'. $rowId .'">'
                        .'<span><a class="btn1" href="/examiner-active-exams.php?id='. $rowId .'" target="_blank">'
                        .$row['Exam_Name'].'</a></span></td>'
                        . '<td ID="rowC2'. $rowId .'">'.$row['Exam_Password'].'</td>'
                        . '<td ID="rowC3'. $rowId .'">'.$row['Duration'].'</td>'
                        . '<td ID="rowC4'. $rowId .'">'.$row['Additional_Instructions'].'</td>'
                        . '<td ID="rowC5'. $rowId .'">'.$row['Question_Bank_IDs'].'</td>'
                        . '<td ID="rowC6'. $rowId .'">'.$row['Max_Questions'].'</td>'
                        . '<td ID="rowC7'. $rowId .'">'
                        . (!empty($start_time)?(date('Y-M-d <\b\\r /><\b>h:i:s a</\b>', strtotime($start_time))):'')
                        .'</td>'
                        . '<td ID="rowD8'. $rowId .'">'
                        . (!empty($start_time)?(date('Y-M-d <\b\\r /><\b>h:i:s a</\b>', strtotime($end_time))):'')
                        .'</td>'
                        . '</tr>';
            }
            echo '</table>';
            
            $obj = a_query('Select COUNT(ID) AS Total FROM exams');
            $row = $obj->fetch();
            $total_records = $row['Total'];
            $total_pages = ceil($total_records / $rec_limit);
            echo '<hr />';
            for($i=1; $i<=$total_pages; $i++){
                 if ($i == $page){
                     echo '<span class="paging">' .
                     $i . '</span> ';
                 }else{
                     echo '<a class="paging1" href="'.a_server('PHP_SELF') .
                     '?page='. $i .'">'. $i . '</a> ';
                 }
            }
        }else{
            if($page > 1){
                // Goto previous page if table is not empty.
                $page--;
                // Release Connection - Before Refresh.
                $conn = null;
                header('refresh:0; url= '. 
                        a_server('PHP_SELF'). '?page=' .$page);
                exit();
            }else{
                // Means table is empty.
                echo 'No Exams created yet.';
            }
        }

echo'
    </div>
    
<div id="tabs-3">
<div>
<form class="form1" method="POST" action="'. a_server('PHP_SELF') .'">
Exam Name:<br />
<input name="examname" type="text" /><br />
Exam Password:<br />
<input name="exampassword" type="text" /><br />
Exam Duration in Minutes:<br />
<input name="examduration" type="number" /><br />
Additional Instructions:<br />
<textarea name="additionalinstructions" rows="10" cols="50"></textarea><br /><br />
Question Bank IDs (separeted by comma):<br />
<input name="questionbanks" type="text" /><br />
Maximum Questions:<br />
<input name="maxquestions" type="number" /><br />
<div></div>
<input type="submit" value="Create" />
</form>
</div>

</div>

</div>
</div>
';

require('components/footer.php');
?>

<script>
$(document).ready(function(){
    
// Separation of divisions into tabs
$( "#tabs" ).tabs();

    // Environment setup for record deletion
    $("#btnDelete").click(function(){
            var selection = $("input[name=selection]:checked").val();
            if(selection){
                var str = "Are you sure you want to delete ? <br /><br /><br />" + 
                                "<table class=tbl1><tr>" +
                                "<th>ID</th><th>Exam Name</th><th>Exam Password</th><th>Duration</th>" +
                                "<th>Additional Instructions</th><th>Question Bank IDs</th><th>Max Questions</th>"+
                                "<th>Start Time</th> " + 
                                "</tr><tr><td>" + 
                                selection + "</td><td>" +
                                $("#rowC1"+selection).html() + "</td><td>" +
                                $("#rowC2"+selection).html() + "</td><td>" +
                                $("#rowC3"+selection).html() + "</td><td>" +
                                $("#rowC4"+selection).html() + "</td><td>" +
                                $("#rowC5"+selection).html() + "</td><td>" +
                                $("#rowC6"+selection).html() + "</td><td>" +
                                $("#rowC7"+selection).html() + "</td></tr></table><br /><br />";
                $("#dialogDelete").children("div").html(str);
                
                $("#btnDel").attr("href", "<?php 
                echo filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING) . '?page=' .$page ?>&del=" + selection);
                        
                $("#dialogDelete").show();
                
            }
    });
    // Cancel Delete Operation
    $("#btnDelCancel").click(function(){
        $("#dialogDelete").hide();
    } );
  
  // Environment setup for starting an exam.
    $("#btnStart").click(function(){
            var selection = $("input[name=selection]:checked").val();
            if(selection){
                window.location.href ="<?php 
                echo filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING) . 
                        '?page=' .$page ?>&start=" + selection + "";
            }
    });

// *** Code to remember last tab that was opened.
    //  jQueryUI 1.10 and HTML5 ready
    //      http://jqueryui.com/upgrade-guide/1.10/#removed-cookie-option 
    //  Documentation
    //      http://api.jqueryui.com/tabs/#option-active
    //      http://api.jqueryui.com/tabs/#event-activate
    //      http://balaarjunan.wordpress.com/2010/11/10/html5-session-storage-key-things-to-consider/
    //
    //  Define friendly index name
    var index = "key";
    //  Define friendly data store name
    var dataStore = window.sessionStorage;
    //  Start magic!
    try {
        // getter: Fetch previous value
        var oldIndex = dataStore.getItem(index);
    } catch(e) {
        // getter: Always default to first tab in error state
        var oldIndex = 0;
    }
    $("#tabs").tabs({
        // The zero-based index of the panel that is active (open)
        active : oldIndex,
        // Triggered after a tab has been activated
        activate : function( event, ui ){
            //  Get future value
            var newIndex = ui.newTab.parent().children().index(ui.newTab);
            //  Set future value
            dataStore.setItem( index, newIndex ) 
        }
    });
    
    
    
});
</script>