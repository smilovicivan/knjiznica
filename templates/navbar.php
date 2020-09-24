<nav class="navbar navbar-light navbar-expand-md bg-dark justify-content-center">
    <div class="color navbar-collapse collapse justify-content-between align-items-center w-100" id="collapsingNavbar2">
        <ul class="navbar-nav mx-auto text-center">
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $path ?>index.php">Naslovna</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $path ?>private/book/books.php">Popis knjiga</a>
            </li>
            <?php if (isset($_SESSION['is_logged_in'])): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $path ?>private/book/borrowed-books.php">Posudbe</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $path ?>private/users/all-memberships.php">Članarine</a>
            </li>
            <?php endif; ?>
            <?php if (isset($_SESSION["is_logged_in"]) && $_SESSION["is_logged_in"]->role === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $path ?>private/users/users.php">Korisnici</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $path ?>private/book/zanr.php">Žanrovi</a>
                </li>
            <?php endif; ?>
            <?php if (isset($_SESSION["is_logged_in"])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $path ?>logout.php">Odjava <?php echo $_SESSION["is_logged_in"]->firstname ?></a>
                </li>
                <li class="nav-item sub-btn">
                    
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $path ?>login.php">Prijava</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>