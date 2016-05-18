<!DOCTYPE html>
<?php
  include 'connectdb.php';
  $unique = true;
  if (isset($_POST["username"], $_POST["password"], $_POST["fname"], $_POST["lname"])) {
    $params = array($_POST["username"]);

      //check if entry exists in database
    $query = "select username from person where username = $1";
    $result = pg_query_params($query, $params);
    if (pg_num_rows($result) != 0) $unique = false;
    else {
      $params = array($_POST['username'],
                      $_POST['password'],
                      $_POST['fname'],
                      $_POST['lname'],
                      $_POST['description'],
                      $_POST['email']);
      $query = "insert into person (username, password, first_name, last_name, description, email) values ($1, $2, $3, $4, $5, $6)";
      $result = pg_query_params($query, $params);

      if (strcmp($_POST['tech'], "yes") == 0) {
        $hardware = isset($_POST['hardware']) ? 'true' : 'false';
        $gaming = isset($_POST['gaming']) ? 'true' : 'false';
        $website = isset($_POST['website']) ? 'true' : 'false';
        $app = isset($_POST['app']) ? 'true' : 'false';

        $params = array($_POST['username'], $hardware, $gaming, $website, $app);
        $query = "insert into projtype values (DEFAULT, $1, 'NULL', $2, $3, $4, $5)";
        $result = pg_query_params($query, $params);

        $query = "select id from projtype where username = $1";
        $result = pg_query_params($query, array($_POST['username']));

        $params = array($_POST['username'], pg_fetch_result($result, 0, 0));
        $query = "insert into technologist values ($1, $2)";
        $result = pg_query_params($query, $params);

        $_SESSION["tech"] = true;
        $_SESSION['hardware'] = isset($_POST['hardware']) ? 't' : 'f';
        $_SESSION['gaming'] = isset($_POST['gaming']) ?  't' : 'f';
        $_SESSION['website'] = isset($_POST['website']) ?  't' : 'f';
        $_SESSION['app'] = isset($_POST['app']) ?  't' : 'f';
      }

      $_SESSION['username'] = $_POST['username'];
      $_SESSION['fname'] = $_POST['fname'];
      $_SESSION['lname'] = $_POST['lname'];
      header('Location: home.php');
    }
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
          </td>
          <td class="createProject">
          </td>
          <td class="findMatches">
          </td>
          <td class="search">
          </td>
        </tbody>
      </table>
    </header>
    <br>
    <div class="content">
      <section class="register">
        <h1>Registration</h1>
        <?php 
          if (!$unique) echo "<br>This username has already been taken";
        ?>
        <form action="register.php" method="POST" id="register">
          <br>First Name: <input type="text" name="fname" required> 
          <br>Last Name: <input type="text" name="lname" required>
          <br>Username: <input type="text" name="username" required>
          <br>Password: <input type="text" name="password" required>
          <br>Email: <input type="text" name="email" required>
          <br><br>Description<br>
          <textarea rows="8" cols="50" name="description" form="register" maxlength="10000"></textarea>

          <br><br>Technologist?<br>
          <input type="radio" name="tech" value="yes"> Yes
          <input type="radio" name="tech" value="no" checked="checked"> No

          <br><br>Please select which skills you have if you are a technologist
          <br><input type="checkbox" name="hardware" value="yes"> Hardware
          <br><input type="checkbox" name="gaming" value="yes"> Gaming
          <br><input type="checkbox" name="website" value="yes"> Website
          <br><input type="checkbox" name="app" value="yes"> App

          <br><br><input type="submit" value="Submit"><br><br><br><br><br>
        </form>
      </section>
    </div>
  </body>
</html>
