<?php

// enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();


// determine requested page
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$page = ($path === '/' || $path === '') ? 'home' : ltrim($path, '/');
$ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']);


// function to render inner content based on $page
function renderContent()
{
    global $page;
    switch ($page) {
        case 'songs':
            echo '<section><h1 class="title">Songs</h1><p>Here is your list of songs.</p></section>';
            break;
        case 'artists':
            echo '<section><h1 class="title">Artists</h1><p>Here is your list of artists.</p></section>';
            break;
        case 'upload':
            echo
            '<section><h1 class="title">Upload</h1>
            <form class="box">
            <div class="field">
            <label class="label">Song File</label>
            <div class="control">
            <input class="input" type="file" name="song">
            </div>
            </div>
            <button class="button is-primary">Upload</button>
            </form>
            </section>';
            break;


    }

}


// if AJAX request, return only the fragment
if ($ajax) {
    renderContent();
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Music Platform</title>
    <!-- Bulma CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <style>
        /* fixed navbar */
        .is-fixed-top {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 30;
        }

        .navbar-spacer {
            height: 3.25rem;
        }

        /* hero background */
        .home-hero {
            background-image: url('./images/hero-bg.png');
            background-size: cover;
            background-position: center;
        }

        /* active nav item */
        .navbar-item.is-active {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }
    </style>
</head>
<body>
<nav class="navbar is-dark is-fixed-top" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <a class="navbar-item" href="/">Music Platform</a>
        <a role="button" class="navbar-burger" data-target="navMenu" aria-label="menu" aria-expanded="false">
            <span aria-hidden="true"></span><span aria-hidden="true"></span><span aria-hidden="true"></span>
        </a>
    </div>
    <div id="navMenu" class="navbar-menu">
        <div class="navbar-start">
            <a class="navbar-item" href="/">Home</a>
            <a class="navbar-item" href="/songs">Songs</a>
            <a class="navbar-item" href="/artists">Artists</a>
            <a class="navbar-item" href="/upload">Upload</a>
        </div>
    </div>
</nav>
<div class="navbar-spacer"></div>

<main class="section">
    <div class="container" id="app">
        <div id="page-content">
            <?php renderContent(); ?>
        </div>
    </div>
</main>

<footer class="footer">
    <div class="content has-text-centered">
        <p>Music Platform &copy; <?= date('Y') ?></p>
    </div>
</footer>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(function () {
        // burger toggle
        $('.navbar-burger').click(function () {
            $('#' + $(this).data('target')).toggleClass('is-active');
        });
        // SPA navigation
        $('.navbar-item').click(function (e) {
            var href = $(this).attr('href');
            if (href && href.startsWith('/')) {
                e.preventDefault();
                history.pushState(null, '', href);
                loadPage(href);
                setActiveNav(href);
            }
        });
        window.onpopstate = function () {
            loadPage(location.pathname);
            setActiveNav(location.pathname);
        };

        function loadPage(path) {
            $.get(path, function (html) {
                var content = $('<div>').html(html).find('#page-content').html() || '<p>Not found.</p>';
                $('#page-content').fadeOut(150, function () {
                    $(this).html(content).fadeIn(150);
                });
            });
        }

        function setActiveNav(path) {
            $('.navbar-item').removeClass('is-active');
            $('.navbar-item[href="' + path + '"]').addClass('is-active');
        }

        // initialize
        setActiveNav(location.pathname);
    });
</script>
</body>
</html>

