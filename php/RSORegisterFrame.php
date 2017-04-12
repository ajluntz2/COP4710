<?php
require_once ('config.php');
require_once ('check_session.php');
require_once('utils.php');
require_once('rso.php');
require_once('university.php');
?>

<head>
<title>New Registered Student Organization (RSO)</title>
  <link href="../css/style.css" rel="stylesheet">
</head>

<body>

<h1>New Registered Student Organization (RSO)</h1>
      <div class = "container">
         <form class="form-register" action = "../php/registerRSO.php" method="post">

            <h5 style="display: inline;">Name:</h5>
            <input type="text" class="form-control" name = "name" placeholder="Underwater Basket Weaving Club" required autofocus></br>

            <?php $tags = gen_univeristy_options($_SESSION['userid']);
              if ($tags !== '') {
                echo "<select name=\"school\">";
                echo $tags;
                echo "</select>";
              }
            ?>

            <h5 style="display: block; padding:0px; padding-top: 16px;">Description:</h5>
            <textarea type="text" style="height:50%; width:100%;" name = "description"required><p>&nbsp;</p></textarea>

            <h5 class = "empty-space"></h5>

            <button class="register-button" type="submit" name="register">Register</button>
         </form>
</body>
</html>
