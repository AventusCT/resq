<header class="bg-gray-800 text-white px-8 py-4 flex justify-between items-center sticky top-0 z-50 shadow">
    <h1 class="text-2xl font-bold">Welkom op bij ResQ</h1>

    <nav>
        <ul class="flex gap-6">

            <li><a href="index.php" class="hover:text-yellow-400">Home</a></li>

            <?php if (isset($_SESSION['user'])):
                $user = is_string($_SESSION['user']) ? unserialize($_SESSION['user']) : $_SESSION['user'];
            ?>

                <li><a href="inventarisbeheer.php" class="hover:text-yellow-400">Inventarisbeheer</a></li>
                <li><a href="warehouse.php" class="hover:text-yellow-400">Warenhuisplaatsing</a></li>
                <li><a href="reservation.php" class="hover:text-yellow-400">Status Veranderen</a></li>

                <?php if (method_exists($user, 'getRole') && $user->getRole() === 'admin'): ?>
                    <li><a href="admin.php" class="hover:text-yellow-400">AdminPanel</a></li>
                <?php endif; ?>

                <li><a href="logout.php" class="hover:text-yellow-400">Uitloggen</a></li>

            <?php else: ?>

                <li><a href="login.php" class="hover:text-yellow-400">Inloggen</a></li>
                <li><a href="register.php" class="hover:text-yellow-400">Registreren</a></li>

            <?php endif; ?>

        </ul>
    </nav>

    <?php if (isset($user) && method_exists($user, 'getName')): ?>
        <span class="ml-6 text-yellow-300 font-semibold">
            Welkom, <?= htmlspecialchars($user->getName()); ?>
        </span>
    <?php endif; ?>
</header>
