<!DOCTYPE html>
<?php
  include 'connectdb.php';
  session_destroy();
  session_start();
  //if the user have entered both entries in the form, check if they exist in the database
  if(isset($_POST["username"]) && isset($_POST["password"])) {
    $params = array($_POST["username"], $_POST["password"]);

      //check if entry exists in database
    $query = "select username, first_name, last_name, password from person where username = $1 and password = $2";
    $result = pg_query_params($query, $params);

    if (pg_num_rows($result) == 1) {
      //if there is a match set session variables and send user to homepage
      $_SESSION["username"] = pg_fetch_result($result, 0, 0);
      $_SESSION["fname"] = pg_fetch_result($result, 0, 1);
      $_SESSION["lname"] = pg_fetch_result($result, 0, 2);
      $_SESSION["REMOTE_ADDR"] = $_SERVER["REMOTE_ADDR"]; //store clients IP address to help prevent session hijack
    }

    $query = "select technologist.username, skills, hardware, gaming, website, app from technologist, projtype where technologist.username = $1 and skills = id";
    $result = pg_query_params($query, array($_POST['username']));
    if (pg_num_rows($result) > 0) {
      $_SESSION['tech'] = true;
      $_SESSION['hardware'] = pg_fetch_result($result, 0, 'hardware');
      $_SESSION['gaming'] = pg_fetch_result($result, 0, 'gaming');
      $_SESSION['website'] = pg_fetch_result($result, 0, 'website');
      $_SESSION['app'] = pg_fetch_result($result, 0, 'app');
    }
    
    header('Location: home.php');
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
          <td>
          </td>
          <td>
          </td>
          <td>
          </td>
        </tbody>
      </table>
    </header>
    <br>
    <div class="content">
      <section class="login">
        <h1>Please log in</h1>
        <form action="index.php" method="POST">
          <input type="text" name="username" value="Username" required><br>
          <input type="password" name="password" value="Password" required><br>
          <input type="submit" value="Log in"><br>
        </form>
        <?php
          if (isset($_POST["username"]))
            echo "The username or password you entered is invalid.<br>";
          echo "New user? Sign up <a href='register.php'>here</a>!";
        ?>
      </section>
    </div>
  </body>
</html>
