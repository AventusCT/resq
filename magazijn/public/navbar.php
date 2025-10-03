<nav class="navbar">
    <div class="container" style="display: flex; align-items: center; justify-content: space-between;">
        <a class="navbar-brand" href="account.php" style="margin-right: 16px;">Webwinkel L.D.H</a>
        <div style="flex: 1;">
            <div style="display: flex; align-items: center; gap: 24px;">
                <?php if (isset($_SESSION['user'])): ?>
                    <?php $user = unserialize($_SESSION['user']); ?>
                    <a class="nav-link" href="account.php" style="margin-right: 16px;">Account</a>
                    <a class="nav-link" href="bestellen.php" style="margin-right: 16px;">Bestellen</a>
                    <a class="nav-link" href="feedback.php" style="margin-right: 16px;">Feedback</a>
                    <?php if ($user->getRole() === 'admin'): ?>
                        <a class="nav-link" href="itemadmin.php" style="margin-right: 16px;">Productbeheer</a>
                        <a class="nav-link" href="feedbackadmin.php" style="margin-right: 16px;">Feedbackbeheer</a>
                    <?php endif; ?>
                    <a class="nav-link" href="logout.php" style="margin-right: 16px;">Uitloggen</a>
                <?php else: ?>
                    <a class="nav-link" href="login.php" style="margin-right: 16px;">Inloggen</a>
                    <a class="nav-link" href="registratie.php" style="margin-right: 16px;">Registreren</a>
                <?php endif; ?>
            </div>
        </div>
        <?php if (isset($_SESSION['user'])): ?>
            <?php $user = unserialize($_SESSION['user']); ?>
            <span class="navbar-text text-light" style="margin-left: 16px;">
                Welkom, <?= htmlspecialchars($user->getName()); ?>
            </span>
        <?php endif; ?>
    </div>
</nav>