<?php
require('components/header.php');
require('components/candidate-nav.php');

require('components/formhandler.php');
require('components/dbhandler.php');

?>
<br />
<main>
    <br />
    <h1>Instructions for Candidates</h1>
    <hr />
    <ul>
 <?php
    echo '
        <li>
        <b>Exam Name: </b><br />'. $_SESSION['exam_name'].
        '<br /><br /><b>Instructions By Examiner: </b><br />'. $_SESSION['additional_instructions'].
        '<br /><br /><b>End Time: </b><br />'. date('Y-M-d <\b>h:i:s a</\b>', strtotime($_SESSION['end_time'])).
        '<br /><br /><b>Maximum Questions: </b><br />'. $_SESSION['max_questions']. '<hr /></li>
     ';
?>
    
        <li>Do not minimize exam window until exam time is over. <br />Such activity is reported to examiner automatically.</li>
        <li>You cannot submit examination without attempting all the questions before exam end time.</li>
    </ul>
    <hr />
    <br /><br />
    <center><a id="popup" class="btn2" href="/candidate-exam-window.php">Proceed to Exam Window</a></center>
    <br /><br />
</main>

<?php
require('components/footer.php');
?>

<script>
    $(document).ready(function() {
   $('#popup').click(function() {
     var NWin = window.open($(this).prop('href'), '', 'left=1, top=1, height=500,width=1000');
     if (window.focus)
     {
       NWin.focus();
     }
     return false;
     });
});

</script>