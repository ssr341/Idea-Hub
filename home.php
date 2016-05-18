<!DOCTYPE html>
<?php
  include 'connectdb.php';
  if (!isset($_SESSION["username"])) 
    header('Location: index.php');
?>
<html>
  <head>
    <link rel="stylesheet" type="text/css" href="style.css">
  </head>
  <body>
    <header>
      <table class="heading">
        <tbody>
          <td class="logo">
            <a href="home.php"><img src="Idea-Hub Border.png"></a>
          </td>
          <td class="profile">
              <a href='profile.php'>Profile</a>
          </td>
          <td class="match">
            <a href='match.php'>Find Matches</a>
          </td>
          <td class="project">
            <?php 
              if (!$_SESSION['tech'])
                echo "<a href='project.php'>Create Project</a>";
            ?>
          </td>
          <td class="search">
          </td>
        </tbody>
      </table>
    </header>
    <br>
    <div class="content">
      <aside class="sidebar">
        <?php
            $user = $_SESSION["username"];
            $fname = $_SESSION["fname"];
            $lname = $_SESSION["lname"];
            echo "<br><br>Hello ".$fname." ".$lname."!<br>";
            echo "Not ".$fname."? Sign in or log out <a href='index.php'>here</a>!";
        ?>
      </aside>
      <div class="content_main">
        <h1 class="content_title">Your Matches</h1>
        <table>
          <tbody>
            <?php displayAllMessages(); ?>
          </tbody>
        </table>
      </div>
    </div>
  </body>
</html>