<?php

?>
<!doctype html>
<html lang="en">
<head>
    <title>Title</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bulma CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.0/css/bulma.min.css">
</head>
<body>
<!--NAV-->
<nav class="navbar is-light" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <!-- Homepage url -->
        <a class="navbar-item" href="https://bulma.io">
            <!-- Brand logo -->
            <img src="https://bulma.io/images/bulma-logo.png" width="112" height="28">
        </a>

        <a role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false" data-target="navbarId">
            <span aria-hidden="true">navbarId</span>
            <span aria-hidden="true">Menu Item</span>
            <span aria-hidden="true">Menu Item</span>
        </a>
    </div>
    <div id="Menu Item" class="navbar-menu">
        <div class="navbar-start">
            <a class="navbar-item">
                Menu Item
            </a>
            <a class="navbar-item">
                Item
            </a>
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link">
                    Item with menu
                </a>
                <div class="navbar-dropdown">
                    <a class="navbar-item">
                        Submenu item
                    </a>
                    <a class="navbar-item">
                        Submenu item
                    </a>
                    <hr class="navbar-divider">
                    <a class="navbar-item">
                        Submenu item with divider
                    </a>
                </div>
            </div>
        </div>

        <div class="navbar-end">
            <div class="navbar-item">
                <div class="buttons">
                    <a class="button is-primary">
                        <strong>Action</strong>
                    </a>
                    <a class="button is-light">
                        Action
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>
<!--HERO-->
<section class="hero is-primary">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
                Title
            </h1>
            <h2 class="subtitle">
                Subtitle
            </h2>
        </div>
    </div>
</section>

<!-- Albums grid -->
<section id="albums">
    <h2 class="title is-4">
        <span class="icon"><i class="fas fa-compact-disc"></i></span>
        Albums
    </h2>
    <div class="columns is-multiline">
        <div class="column is-one-quarter">
            <div class="card">
                <div class="card-image">
                    <figure class="image is-4by3">
                        <img src="https://images.pexels.com/photos/164853/pexels-photo-164853.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=300&w=300" alt="Album Placeholder">
                    </figure>
                </div>
                <div class="card-content">
                    <p class="title is-6">Album Title</p>
                    <p class="subtitle is-7">Artist Name</p>
                </div>
            </div>
        </div>
        <!-- SVG placeholder -->
        <div class="column is-one-quarter">
            <div class="card">
                <div class="card-image">
                    <svg class="image is-4by3" xmlns="http://www.w3.org/2000/svg"
                         role="img" aria-label="Placeholder: 4×3" preserveAspectRatio="xMidYMid slice" focusable="false">
                        <rect width="100%" height="100%" fill="#868e96"></rect>
                        <text x="50%" y="50%" fill="#dee2e6" dy=".3em" text-anchor="middle">4×3</text>
                    </svg>
                </div>
                <div class="card-content">
                    <p class="title is-6">Loading...</p>
                </div>
            </div>
        </div>
        <!-- repeat other placeholders as needed -->
    </div>
</section>

<!-- Featured tracks carousel -->
<section id="featured-tracks" class="mt-6">
    <h2 class="title is-4">
        <span class="icon"><i class="fas fa-music"></i></span>
        Featured Tracks
    </h2>
    <div class="scroll-container">
        <div class="scroll-item card">
            <div class="card-image">
                <img src="https://images.pexels.com/photos/164835/pexels-photo-164835.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=200&w=200" alt="Track">
            </div>
            <div class="card-content">
                <p class="title is-6">Track Title</p>
                <p class="subtitle is-7">Artist</p>
            </div>
        </div>
        <!-- Repeat 4 more items -->
    </div>
</section>

<!--Main-->

<!-- Artist of the Week -->
<section id="artist-of-week" class="mt-6">
    <h2 class="title is-4">
        <span class="icon"><i class="fas fa-star"></i></span>
        TFN Artist of the Week
    </h2>
    <div class="box">
        <article class="media">
            <figure class="media-left">
                <p class="image is-64x64">
                    <img src="https://images.pexels.com/photos/39958/pexels-photo-39958.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=64&w=64" alt="Artist">
                </p>
            </figure>
            <div class="media-content">
                <div class="content">
                    <p>
                        <strong>Artist Name</strong> <small>@artist</small>
                        <br>
                        A brief bio or highlight of the featured artist.
                    </p>
                </div>
                <nav class="level is-mobile">
                    <div class="level-left">
                        <a class="level-item" aria-label="follow">
                            <span class="icon is-small"><i class="fas fa-user-plus"></i></span>
                        </a>
                        <a class="level-item" aria-label="share">
                            <span class="icon is-small"><i class="fas fa-share-alt"></i></span>
                        </a>
                    </div>
                </nav>
            </div>
        </article>
    </div>
</section>
</main>
<!--Aside Filters -->
<aside class="menu section container" id="filters">
    <p class="menu-label">
        <span class="icon"><i class="fas fa-filter"></i></span>
        Filters
    </p>
    <ul class="menu-list">
        <li>
            <a>
                <span class="icon"><i class="fas fa-music"></i></span>
                Genre
            </a>
        </li>
        <li>
            <a>
                <span class="icon"><i class="fas fa-smile"></i></span>
                Mood
            </a>
        </li>
        <li>
            <a>
                <span class="icon"><i class="fas fa-guitar"></i></span>
                Instrument
            </a>
        </li>
    </ul>
</aside>


<!-- Flash messages / alerts -->
<section class="section container">
    <article class="message is-info">
        <div class="message-header">
            <p>
                <span class="icon"><i class="fas fa-info-circle"></i></span>
                Info
            </p>
            <button class="delete" aria-label="delete"></button>
        </div>
        <div class="message-body">
            This is an informational flash message.
        </div>
    </article>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="content has-text-centered">
        <p>&copy; 2025 TFN Music Platform</p>
        <p>
            <a href="#top" class="button is-light">
                <span class="icon"><i class="fas fa-arrow-up"></i></span>
                Back to top
            </a>
        </p>
    </div>
</footer>

<!-- Optional JavaScript -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script>
    // navbar burger toggle
    $(document).ready(() => {
        $(".navbar-burger").click(() => {
            $(".navbar-burger, .navbar-menu").toggleClass("is-active");
        });
        // close flash message
        document.querySelectorAll('.message .delete').forEach(btn =>
            btn.addEventListener('click', () => btn.closest('.message').remove())
        );
    });
    console.log('welcome to the app');
</script>
</body>
</html>
