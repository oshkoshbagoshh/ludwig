<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TFN Music Platform</title>
    <!-- Bulma CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Hero background */
        .hero {
            background: url('https://images.pexels.com/photos/164849/pexels-photo-164849.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260') no-repeat center/cover;
        }

        /* Carousel */
        .carousel {
            display: flex;
            overflow-x: auto;
            gap: 1rem;
            scroll-snap-type: x mandatory;
            padding-bottom: 1rem;
        }

        .carousel-item {
            flex: 0 0 auto;
            scroll-snap-align: center;
            width: 200px;
        }
    </style>
</head>
<body id="top">
<!-- NAVBAR -->
<nav class="navbar is-light" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <a class="navbar-item" href="/">
            <img src="./img/kp-logo.png" alt="TFN Music Logo" width="112" height="28">
        </a>
        <button class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarMenu">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </button>
    </div>
    <div id="navbarMenu" class="navbar-menu">
        <div class="navbar-start">
            <a class="navbar-item" href="#albums">Albums</a>
            <a class="navbar-item" href="#featured-tracks">Tracks</a>
            <a class="navbar-item" href="#artist-of-week">Artist</a>
        </div>
        <div class="navbar-end">
            <div class="navbar-item">
                <div class="buttons">
                    <a class="button is-primary"><strong>Sign up</strong></a>
                    <a class="button is-light">Log in</a>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero is-medium is-dark">
    <div class="hero-body">
        <div class="container has-text-white">
            <h1 class="title">Welcome to TFN Music</h1>
            <h2 class="subtitle">Your source for CTV music & advertising tracks</h2>
        </div>
    </div>
</section>


<!-- MAIN CONTENT -->
<main class="section">
    <div class="container">
        <div class="columns">
            <!-- Left column -->
            <div class="column is-three-quarters">
                <!-- Albums -->
                <section id="albums" class="mb-6">
                    <h2 class="title is-4"><i class="fas fa-compact-disc"></i> Albums</h2>
                    <div class="columns is-multiline">
                        <div class="column is-one-quarter">
                            <div class="card">
                                <div class="card-image">
                                    <figure class="image is-4by3">
                                        <img src="https://images.pexels.com/photos/164853/pexels-photo-164853.jpeg?auto=compress&cs=tinysrgb&h=300"
                                             alt="Album cover">
                                    </figure>
                                </div>
                                <div class="card-content">
                                    <p class="title is-6">Album Title</p>
                                    <p class="subtitle is-7">Artist Name</p>
                                </div>
                            </div>
                        </div>
                        <!-- Add more album cards or placeholders here -->
                    </div>
                </section>

                <!-- Featured Tracks -->
                <section id="featured-tracks" class="mb-6">
                    <h2 class="title is-4"><i class="fas fa-music"></i> Featured Tracks</h2>
                    <div class="carousel">
                        <div class="carousel-item card">
                            <div class="card-image">
                                <figure class="image is-4by3">
                                    <img src="https://images.pexels.com/photos/164835/pexels-photo-164835.jpeg?auto=compress&cs=tinysrgb&h=200"
                                         alt="Track artwork">
                                </figure>
                            </div>
                            <div class="card-content">
                                <p class="title is-6">Track Title</p>
                                <p class="subtitle is-7">Artist Name</p>
                            </div>
                        </div>
                        <!-- Repeat for up to 5 items -->
                    </div>
                </section>

                <!-- Artist of the Week -->
                <section id="artist-of-week" class="mb-6">
                    <h2 class="title is-4"><i class="fas fa-star"></i> TFN Artist of the Week</h2>
                    <div class="box">
                        <article class="media">
                            <figure class="media-left">
                                <p class="image is-64x64">
                                    <img src="https://images.pexels.com/photos/39958/pexels-photo-39958.jpeg?auto=compress&cs=tinysrgb&h=64"
                                         alt="Artist">
                                </p>
                            </figure>
                            <div class="media-content">
                                <div class="content">
                                    <p>
                                        <strong>Artist Name</strong> <small>@artist</small><br>
                                        A brief bio or highlight of the featured artist.
                                    </p>
                                </div>
                                <nav class="level is-mobile">
                                    <div class="level-left">
                                        <a class="level-item" aria-label="Follow"><i class="fas fa-user-plus"></i></a>
                                        <a class="level-item" aria-label="Share"><i class="fas fa-share-alt"></i></a>
                                    </div>
                                </nav>
                            </div>
                        </article>
                    </div>
                </section>

                <!-- Flash Message -->
                <div class="notification is-info is-light">
                    <button class="delete" aria-label="Close"></button>
                    This is an informational flash message.
                </div>
            </div>

            <!-- Right column (filters) -->
            <aside class="column is-one-quarter">
                <nav class="menu">
                    <p class="menu-label"><i class="fas fa-filter"></i> Filters</p>
                    <ul class="menu-list">
                        <li><a><i class="fas fa-music"></i> Genre</a></li>
                        <li><a><i class="fas fa-smile"></i> Mood</a></li>
                        <li><a><i class="fas fa-guitar"></i> Instrument</a></li>
                    </ul>
                </nav>
            </aside>
        </div>
    </div>
</main>

<!-- FOOTER -->
<footer class="footer">
    <div class="content has-text-centered">
        <p>&copy; 2025 TFN Music Platform</p>
        <a href="#top" class="button is-light">
            <span class="icon"><i class="fas fa-arrow-up"></i></span>
            <span>Back to top</span>
        </a>
    </div>
</footer>

<!-- JS -->
<script defer src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script defer>
    document.addEventListener('DOMContentLoaded', () => {
        // Navbar burger toggle
        const burger = document.querySelector('.navbar-burger');
        const menu = document.getElementById(burger.dataset.target);
        burger.addEventListener('click', () => {
            burger.classList.toggle('is-active');
            menu.classList.toggle('is-active');
        });

        // Flash message close
        document.querySelectorAll('.notification .delete').forEach(btn =>
            btn.addEventListener('click', () =>
                btn.parentElement.remove()
            )
        );
    });
</script>
</body>
</html>