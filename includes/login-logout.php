<?php

$current_page = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL); // current page including query string parameters

function url($current_page){ // returns correct URL to be used in $logout_url (given query string parameters)
    if (strpos($current_page, '?') == FALSE) {
        return $url = htmlspecialchars( $_SERVER['PHP_SELF'] ) . '?';
    } elseif (strpos($current_page, '&') != FALSE) {
        return $url = rtrim(htmlspecialchars($current_page), '&logout=') . '&';
    } else {
        return $url = rtrim(htmlspecialchars($current_page), 'logout=') . '&';
    }
}

if ( is_user_logged_in() ) { ?>

    <p class="login user_msg">
    Welcome, <?php echo htmlspecialchars($current_user['username']) ?>! &nbsp;

    <?php
    $logout_url = url($current_page) . http_build_query( array( 'logout' => '' ) );
    echo '<a href="' . $logout_url . '">Sign Out </a>'; ?>
    </p>

<?php } else { ?>

    <form id="loginForm" class="login" action="<?php echo rtrim(htmlspecialchars($current_page), 'logout='); ?>" method="post">
        <label for="username">Username:</label>
        <input id="username" type="text" name="username"/>
        &nbsp;
        <label for="password">Password:</label>
        <input id="password" type="password" name="password"/>
        &nbsp;
        <button name="login" type="submit">Sign In</button>
    </form>

<?php } ?>
