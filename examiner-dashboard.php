<?php 
require('components/header.php');
require('components/formhandler.php');
require('components/examiner-nav.php');

echo '
<h1>Examiner Dashboard</h1>
<a href="'. a_server('PHP_SELF') .'" id="reloadBtn">Reload</a>
<main>
    <a href="examiner-question-banks.php"><img  class="thumbnail" src="design/questionbanks.png" width="120px" alt="Question Banks" /></a>
    <a href="examiner-exams.php" target="_blank"><img  class="thumbnail" src="design/exams.png" width="120px" alt="Exams" /></a>
    <a href="examiner-overall-results.php"><img  class="thumbnail" src="design/results.png" width="120px" alt="Results" /></a>
<a href="examiner-instructions.php"><img  class="thumbnail" src="design/instructions.png" width="120px" alt="Instructions" /></a>
<a href="examiner-about.php"><img  class="thumbnail" src="design/about.png" width="120px" alt="About" /></a>
<hr />
    <a href="examiner-edit-profile-details.php"><img class="thumbnail" src="design/editprofiledetails.png" width="120px" alt="Edit Profile Details" /></a>
    <hr />
</main>
';

require('components/footer.php');