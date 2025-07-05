<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($viewData['title']) ? sanitize($viewData['title']) : 'Music Platform' ?></title>
    
    <!-- Bulma CSS via CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    
    <!-- jQuery via CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/custom.css">
</head>
<body>
    <nav class="navbar is-dark" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <a class="navbar-item" href="/">
                Music Platform
            </a>
        </div>
        
        <div class="navbar-menu">
            <div class="navbar-start">
                <a class="navbar-item" href="/songs">Songs</a>
                <a class="navbar-item" href="/artists">Artists</a>
                <a class="navbar-item" href="/upload">Upload</a>
            </div>
        </div>
    </nav>

    <main class="section">
        <div class="container">
            <?php if (isset($viewData['error'])): ?>
                <div class="notification is-danger">
                    <?= sanitize($viewData['error']) ?>
                </div>
            <?php endif; ?>
            
            <?php require $content ?? VIEW_PATH . '/404.php'; ?>
        </div>
    </main>

    <footer class="footer">
        <div class="content has-text-centered">
            <p>Music Platform &copy; <?= date('Y') ?></p>
        </div>
    </footer>
</body>
</html>