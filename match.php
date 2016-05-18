<!DOCTYPE html>
<?php
  include 'connectdb.php';
  if (!isset($_SESSION["username"])) 
    header('Location: home.php');
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
      <section class=matches>
        <h1>Potential Matches</h1><hr>
        <table>
          <?php
            if ($_SESSION['tech']) {
              $query = "select project.id, project.projname, project.username, description, project.username, projtype.id, hardware, gaming, website, app from project, projtype where project.projtype = projtype.id and projtype.projname != 'NULL' and project.projname = projtype.projname";
              $result = pg_query($query);
              
              while ($project = pg_fetch_array($result)) {
                if ((strcmp($project['hardware'], $_SESSION['hardware']) == 0) &&
                   (strcmp($project['gaming'], $_SESSION['gaming']) == 0) &&
                   (strcmp($project['website'], $_SESSION['website']) == 0) &&
                   (strcmp($project['app'], $_SESSION['app']) == 0)) {
                  echo "<h3>".$project['username']."</h3>";
                  echo "<h3>".$project['projname']."</h3>";
                  echo "<h4>".$project['description']."</h4>";
                  echo "<hr>";
                }
              }
            }
            else {
              $params = array($_SESSION['username']);
              $query = "select project.projname, project.description, hardware, gaming, website, app from project, projtype where project.projtype = projtype.id and project.username = $1";
              $result = pg_query_params($query, $params);

              $query = 'select * from technologist, projtype where skills = id';
              $techs = pg_query($query);
              while ($project = pg_fetch_array($result)) {
                for ($i = 0; $i < pg_num_rows($techs); $i++) {
                  if (strcmp($project['hardware'], pg_fetch_result($techs, 0, 'hardware')) == 0 &&
                     (strcmp($project['gaming'], pg_fetch_result($techs, 0, 'gaming')) == 0) &&
                     (strcmp($project['website'],pg_fetch_result($techs, 0, 'website')) == 0) &&
                     (strcmp($project['app'], pg_fetch_result($techs, 0, 'app')) == 0)) {
                    echo "<h3>".pg_fetch_result($techs, 0, 'username')."</h3>";
                    echo "<h3>".$project['projname']."</h3>";
                    echo "<h4>".$project['description']."</h4>";
                    echo "<hr>";
                  }
                }
              }
            }
          ?>
        </table>
      </section>
    </div>
  </body>
</html>