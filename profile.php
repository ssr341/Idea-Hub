<!DOCTYPE html>
<?php
  include 'connectdb.php';
  if (!isset($_SESSION["username"])) 
    header('Location: index.php');
  if (isset($_POST['description'])) {
    $params = array($_POST['description'], $_SESSION['username']);
    $query = "update person set description = $1 where username = $2";
    $result = pg_query_params($query, $params);
  }
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
      <table class="messages">
        <tbody>
          <tr>
            <td>
              <h1> Update your description </h1>
              <form action="profile.php" method="POST" id="description">
                <textarea rows="8" cols="50" name="description" form="description" maxlength="10000"><?php
                    $query = "select description from person where username = $1";
                    $result = pg_query_params($query, array($_SESSION['username']));
                    $desc = pg_fetch_result($result, "description");
                    echo $desc;
                  ?></textarea>
                <br>
                <input type="submit" value="Update description">
              </form>
            </td>
          </tr>
          <tr>
            <td>
              <hr> 
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </body>
</html>