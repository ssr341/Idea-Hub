<!DOCTYPE html>
<?php
  include 'connectdb.php';
  if (!isset($_SESSION["username"])) 
    header('Location: index.php');

  if ($_SESSION['tech'])
    header('Location: home.php');

  if (isset($_POST['name'])) {
    $hardware = isset($_POST['hardware']) ? 'true' : 'false';
    $gaming = isset($_POST['gaming']) ? 'true' : 'false';
    $website = isset($_POST['website']) ? 'true' : 'false';
    $app = isset($_POST['app']) ? 'true' : 'false';

    
    $params = array($_SESSION['username'],
                    $_POST['name'],
                    $hardware,
                    $gaming,
                    $website,
                    $app);
    $query = "insert into projtype values (DEFAULT, $1, $2, $3, $4, $5, $6)";
    $result = pg_query_params($query, $params);

    $params = array($_SESSION['username'], $_POST['name']);
    $query = 'select id from projtype where username = $1 and projname = $2';
    $result = pg_query_params($query, $params);
    $id = pg_fetch_result($result, 0, 0);

    $params = array($_POST['name'],
                    $_POST['description'],
                    $_SESSION['username'],
                    $id);
    $query = 'insert into project values (DEFAULT, $1, $2, now(), false, $3, $4)';
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
      <section class="project">
        <h1>Current Projects</h1>
          <?php
            $params = array($_SESSION['username']);
            $query = 'select * from project where username = $1';
            $result = pg_query_params($query, $params);

            while ($row = pg_fetch_array($result)) {
              echo '<h3>' . $row["projname"] . '</h3>';
              echo $row['description'];
            }
          ?>
        <h1>Create New Project</h1>
        <form action="project.php" method="POST" id='project'>
          <br>Project Name: <input type="text" name="name" required>
          <br><br><textarea rows="16" cols="50" name="description" form="project" maxlength="50000"></textarea>
          <br><br>Please select which skills you require for your project
          <br><input type="checkbox" name="hardware" value="yes"> Hardware
          <br><input type="checkbox" name="gaming" value="yes"> Gaming
          <br><input type="checkbox" name="website" value="yes"> Website
          <br><input type="checkbox" name="app" value="yes"> App
          <br><br><input type="submit" value="Submit"><br>
        </form>
      </section>
    </div>
  </body>
</html>