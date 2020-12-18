<?php
include("includes/init.php");
$img_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
$page_bottom = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL) . "#edit"; // help redirect to page bottom when filling out forms
$new_tag = ucwords(filter_input(INPUT_POST, 'new_tag_name', FILTER_SANITIZE_STRING)); // uppercase for consistency
$existing_tag_id = filter_input(INPUT_POST, 'existing_tag_input', FILTER_SANITIZE_STRING);
$delete_tag_id = filter_input(INPUT_POST, 'delete_tag_input', FILTER_SANITIZE_STRING);
$message = "";

$sql = "SELECT DISTINCT images.id, images.user_id, images.file_ext, images.description, tags.tag, tags.id FROM images
    LEFT OUTER JOIN image_tags ON images.id = image_tags.image_id
    LEFT OUTER JOIN tags ON tags.id = image_tags.tag_id
    WHERE images.id = :img_id;";
$params = array(
    ':img_id' => $img_id
);
$single_img_result = exec_sql_query($db, $sql, $params);

if ($single_img_result) {
    $records = $single_img_result->fetchAll();
    foreach($records as $record) {
        $current_tags .= htmlspecialchars($record["tag"]) . ", ";
    }
}

// display existing tags in dropdown list after clicking "Add Existing Tag"
function list_all_tags($current_tags) {
    global $db;
    $sql = "SELECT DISTINCT tag FROM tags ORDER BY tag;";
    $params = array();
    $result = exec_sql_query($db, $sql, $params);

    if ($result) {
    $tags = $result->fetchAll(); ?>
    <p>
        <label for="existing_tag_input">Tag Name:</label>
        <select id="existing_tag_input" name="existing_tag_input">
        <option value="" selected disabled>Select Tag</option>
        <?php foreach ($tags as $tag) {
            // only display tags not already associated with image
            if (strpos($current_tags, $tag['tag']) === FALSE) {
            echo "<option value = '" . htmlspecialchars($tag['id']) . "'>" . htmlspecialchars($tag['tag']) . "</option>" ;
            }
        } ?>
        </select>
    </p>
    <?php }
}

function delete_img($file) {
    global $db;
    global $message;
    global $img_id;

    $sql = "DELETE FROM image_tags WHERE image_id = :img_id;";
    $params = array(
        ':img_id' => $img_id
    );
    $result = exec_sql_query($db, $sql, $params);

    if ($result) {
        $sql = "DELETE FROM images WHERE id = :img_id;";
        $params = array(
            ':img_id' => $img_id
        );
        $result = exec_sql_query($db, $sql, $params);
        if ($result) {
            header('Location: index.php'); // redirect to index.php after successfully deleting image
            unlink($file); // delete image file
        } else {
            $message = "Failed to delete image. Please try again!";
        }
    } else {
        $message = "Failed to delete image. Please try again!";
    }
}

if (isset($_POST['submit_new_tag']) && !empty($new_tag)) { // exit form if no tag is entered
    $unique_tag = TRUE;
    foreach ($records as $record) {
        if (strpos(strtolower($current_tags), strtolower($new_tag)) !== FALSE) { // verify user-entered tag is unique
            $unique_tag = FALSE;
        }
    }
    if ($unique_tag) { // if user-entered tag is unique
        $sql = "INSERT INTO tags (tag) VALUES (:tag);";
        $params = array(
        ':tag' => $new_tag
        );
        $result = exec_sql_query($db, $sql, $params);

        if ($result) {
            $new_tag_id = $db->lastInsertId("id");
            $sql = "INSERT INTO image_tags (image_id,tag_id) VALUES (:img_id,:new_tag_id);";
            $params = array(
                ':img_id' => $img_id,
                ':new_tag_id' => $new_tag_id
            );
            $result = exec_sql_query($db, $sql, $params);
            if ($result) {
                $message = "Tag added!";
            } else {
                $message = "Failed to add tag. Please try again!";
            }
        } else {
            $message = "Failed to add tag. Please try again!";
        }
    } else { // if user-entered tag already exists
        $message = "Tag already exists in our database. Try adding an existing tag!";
    }
}

if (isset($_POST['submit_existing_tag'])) {
    $sql = "INSERT INTO image_tags (image_id,tag_id) VALUES (:img_id,:existing_tag_id);";
    $params = array(
        ':img_id' => $img_id,
        ':existing_tag_id' => $existing_tag_id
    );
    $result = exec_sql_query($db, $sql, $params);
    if ($result) {
        $message = "Tag added!";
    } else {
        $message = "Failed to add tag. Please try again!";
    }
}

if (isset($_POST['submit_delete_tag'])) {
    $sql = "DELETE FROM image_tags WHERE image_id = :img_id AND tag_id = :delete_tag_id;";
    $params = array(
        ':img_id' => $img_id,
        ':delete_tag_id' => $delete_tag_id
    );
    $result = exec_sql_query($db, $sql, $params);
    if ($result) {
        $message = "Tag deleted!";
    } else {
        $message = "Failed to delete tag. Please try again!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>Image Details</title>
  <link rel="stylesheet" type="text/css" href="styles/all.css" media="all" />
</head>

<body>

  <?php include("includes/header-nav.php"); ?>
  <?php include("includes/login-logout.php"); ?>

  <h2>Image Details</h2>

  <?php
    if ($single_img_result) {
        // display single image
        echo "<img src='uploads/images/" . htmlspecialchars($img_id) . "." . htmlspecialchars($records[0]["file_ext"]) . "' class='single_img' alt='" . htmlspecialchars($records[0]["description"]) . "'/>";

        echo "<p class='tags'> Tags: ";
        if (trim($current_tags) == ",") {
            $current_tags = "None, "; // display "Tags: None" if no tags found
        }
        echo rtrim($current_tags, ', ') . "<a id='edit'></a></p>;
        <form action='" . $page_bottom . "' method='post'>"; ?>

        <input type='submit' name='new_tag' value='Add New Tag'/> &nbsp;
        <input type='submit' name='existing_tag' value='Add Existing Tag'/> &nbsp;

        <?php if ( is_user_logged_in() ) {
            if ($records[0]["user_id"] == $current_user['id']) { // if logged in user uploaded the image
                if ($records[0]["tag"] != "") { ?>
                <input type='submit' name='delete_tag' value='Remove a Tag'/> &nbsp;
                <?php } ?>
                <input type='submit' name='delete_img' value='Delete Image'/>
            <?php }
        }
        echo "</form>";
    }

    if (isset($_POST['new_tag'])) { ?>
        <form id="new_tag" class="img_options" method="post" action='<?php echo $page_bottom ?>'>
            <fieldset>
                <legend> Add New Tag </legend>
                <p>
                    <label for="new_tag_name"> New Tag Name: </label>
                    <input id="new_tag_name" type="text" name="new_tag_name" value =""/>
                </p>
                <input type="submit" name="submit_new_tag" value="Submit"/>
            </fieldset>
        </form>
    <?php }

    if (isset($_POST['existing_tag'])) { ?>
        <form id="existing_tag" class="img_options" method="post" action='<?php echo $page_bottom ?>'>
            <fieldset>
                <legend>Add Existing Tag</legend>
                <?php if ($single_img_result) {
                        list_all_tags($current_tags);
                } else {
                    $message = "Failed to load existing tags. Please try again!";
                } ?>

                <input type="submit" name="submit_existing_tag" value="Submit"/>
            </fieldset>
        </form>
    <?php }

    if (isset($_POST['delete_tag'])) { ?>
        <form id="delete_tag" class="img_options" method="post" action='<?php echo $page_bottom ?>'>
            <fieldset>
                <legend>Delete Image Tag</legend>
                <p>
                    <label for="delete_tag_input">Tag Name:</label>
                    <select id="delete_tag_input" name="delete_tag_input">
                    <option value="" selected disabled>Select Tag</option>

                    <?php if ($single_img_result) {
                        foreach ($records as $record) {
                            echo "<option value = '" . htmlspecialchars($record['id']) . "'>" . htmlspecialchars($record['tag']) . "</option>" ;
                        }
                    } ?>
                    </select>
                <p>
                <input type="submit" name="submit_delete_tag" value="Submit"/>
        </fieldset>
    </form>

    <?php } if (isset($_POST['delete_img'])) {
        if ($single_img_result) {
            $file = "uploads/images/" . htmlspecialchars($img_id) . "." . htmlspecialchars($records[0]["file_ext"]);
            delete_img($file);
        }
    }?>

    <p class="user_msg"> <?php echo $message ?> </p>

  <?php include("includes/footer.php"); ?>

</body>
</html>
