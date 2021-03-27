<?php 
require('components/header.php');
require('components/candidate-nav.php');

echo '
<h1>Candidate Dashboard</h1>

<main>
<a href="candidate-edit-profile-details.php"><img class="thumbnail" src="design/editprofiledetails.png" width="120px" alt="Edit Profile Details" /></a>
<a href="candidate-new-exam.php"><img  class="thumbnail" src="design/newexam.png" width="120px" alt="New Exam" /></a>
    
<hr />
<a href="candidate-results.php"><img  class="thumbnail" src="design/results.png" width="120px" alt="Results" /></a>
<a href="candidates-instructions.php"><img  class="thumbnail" src="design/instructions.png" width="120px" alt="Instructions" /></a>
<a href="candidate-about.php"><img  class="thumbnail" src="design/about.png" width="120px" alt="About" /></a>
<hr />

</main>
';

require('components/footer.php');