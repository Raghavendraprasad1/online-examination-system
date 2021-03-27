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
    if($rec_del){
        // Database Operations - Delete a record from question_bank.
        $result = a_exec('DELETE FROM question_bank WHERE ID=?', [$rec_del]);
        if($result){
            echo '<div id="successMsg">Question Bank deleted successfully.</div>';
        }
    }
}

// Empty all strings
$question_bank_name = $question_bank_subject = $question_bank_description = '';

// Sanitize and validate every input using filters.
if(a_server('REQUEST_METHOD') == 'POST'){
    // Fetch data into variables from POST method.
    $question_bank_name = a_post('questionbankname');
    $question_bank_subject = a_post('questionbanksubject');
    $question_bank_description = a_post('questionbankdescription');
    
     // Check if any of above is empty and display appropriate errors.
    $errorMsg = '';
    if(empty($question_bank_name)){
        $errorMsg .= '* Question Bank Name not valid.<br />';
    }
    if(empty($question_bank_subject)){
        $errorMsg .= '* Question Bank Subject not valid.<br />';
    }
    
    if(!empty($errorMsg)){
        // If any errors display them to the user.
        echo '<div id="errorMsg"><u>Errors: </u><br />'. $errorMsg . '</div>';
    }else{
        // Database Operations
        $result = a_exec('INSERT INTO question_bank(Question_Bank_Name, Subject, Description, Examiner_ID) '
                . 'VALUES(?, ?, ?, ?)', [$question_bank_name, $question_bank_subject, $question_bank_description, $id]);
        if($result){
            echo '<div id="successMsg">Question Bank created successfully.</div>';
        }
    }
}


echo '
<h1>Examiner - Question Banks</h1>
<a href="'. a_server('PHP_SELF') .'?page='.$page.'" id="reloadBtn">Reload</a>

<div id="content1">
<div id="tabs">
<ul>
<li><a href="#tabs-1">Question Banks List</a></li>
<li><a href="#tabs-2">Create a Question Bank</a></li>
</ul>
  
<div id="tabs-1">
<a href="#" ID="btnDelete" class="btn1">Delete</a>
<br />

<div id="dialogDelete">
<div>
</div>
<a ID="btnDel" class="btn1" href="#">Delete</a> 
<a ID="btnDelCancel" class="btn1" href="#">Cancel</a>
</div><br /><br />
';

$obj = a_query("SELECT ID, Question_Bank_Name, Subject, Description, Created From question_bank WHERE Examiner_ID=?"
        . " ORDER BY ID DESC LIMIT $offset, $rec_limit ", [$id]);
        if($obj->rowCount() > 0){
            echo '
                <table class="tbl1">
                <tr>
                <th>ID</th>
                <th>Question_Bank_Name</th>
                <th>Subject</th>
                <th>Description</th>
                <th>Created</th>
                </tr>
            ';
            while($row = $obj->fetch()){
                $rowId = $row['ID'];
                echo '<tr ID="row'. $rowId .'">'
                          . '<td><input type="radio" name="selection" value="'. $rowId .'" />'
                          .$rowId.'</td>'   
                        . '<td ID="rowQ'. $rowId .'">'
                        .'<a class="btn3" target="_blank" href="examiner-edit-question-bank.php?id='. $rowId .'">'
                        .$row['Question_Bank_Name'].'</a></td>'
                        . '<td ID="rowS'. $rowId .'">'.$row['Subject'].'</td>'
                        . '<td ID="rowD'. $rowId .'">'.$row['Description'].'</td>'
                        . '<td ID="rowC'. $rowId .'">'.date('Y-M-d <\b\\r /><\b>h:i:s a</\b>', strtotime($row['Created'])).'</td>'
                        . '</tr>';
            }
            echo '</table>';
            
            $obj1 = a_query('Select COUNT(ID) AS Total FROM question_bank');
            $row = $obj1->fetch();
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
                echo 'No question bank created yet.';
            }
        }

echo'
    </div>
    
<div id="tabs-2">
<div>
<form class="form1" method="POST" action="'. a_server('PHP_SELF') .'">
Question Bank Name:<br />
<input name="questionbankname" type="text" /><br />
Question Bank Subject:<br />
<input name="questionbanksubject" type="text" /><br />
Description (Only visible to you):<br />
<textarea name="questionbankdescription"></textarea><br />
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
                                "<th>ID</th><th>Question_Bank_Name</th><th>Subject</th><th>Description</th><th>Created</th>" + 
                                "</tr><tr><td>" + 
                                selection + "</td><td>" +
                                $("#rowQ"+selection).html() + "</td><td>" +
                                $("#rowS"+selection).html() + "</td><td>" +
                                $("#rowD"+selection).html() + "</td><td>" +
                                $("#rowC"+selection).html() + "</td></tr></table><br /><br />";
                $("#dialogDelete").children("div").html(str);
                
                $("#btnDel").attr("href", "<?php 
                echo a_server('PHP_SELF') . '?page=' .$page ?>&del=" + selection);
                        
                $("#dialogDelete").show();
                
            }
    });
    // Cancel Delete Operation
    $("#btnDelCancel").click(function(){
        $("#dialogDelete").hide();
    } );
  

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