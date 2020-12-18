<?php
include("includes/init.php");

// display distinct images on page (all seed images are my own!)
function display_imgs($record) {
    echo "<figure class='centered_imgs'>
    <a href='single_img.php?" . http_build_query(array('id'=>$record['id'])) . "'><img src='uploads/images/" . $record["id"] . "." . htmlspecialchars($record["file_ext"]) . "' class='multi_img' alt='" . htmlspecialchars($record["description"]) . "'/></a>
    <figcaption>
    <a href='single_img.php?" . http_build_query(array('id'=>$record['id'])) . "'>View Image Details</a>
    </figcaption>
    </figure>";
}

// display tags as links & programatically indicate current tag in bold
function display_tags($browsed_tag) {
  global $db;
  $sql = "SELECT DISTINCT tag FROM tags
  INNER JOIN image_tags ON tags.id = image_tags.tag_id
  ORDER BY tag;"; // only display tags with at least 1 associated image
  $params = array();
  $result = exec_sql_query($db, $sql, $params);

  if ($result) {
    $tags = $result->fetchAll();

    if ($browsed_tag == 'ALL' || $browsed_tag =="") { // all tags
      echo "<p class='tags'><strong>
      <a href='index.php?" . http_build_query(array('tag'=>'ALL')) . "'>All Tags</a></strong> &nbsp;&nbsp;";
      foreach($tags as $tag) {
        echo "<a href='index.php?" . http_build_query(array('tag'=>htmlspecialchars($tag['tag']))) . "'>" . htmlspecialchars($tag['tag']) . "</a> &nbsp;&nbsp;";
      }
    } else {
      echo "<p class='tags'>
      <a href='index.php?" . http_build_query(array('tag'=>'ALL')) . "'>All Tags</a> &nbsp;&nbsp;";
      foreach($tags as $tag) {
        if ($tag['tag'] == $browsed_tag) {
          echo "<strong><a href='index.php?" . http_build_query(array('tag'=>htmlspecialchars($tag['tag']))) . "'>" . htmlspecialchars($tag['tag']) . "</a></strong> &nbsp;&nbsp;";
        } else {
          echo "<a href='index.php?" . http_build_query(array('tag'=>htmlspecialchars($tag['tag']))) . "'>" . htmlspecialchars($tag['tag']) . "</a> &nbsp;&nbsp;";
        }
      }
    }
    echo "</p>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>Views from McGraw Tower - Home</title>
  <link rel="stylesheet" type="text/css" href="styles/all.css" media="all" />
</head>

<body>

  <?php include("includes/header-nav.php"); ?>
  <?php include("includes/login-logout.php"); ?>

  <h2>Browse Tags</h2>

  <?php

    $browsed_tag = filter_input(INPUT_GET, 'tag', FILTER_SANITIZE_STRING);
    display_tags(htmlspecialchars($browsed_tag));

    if ($browsed_tag == 'ALL' || $browsed_tag =="") {
      $browsed_tag == 'All';
      $sql = "SELECT DISTINCT id, file_ext, description FROM images;";
      $params = array();
    } else {
      $sql = "SELECT DISTINCT images.id, images.file_ext, images.description, tags.tag FROM images
      LEFT OUTER JOIN image_tags ON images.id = image_tags.image_id
      INNER JOIN tags ON tags.id = image_tags.tag_id
      WHERE tags.tag = :tag;";
      $params = array(
        'tag' => $browsed_tag
      );
    }

    $result = exec_sql_query($db, $sql, $params);
    if ($result) {
      $records = $result->fetchAll();
      foreach($records as $record) {
        display_imgs($record);
      }
    }

    ?>

  <?php include("includes/footer.php"); ?>

</body>
</html>
