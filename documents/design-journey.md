# Project 3: Design Journey

Your Name: Billie Sun

# Project 3, Milestone 1 - Design, Plan, & Draft Website

## Describe your Gallery

Images of Cornell taken from the top of McGraw Tower


## Target Audiences

* Content creators: current chimesmasters sharing images taken from the top of the tower (log in to upload and manage images/tags)

* Content viewers: current & prospective Cornell students interested in viewing images of the campus (may browse images and add/browse tags without logging in)


## Design Process

Card Sorting
![card sorting](cardsort.jpg)

Preliminary sketches
![Preliminary sketches](it1.jpg)

Iteration 2: centered text, added footer, moved navigation bar above login bar for clearer information hierarchy
![Iteration 2](it2.JPG)


## Final Design Plan

Final design:
* Modified navigation bar to contain "View Images" and "Upload Images" (instead of "View Images" and "Your Images")
* Changed grid layout to single image layout to better accommodate for different image dimensions

Home page: logged out
![Home page: logged out](fin_1.jpg)

Home page: logged in
![Home page: logged in](fin_2.jpg)

Single image page
![Single image page](fin_3.jpg)

Upload page
![Upload page](fin_4.jpg)


## Templates

* header-nav: "Views from McGraw Tower" header (& navigation bar if user is logged in)
* login-logout: login form if user is logged out, "Welcome, user!" & logout option if user is logged in
* footer: Cornell Chimes email & Facebook page for current & prospective Cornell students to reference


## Database Schema Design

```
users (
  id: INTEGER {PK, U, Not, AI} -- surrogate primary key
  username: TEXT {U, Not}
  password: TEXT {Not}
)

images (
  id: INTEGER {PK, U, Not, AI} -- surrogate primary key
  user_id: INTEGER {Not} -- foreign key
  file_name: TEXT {Not}
  file_ext: TEXT {Not}
  description: TEXT
)

tags (
  id: INTEGER {PK, U, Not, AI} -- surrogate primary key
  tag: TEXT {U, Not}
)

image_tags (
  id: INTEGER {PK, U, Not, AI} -- surrogate primary key
  image_id: INTEGER {Not} -- foreign key
  tag_id: INTEGER {Not} -- foreign key
)

sessions (
  id: INTEGER {PK, U, Not, AI} -- surrogate primary key
  user_id: INTEGER {Not} -- foreign key
  session: TEXT {U, Not}
)
```

## Code Planning

Top-level PHP pages
* index.php (home page; view all/multiple images)
* single_img.php (view and/or edit a single image)
* upload.php (upload an image)

Templates
* header-nav:
  ```
  always display "Views from McGraw Tower" header as an unformatted link to index.html

  if user is logged in:
      show navigation bar ("View Images" and "Upload Images")
  ```

* login-logout: login form if user is logged out, "Welcome, user!" & logout option if user is logged in
  ```
  if user is logged in:
      display "Welcome, user!" and link to sign out

  if user is not logged in:
      display login form
  ```
* footer: no PHP; link Cornell Chimes email & Facebook page for current & prospective Cornell students to reference

PHP Planning

init.php:
```
function is_user_logged_in {
  if user is logged in, return true
  otherwise, return false
}
```

index.php:
```
for each distinct image {
    function display_imgs($record) {
        display image with caption "View Image Details"
        (image and caption both link to single image page with query string parameter corresponding with image ID)
    }
}
```
```
function display_tags($browsed_tag) {
    if user wants to view all tags:
        set query string parameter to "ALL"
        echo each tag ("All Tags" in bold)

    otherwise:
        set query string parameter equal to the tag selected
        echo each tag (selected tag in bold)
}
```

single_img.php:
```
if adding an existing tag to an image:
  function list_all_tags($current_tags) {
        for each tag:
            if tag is NOT already associated with image:
            add tag as an option to dropdown list
  }
```
```
function delete_img($file) {
    delete entries in image_tags with that image
    delete image from images table
    delete image file from uploads/images
    redirect to index.php after deleting
}
```
```
if user is logged in & user uploaded the image:
    display all add and delete options

if user is not logged in:
    only display "Add New Tag" and "Add Existing Tag" options
```
```
if add/delete tag button is submitted:
    display simple form to add/delete tag
```

upload.php:
```
if user is logged in:
    display upload form

if user is not logged in:
    ask user to log in before uploading an image
```
```
if user is logged in & form is submitted:
  filter input
  add image to database
  add image to files
```


## Database Query Plan

All tags
```sql
  SELECT DISTINCT tag FROM tags ORDER BY tag;
```

All images
```sql
  SELECT DISTINCT id, file_ext, description FROM images;
```

Tags with an associated image
```sql
  SELECT DISTINCT tag FROM tags
  INNER JOIN image_tags ON tags.id = image_tags.tag_id
  ORDER BY tag;
```

Images under a specific tag
```sql
  SELECT DISTINCT images.id, images.file_ext, images.description, tags.tag FROM images
  LEFT OUTER JOIN image_tags ON images.id = image_tags.image_id
  INNER JOIN tags ON tags.id = image_tags.tag_id
  WHERE tags.tag = :tag;
```

Image and tag information for one specific image
```sql
  SELECT DISTINCT images.id, images.user_id, images.file_ext, images.description, tags.tag, tags.id FROM images
  LEFT OUTER JOIN image_tags ON images.id = image_tags.image_id
  LEFT OUTER JOIN tags ON tags.id = image_tags.tag_id
  WHERE images.id = :img_id;
```

Add an image
```sql
  INSERT INTO images (user_id,file_name,file_ext,description)
  VALUES (:user_id,:file_name, :file_ext, :description);
```

Add an existing tag
```sql
  INSERT INTO image_tags (image_id,tag_id) VALUES (:img_id,:existing_tag_id);
```

Add a new tag
```sql
  INSERT INTO tags (tag) VALUES (:tag);
  INSERT INTO image_tags (image_id,tag_id) VALUES (:img_id,:new_tag_id);
```

Delete a tag from an image (note: does not delete the tag itself)
```sql
  DELETE FROM image_tags
  WHERE image_id = :img_id
  AND tag_id = :delete_tag_id;
```

Delete an image
```sql
  DELETE FROM image_tags WHERE image_id = :img_id;
  DELETE FROM images WHERE id = :img_id;
```


# Project 3, Milestone 2 - Gallery and User Access Controls

## Issues & Challenges

* I realized that uploading an image would look best on a separate page, so I had to reformat my layout slightly
* I struggled to fine-tune content using HTML and CSS as it got more complex (still working on it!)
* I initially had some syntax errors in init.sql, so debugging it was a challenge (but DB Browser was a big help!)

# Final Submission: Complete & Polished Website

## Reflection

I found this assignment a lot more challenging than projects 1 & 2 (but definitely very rewarding)! This project was a great opportunity to learn about the specifics of website security and dynamically displaying web content from databases. I used a lot more PHP in this project than I previously had in this class, so I feel that I've come a long way since the beginning (i.e. knowing next to nothing about web development). Looking forward to Project 4!
