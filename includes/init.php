<?php
// vvv DO NOT MODIFY/REMOVE vvv

// check current php version to ensure it meets 2300's requirements
function check_php_version()
{
  if (version_compare(phpversion(), '7.0', '<')) {
    define(VERSION_MESSAGE, "PHP version 7.0 or higher is required for 2300. Make sure you have installed PHP 7 on your computer and have set the correct PHP path in VS Code.");
    echo VERSION_MESSAGE;
    throw VERSION_MESSAGE;
  }
}
check_php_version();

function config_php_errors()
{
  ini_set('display_startup_errors', 1);
  ini_set('display_errors', 0);
  error_reporting(E_ALL);
}
config_php_errors();

// open connection to database
function open_or_init_sqlite_db($db_filename, $init_sql_filename)
{
  if (!file_exists($db_filename)) {
    $db = new PDO('sqlite:' . $db_filename);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (file_exists($init_sql_filename)) {
      $db_init_sql = file_get_contents($init_sql_filename);
      try {
        $result = $db->exec($db_init_sql);
        if ($result) {
          return $db;
        }
      } catch (PDOException $exception) {
        // If we had an error, then the DB did not initialize properly,
        // so let's delete it!
        unlink($db_filename);
        throw $exception;
      }
    } else {
      unlink($db_filename);
    }
  } else {
    $db = new PDO('sqlite:' . $db_filename);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
  }
  return null;
}

function exec_sql_query($db, $sql, $params = array())
{
  $query = $db->prepare($sql);
  if ($query and $query->execute($params)) {
    return $query;
  }
  return null;
}
// ^^^ DO NOT MODIFY/REMOVE ^^^


// You may place any of your code here.

$db = open_or_init_sqlite_db("secure/gallery.sqlite", "secure/init.sql");

// ******************************* LOGIN & LOGOUT *******************************
// Reference: (original work) Kyle Harms

const SESSION_COOKIE_DURATION = 3600; // 1 hr = (60 sec per min) * (60 min per hr)

function log_in($username, $password) {
  global $db;
  global $current_user;

  if ( isset($username) && isset($password) ) {
    // check if user exists in db
    $sql = "SELECT * FROM users WHERE username = :username;";
    $params = array(
      ':username' => $username
    );

    $records = exec_sql_query($db, $sql, $params)->fetchAll();

    if ($records) {

      $user_account = $records[0]; // users are unique

      // check if password is valid
      if ( password_verify($password, $user_account['password']) ) {

        $session = session_create_id();

        // store session ID
        $sql = "INSERT INTO sessions (user_id, session) VALUES (:user_id, :session);";
        $params = array(
          ':user_id' => $user_account['id'],
          ':session' => $session
        );

        $result = exec_sql_query($db, $sql, $params);

        if ($result) {
          // session successfully stored in DB
          setcookie("session", $session, time() + SESSION_COOKIE_DURATION);

          $current_user = $user_account;
          return $current_user;
        }
      }
    }
  }
  $current_user = NULL;
  return NULL;
}

function find_user($user_id) {
  global $db;

  $sql = "SELECT * FROM users WHERE id = :user_id;";
  $params = array(
    ':user_id' => $user_id
  );
  $records = exec_sql_query($db, $sql, $params)->fetchAll();
  if ($records) {
    return $records[0]; // users are unique
  }
  return NULL;
}

function find_session($session) {
  global $db;

  if (isset($session)) {
    $sql = "SELECT * FROM sessions WHERE session = :session;";
    $params = array(
      ':session' => $session
    );
    $records = exec_sql_query($db, $sql, $params)->fetchAll();
    if ($records) {
      return $records[0]; // sessions are unique
    }
  }
  return NULL;
}

function session_login() {
  global $db;
  global $current_user;

  if (isset($_COOKIE["session"])) {
    $session = $_COOKIE["session"];
    $session_record = find_session($session);

    if ( isset($session_record) ) {
      $current_user = find_user($session_record['user_id']);

      // renew cookie for 1 more hour
      setcookie("session", $session, time() + SESSION_COOKIE_DURATION);
      return $current_user;
    }
  }
  $current_user = NULL;
  return NULL;
}

function is_user_logged_in() {
  global $current_user;
  return ($current_user != NULL);
}

function log_out() {
  global $current_user;
  // force session to expire
  setcookie('session', '', time() - SESSION_COOKIE_DURATION);
  $current_user = NULL;
}

// check if we should log in user
if ( isset($_POST['login']) && isset($_POST['username']) && isset($_POST['password']) ) {
  $username = trim( $_POST['username'] );
  $password = trim( $_POST['password'] );
  log_in($username, $password);

} else {
  // check if already logged in via cookie
  session_login();
}

// check if we should log out user
if ( isset($current_user) && ( isset($_GET['logout']) || isset($_POST['logout']) ) ) {
  log_out();
}

?>
