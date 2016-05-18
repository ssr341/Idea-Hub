<!DOCTYPE html>
<?php
  include './utility.php';
  if (!isset($_SESSION["user"])) 
    header('Location: ./index.php');
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
            <a href="home.php">Home</a>
          </td>
          <td class="profile">
              <a href='profile.php'>Profile</a>
          </td>
          <td class="friends">
            <a href='friends.php'>Friends</a>
          </td>
          <td class="settings">
            <a href='settings.php'>Settings</a>
          </td>
          <td class="search">
            <form action="search.php" method="GET">
              <input type="text" name="name" value="Search for users">
              <input type="submit" value="Search"> 
            </form>
          </td>
        </tbody>
      </table>
    </header>
    <br>
    <div class="content">
      <h1 class="content_title">Search Results for <?php echo $_GET["name"] ?></h1>
      <table class='search'>
        <tbody>
          <?php displayUsers($_GET["name"]); ?>
        </tbody>
      </table>
    </div>
  </body>
</html>