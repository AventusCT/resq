<?php
/**
 * Admin Dashboard for ResQ
 */
require_once __DIR__ . '/Includes/db.php';
require_once __DIR__ . '/classes/User.php';
require_once __DIR__ . '/classes/Item.php';
require_once __DIR__ . '/classes/Reservation.php';

User::requireAdmin();

$item = new Item($db);
$reservation = new Reservation($db);

// Get statistics
$allItems = $item->getAll();
$allReservations = $reservation->getAll();
$allUsers = (new User($db))->getAllUsers();

$stats = [
    'total_items' => count($allItems),
    'available_items' => count($item->getAll('available')),
    'reserved_items' => count($item->getAll('reserved')),
    'defective_items' => count($item->getAll('defective')),
    'total_reservations' => count($allReservations),
    'active_reservations' => count(array_filter($allReservations, function($r) {
        return in_array($r['status'], ['pending', 'confirmed', 'picked_up']);
    })),
    'total_users' => count($allUsers),
];

include __DIR__ . '/Includes/header.php';
?>
<div class="container">
    <div class="page-header">
        <h1>Beheer Dashboard</h1>
        <p>Welkom, <?= htmlspecialchars($_SESSION['username']) ?> (Beheerder)</p>
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card stat-card-blue">
            <div class="stat-icon">📦</div>
            <div class="stat-content">
                <div class="stat-label">Totaal Items</div>
                <div class="stat-value"><?= $stats['total_items'] ?></div>
            </div>
        </div>
        <div class="stat-card stat-card-green">
            <div class="stat-icon">✅</div>
            <div class="stat-content">
                <div class="stat-label">Beschikbaar</div>
                <div class="stat-value"><?= $stats['available_items'] ?></div>
            </div>
        </div>
        <div class="stat-card stat-card-orange">
            <div class="stat-icon">📅</div>
            <div class="stat-content">
                <div class="stat-label">Actieve Reserveringen</div>
                <div class="stat-value"><?= $stats['active_reservations'] ?></div>
            </div>
        </div>
        <div class="stat-card stat-card-red">
            <div class="stat-icon">👥</div>
            <div class="stat-content">
                <div class="stat-label">Gebruikers</div>
                <div class="stat-value"><?= $stats['total_users'] ?></div>
            </div>
        </div>
    </div>

    <!-- Admin Links -->
    <div class="admin-links">
        <div class="admin-link-card">
            <div class="link-icon">👥</div>
            <h3>Gebruikersbeheer</h3>
            <p>Beheer gebruikers en rollen</p>
            <a href="beheer/admin_users.php" class="btn btn-primary">Openen</a>
        </div>
        <div class="admin-link-card">
            <div class="link-icon">📦</div>
            <h3>Productbeheer</h3>
            <p>Beheer items en inventaris</p>
            <a href="beheer/admin_products.php" class="btn btn-primary">Openen</a>
        </div>
        <div class="admin-link-card">
            <div class="link-icon">📋</div>
            <h3>Reserveringen</h3>
            <p>Bekijk alle reserveringen</p>
            <a href="beheer/admin_orders.php" class="btn btn-primary">Openen</a>
        </div>
        <div class="admin-link-card">
            <div class="link-icon">🏢</div>
            <h3>Magazijnbeheer</h3>
            <p>Beheer magazijn en locaties</p>
            <a href="warehouse.php" class="btn btn-primary">Openen</a>
        </div>
    </div>
</div>
<?php include __DIR__ . '/Includes/footer.php'; ?>
