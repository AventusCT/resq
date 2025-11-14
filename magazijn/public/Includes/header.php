<nav class="navbar">
    <div class="container" style="display: flex; align-items: center; justify-content: space-between;">
        <a class="navbar-brand" href="account.php" style="margin-right: 16px;">ResQ</a>
        <div style="flex: 1;">
            <div style="display: flex; align-items: center; gap: 24px;">
                <?php if (isset($_SESSION['user'])): ?>
                    <?php $user = unserialize($_SESSION['user']); ?>
                    <a class="nav-link" href="inventarisbeheer.php" style="margin-right: 16px;">Inventarisbeheer</a>
                    <a class="nav-link" href="warehouse.php" style="margin-right: 16px;">Warenhuisplaatsing</a>
                    <a class="nav-link" href="reservation.php" style="margin-right: 16px;">Status Veranderen</a>
                    <a class="nav-link" href="index.php" style="margin-right: 16px;">Index</a>
                    <?php if ($user->getRole() === 'admin'): ?>
                        <a class="nav-link" href="admin.php" style="margin-right: 16px;">AdminPanel</a>
                    <?php endif; ?>
                    <a class="nav-link" href="logout.php" style="margin-right: 16px;">Uitloggen</a>
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