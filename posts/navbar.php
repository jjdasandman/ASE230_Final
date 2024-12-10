<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="/Final2/index.php">Closet Manager</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link active" href="/Final2/posts/index.php">Home</a></li>
                <li class="nav-item special"><a class="nav-link" href="/Final2/profile/index.php">Profile</a></li>
                <?php if (isLoggedIn()) { ?>
                    <li class="nav-item special"><a class="nav-link" href="/Final2/posts/create.php">Create New Post</a></li>
                    <li class="nav-item special"><a class="nav-link" href="/Final2/profile/settings.php">Settings</a></li>
                <?php } ?>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php if (isLoggedIn()) { ?>
                    <?php if (isset($_SESSION['isValidAdmin']) && $_SESSION['isValidAdmin']) { ?>
                        <li class="nav-item"><a href="/Final2/admin.php" class="nav-link">Admin Page</a></li>
                    <?php } ?>
                    <li class="nav-item sign-out"><a href="/Final2/auth/logout.php" class="nav-link">Sign Out</a></li>
                <?php } else { ?>
                    <li class="nav-item"><a href="/Final2/auth/login.php" class="nav-link">Sign In</a></li>
                    <li class="nav-item"><a href="/Final2/auth/signup.php" class="nav-link">Sign Up</a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>