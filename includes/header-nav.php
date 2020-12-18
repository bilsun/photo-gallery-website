<header>
    <h1 id="title">
        <a href='index.php'>Views from McGraw Tower</a>
    </h1>

    <?php if (is_user_logged_in()) { ?>
        <nav id="menu">
            <ul>
                <?php
                $current_file = basename($_SERVER['PHP_SELF']);

                $page_info = array(['index.php', 'View Images'], ['upload.php', 'Upload Images']);

                foreach($page_info as $page) {
                    if($current_file == $page[0]) {
                    $css_id = "id='active_page' ";
                    } else {
                    $css_id = "";
                    }

                    echo "<li><a " . $css_id . "class='nav_anchor' href='" . $page[0] . "'>" . $page[1] . "</a></li>";
                }
                ?>
            </ul>
        </nav>
    <?php } ?>
</header>
