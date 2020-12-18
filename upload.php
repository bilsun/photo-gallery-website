<?php
include("includes/init.php");
const MAX_FILE_SIZE = 1000000; // maximum file size 1 MB = 1000000 bytes
$message = "";

// users must be logged in to upload files
if ( isset($_POST["submit_upload"]) && is_user_logged_in() ) {

  $upload_info = $_FILES["img_file"];
  $img_desc = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

  if ($upload_info["error"] == UPLOAD_ERR_OK) {
    $upload_name = basename($upload_info["name"]);
    $upload_ext = strtolower( pathinfo($upload_name, PATHINFO_EXTENSION) );

    $sql = "INSERT INTO images (user_id,file_name,file_ext,description) VALUES (:user_id,:file_name, :file_ext, :description);";
    $params = array(
      ':user_id' => $current_user['id'],
      ':file_name' => $upload_name,
      ':file_ext' => $upload_ext,
      ':description' => $img_desc
    );

    $result = exec_sql_query($db, $sql, $params);

    if ($result) {
      $file_id = $db->lastInsertId("id");
      $id_path = "uploads/images/" . $file_id . "." . $upload_ext;

      if (move_uploaded_file( $upload_info["tmp_name"], $id_path )) {
        $message = "Upload Successful!";
      } else {
        $message = "Failed to upload file. Please try again!";
      }
    } else {
      $message = "Failed to upload file. Please try again!";
    }
  } else {
    $message = "Failed to upload file. Please try again!";
  }
} ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>Views from McGraw Tower - Upload</title>
  <link rel="stylesheet" type="text/css" href="styles/all.css" media="all" />
</head>

<body>

  <?php include("includes/header-nav.php"); ?>
  <?php include("includes/login-logout.php"); ?>
  <h2>Upload an Image</h2>

  <?php if ( is_user_logged_in() ) { ?>

    <form id="uploadFile" action="upload.php" method="post" enctype="multipart/form-data">
        <fieldset>
          <ul>
            <li>
              <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE; ?>" />
              <label for="img_file">Upload Image:</label>
              <input id="img_file" type="file" name="img_file">
            </li>

            <li>
              <label for="img_desc">Description:</label> <br/>
              <textarea id="img_desc" name="description" cols="40" rows="5"></textarea>
            </li>

            <li>
              <button name="submit_upload" type="submit">Upload Image</button>
            </li>
          </ul>
        </fieldset>
    </form>
    <p class="user_msg"> <?php echo $message ?>

  <?php } else { ?>

    <p class="user_msg">Please sign in before uploading an image to our gallery!</p>

  <?php } ?>

  <?php include("includes/footer.php"); ?>

</body>
</html>
