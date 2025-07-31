<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= $title ?? 'Music Platform' ?></title>
    <!-- Bulma CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../public/css/tfn-style.css">
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
            background-color: var(--indigo);
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
        <div class="navbar-end">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="navbar-item has-dropdown is-hoverable">
                    <a class="navbar-link">
                        Account
                    </a>
                    <div class="navbar-dropdown is-right">
                        <a class="navbar-item" href="/profile">
                            Profile
                        </a>
                        <a class="navbar-item" href="/playlists">
                            My Playlists
                        </a>
                        <hr class="navbar-divider">
                        <a class="navbar-item" href="/auth/logout">
                            Logout
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="navbar-item">
                    <div class="buttons">
                        <a class="button is-primary" href="/auth/register">
                            <strong>Sign up</strong>
                        </a>
                        <a class="button is-light" href="/auth/login">
                            Log in
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>
<div class="navbar-spacer"></div>

<main class="section">
    <div class="container" id="app">
        <div id="page-content">
            <?= $content ?>
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
            $(this).toggleClass('is-active');
        });
    });
</script>
</body>
</html>