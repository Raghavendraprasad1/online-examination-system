<?php
$conn = null;
?>

<footer>
    Online Examination Project
</footer>
</body>
</html>

<script>
$(document).ready(function(){
    
// Success Message Dialog
$( "#successMsg" ).dialog({
      width: 500,
      modal: true,
      buttons: {
        Ok: function() {
          $( this ).dialog( "close" );
        }
      }
    });

// Error Message Dialog
$( "#errorMsg" ).dialog({
      width: 500,
      modal: true,
      buttons: {
        Ok: function() {
          $( this ).dialog( "close" );
        }
      }
    });
});
 </script>