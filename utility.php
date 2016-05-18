<?php
  session_start();

  //Writes $request to $fp, adding header to front of $request
  function writeData($fp, $request){
    $header = (string)strlen($request);
    while (strlen($header) != 8) {
      $header .= "-";
    }
    $message = $header.$request;
    fwrite($fp, $message);
  }

  //Extracts header, then reads specified amount of bytes 
  function readData($fp){
    $reply = "";
    $header = "";
    for ($i = 0; $i < 8; ++$i) {
      $tmp = fgetc($fp);
      if ($fp != '-')
        $header .= $tmp;
    }
    for ($i = 0; $i < (int)$header; ++$i) {
      $reply .= fgetc($fp);
    }
    return $reply;
  }

  //Array of all RM IP addresses
  function getRMIP(){
    return array("localhost", "localhost");
  }

  //Array of all RM port numbers
  function getRMPort(){
    return array("13002", "13003");
  }

  //connect to backend server
  function connectToBackEndServer($request, $sync){
    $replicaManagersIP = getRMIP();
    $replicaManagersPort = getRMPort();
    $reply = "";
    //Send to all RMs until a replyl is received
    //Only primary RM allowed to send reply, so any reply with length > 0 is from primary RM
    for ($i = 0; $i < count($replicaManagersIP); ++$i) {
      $fp = @stream_socket_client("tcp://".$replicaManagersIP[$i].":".$replicaManagersPort[$i], $errno, $errstr, 5);
      if ($fp) {
        writeData($fp, $request);

        if ($sync) {
          $reply = readData($fp);
          if (strlen($reply) > 0)
            break;
        }
        fclose($fp);
      }
    }

    return $reply;
  }

  //Access saved data
  function getPass($user){
    return connectToBackEndServer("getPass $user", TRUE);
  }

  function getFName($user){
    return connectToBackEndServer("getFName $user", TRUE);
  }

  function getLName($user){
    return connectToBackEndServer("getLName $user",TRUE);
  }

  //Login
  function verify($user, $pass) {
    $reply = connectToBackEndServer("verify $user $pass", TRUE);
    $reply = explode(" ", $reply);
    if (count($reply) != 4) return FALSE;

    $_SESSION["user"] = $reply[0];
    $_SESSION["pass"] = $reply[1];
    $_SESSION["fname"] = $reply[2];
    $_SESSION["lname"] = $reply[3];
    return TRUE;
  }

  //Register Functions
  function addUser($user, $pass, $fname, $lname){
    connectToBackEndServer("addUser $user $pass $fname $lname", FALSE);
  }

  function isUnique($user){
    return connectToBackEndServer("isUnique $user", TRUE) == "1" ? TRUE : FALSE;
  }

  function isValid($input){
    return preg_match('/\s/', $input) == 0;
  }

  function isValidAccount($user, $pass, $fname, $lname){
    return isValid($user) && isValid($pass) && isValid($fname) && isValid($lname);
  }

  //Settings functions
  function updateUser($user, $pass, $fname, $lname){
    connectToBackEndServer("updateUser $user $pass $fname $lname", FALSE);
  }

  function deleteAccount($user){
    connectToBackEndServer("deleteAccount $user", FALSE);
  }

  //Message functions
  function saveMessage($message, $privacy){
    connectToBackEndServer("saveMessage ".$_SESSION["user"]." $privacy $message", FALSE);
  }

  //Queries all known RMs for messages from $user
  function displayUserMessages($user){
    $fp = 0;
    $replicaManagersIP = getRMIP();
    $replicaManagersPort = getRMPort();
    for ($i = 0; $i < count($replicaManagersIP); ++$i) {
      $fp = @stream_socket_client("tcp://".$replicaManagersIP[$i].":".$replicaManagersPort[$i], $errno, $errstr, 5);
      if ($fp) {
        writeData($fp, "displayUserMessages $user");
      }
    }

    $count = readData($fp);
    while ($count > 0) {
      $message = readData($fp);
      echo "<tr>";
        echo "<table class='message'>";
          echo "<tr>";
            echo "<td>";
              echo $_SESSION["fname"]." ".$_SESSION["lname"]." (".$_SESSION["user"]."):";
            echo "</td>";
          echo "</tr>";
          echo "<tr>";
            echo "<td>";
              echo "$message";
            echo "</td>";
          echo "</tr>";
        echo "</table>";
      echo "</tr>";
      $count -= 1;
    }
    fclose($fp);
  } 

  //Queries all known RMs for public messages from $user
  function displayPublicMessages($user){
    $fp = 0;
    $replicaManagersIP = getRMIP();
    $replicaManagersPort = getRMPort();
    for ($i = 0; $i < count($replicaManagersIP); ++$i) {
      $fp = @stream_socket_client("tcp://".$replicaManagersIP[$i].":".$replicaManagersPort[$i], $errno, $errstr, 5);
      if ($fp) {
        writeData($fp, "displayPublicMessages $user");
      }
    }

    $fname = getFName($user);
    $lname = getLName($user);

    $count = readData($fp);
    while ($count > 0) {
      $message = readData($fp);
      echo "<tr>";
        echo "<table class='message'>";
          echo "<tr>";
            echo "<td>";
              echo $fname." ".$lname." (".$user."):";
            echo "</td>";
          echo "</tr>";
          echo "<tr>";
            echo "<td>";
              echo "$message";
            echo "</td>";
          echo "</tr>";
        echo "</table>";
      echo "</tr>";
      $count -= 1;    
    }
    fclose($fp);
  }

  //Queries all RMs for all public messages
  function displayAllMessages(){
    $fp = 0;
    $replicaManagersIP = getRMIP();
    $replicaManagersPort = getRMPort();
    for ($i = 0; $i < count($replicaManagersIP); ++$i) {
      $fp = @stream_socket_client("tcp://".$replicaManagersIP[$i].":".$replicaManagersPort[$i], $errno, $errstr, 5);
      if ($fp) {
        writeData($fp, "displayAllMessages");
      }
    }

    $count = readData($fp);
    while ($count > 0) {
      $user = readData($fp);
      $user = explode(" ", $user);
      $message = readData($fp);
      echo "<tr>";
        echo "<table class='message'>";
          echo "<tr>";
            echo "<td>";
              echo "<form action='profile.php' method='GET'>";
                echo "<input type='hidden' name='name' value='".$user[0]."'>";
                echo "<input type='submit' class='button' value='".$user[1]." ".$user[2]." (".$user[0]."):'>";
              echo "</form>";
            echo "</td>";
          echo "</tr>";
          echo "<tr>";
            echo "<td>";
              echo "$message";
            echo "</td>";
          echo "</tr>";
        echo "</table>";
      echo "</tr>";
      $count -= 1;
    }
    fclose($fp);
  } 

  //Friend functions
  function displayFriendRequests($fp, $user){
    $reply = readData($fp);
    $reply = explode(" ", $reply);
    $count = count($reply);

    if ($count == 1) {
      echo "<tr>";
        echo "<td>";
          echo "You currently have no pending requests";
        echo "</td>";
      echo "</tr>";
    }
    else {
      for ($i = 1; $i < $count; ++$i) { 
        echo "<tr>";
          echo "<td class='name'>";
          echo "<a href='profile.php?name=".$reply[$i]."'>".getFName($reply[$i])." ".getLName($reply[$i])." (".$reply[$i].")</a>";
          echo "</td>";
        echo "</tr>";
        echo "<tr>";
          echo "<td class='status'>";
            echo "<form action='friends.php' method='POST'>";
              echo "<input type='hidden' name='cancel' value=$reply[$i]>";
              echo "<input type='submit' value='Cancel Request'>";
            echo "</form>";
          echo "</td>";
        echo "</tr>";
      }
    }
  }

  function displayPendingRequests($fp, $user){
    $reply = readData($fp);
    $reply = explode(" ", $reply);
    $count = count($reply);

    if ($count == 1) {
      echo "<tr>";
        echo "<td>";
          echo "You currently have no friend requests";
        echo "</td>";
      echo "</tr>";
    }
    else {
      for ($i = 0; $i < $count - 1; ++$i) {
        echo "<tr>";
          echo "<td class='name'>";
          echo "<a href='profile.php?name=".$reply[$i]."'>".getFName($reply[$i])." ".getLName($reply[$i])." (".$reply[$i].")</a>";
          echo "</td>";
        echo "</tr>";
        echo "<tr>";
          echo "<td class='status'>";
            echo "<form action='friends.php' method='POST'>";
              echo "<input type='hidden' name='requester' value=$reply[$i]>";
              echo "<input type='radio' name='choice' value='1'>Accept";
              echo "<input type='radio' name='choice' value='0'>Reject<br>";
              echo "<input type='Submit' value='Submit'>";
            echo "</form>";
          echo "</td>";
        echo "</tr>";
      }
    }
  }

  function displayFriends($fp, $user){
    $reply = readData($fp);
    $reply = explode(" ", $reply);
    $count = count($reply);

    if ($count == 1) {
      echo "<tr>";
        echo "<td>";
          echo "You currently have no friends";
        echo "</td>";
      echo "</tr>";
    }
    else {
      for ($i = 1; $i < $count; ++$i) { 
        echo "<tr>";
          echo "<td class='name'>";
            echo "<form action='profile.php' method='GET'>";
              echo "<input type='hidden' name='name' value='".$reply[$i]."'>";
              echo "<input type='submit' class='button' value='".getFName($reply[$i])." ".getLName($reply[$i])." (".$reply[$i].")'>";
            echo "</form>";
          echo "</td>";
        echo "</tr>";
        echo "<tr>";
          echo "<td class='status'>";
            echo "<form action='friends.php' method='POST'>";
              echo "<input type='hidden' name='deleteFriend' value='".$reply[$i]."'>";
              echo "<input type='Submit' value='Delete Friend'>";
            echo "</form>";
          echo "</td>";
        echo "</tr>";
      }
    }
  }

  function displayFollowers($fp, $user){
    $count = readData($fp);

    if ($count == 0) {
      echo "<tr>";
        echo "<td>";
          echo "You currently have no followers";
        echo "</td>";
      echo "</tr>";
    }
    else {
      for ($i = 0; $i < $count; ++$i) { 
        $follower = readData($fp);
        $follower = trim($follower);
        $follower = explode(" ", $follower);
        echo "<tr>";
          echo "<td class='name'>";
            echo "<form action='profile.php' method='GET'>";
              echo "<input type='hidden' name='name' value='".$follower[0]."'>";
              echo "<input type='submit' class='button' value='".$follower[1]." ".$follower[2]." (".$follower[0].")'>";
            echo "</form>";
          echo "</td>";
        echo "</tr>";
        echo "<tr>";
          echo "<td class='status'>";
            echo "<form action='friends.php' method='POST'>";
              echo "<input type='hidden' name='deleteFollower' value='".$follower[0]."'>";
              echo "<input type='Submit' value='Delete Follower'>";
            echo "</form>";
          echo "</td>";
        echo "</tr>";
      }
    }
  }

  //Combines above four functions into one function
  //Queries all known RMs for all friend requests and friend info
  function displayFriendPage($user){
    $fp = 0;
    $replicaManagersIP = getRMIP();
    $replicaManagersPort = getRMPort();
    for ($i = 0; $i < count($replicaManagersIP); ++$i) {
      $fp = @stream_socket_client("tcp://".$replicaManagersIP[$i].":".$replicaManagersPort[$i], $errno, $errstr, 5);
      if ($fp) {
        writeData($fp, "displayFriendPage $user");
      }
    }

    echo "
    <table class='friends'>
        <tbody>
          <tr>
            <td>
              <h1 class='content_title'>Friend Requests</h1>
            </td>
          </tr>";
          displayFriendRequests($fp, $_SESSION['user']);
    echo "
        </tbody>
      </table>
      <table class='friends'>
        <tbody>
          <tr>
            <td>
            <hr>
              <h1 class='content_title'>Your Friends</h1>
            </td>
          </tr>";
          displayFriends($fp, $_SESSION["user"]);
    echo "
        </tbody>
      </table>
      <table class='requests'>
        <tbody>
          <tr>
            <td>
              <hr>
              <h1 class='content_title'>Pending Requests</h1>
            </td>
          </tr>";
          displayPendingRequests($fp, $_SESSION["user"]);
    echo "
        </tbody>
      </table>
      <table class='friends'>
        <tbody>
          <tr>
            <td>
            <hr>
              <h1 class='content_title'>Your Followers</h1>
            </td>
          </tr>";
          displayFollowers($fp, $_SESSION["user"]);
    echo "
        </tbody>
      </table>
    ";
    fclose($fp);
  }


  function cancelRequest($user, $requested) {
    connectToBackEndServer("cancelRequest $user $requested", FALSE);
  }

  function sendRequest($user, $requester){
    connectToBackEndServer("sendRequest $user $requester", FALSE);
  }

  function addFriend($user, $friend){
    connectToBackEndServer("addFriend $user $friend", FALSE);
  }

  function deleteFriend($user, $friend){
    connectToBackEndServer("deleteFriend $user $friend", FALSE);
  }

  function deleteFollower($user, $follower){
    connectToBackEndServer("deleteFollower $user $follower", FALSE);
  }

  function isFriend($user, $requester){
    return (connectToBackEndServer("isFriend $user $requester", TRUE) == "1") ? true : false;   
  }

  function canRequest($user, $requester){
    return connectToBackEndServer("canRequest $user $requester", TRUE) == "1" ? true : false; 
  }

  //Search functions
  //Queries all RM for any user with $user in their name or username
  function displayUsers($user){
    $fp = 0;
    $replicaManagersIP = getRMIP();
    $replicaManagersPort = getRMPort();
    for ($i = 0; $i < count($replicaManagersIP); ++$i) {
      $fp = @stream_socket_client("tcp://".$replicaManagersIP[$i].":".$replicaManagersPort[$i], $errno, $errstr, 5);
      if ($fp) {
        writeData($fp, "displayUsers $user");
      }
    }

    $count = readData($fp);
    while ($count > 0) {
      $reply = readData($fp);
      $reply = explode(" ", $reply);
      echo "<tr>";
        echo "<td>";
          echo "<h2><a href='profile.php?name=".$reply[0]."'>".$reply[1]." ".$reply[2]." (".$reply[0].")</a></h2>";
        echo "</td>";
      echo "</tr>";
      $count -= 1;
    }
    fclose($fp);
  }
?>