<?php
/**
 * Main Dashboard for ResQ
 */
require_once __DIR__ . '/Includes/db.php';
require_once __DIR__ . '/classes/User.php';
require_once __DIR__ . '/classes/Item.php';
require_once __DIR__ . '/classes/Reservation.php';

User::requireLogin();

$item = new Item($db);
$reservation = new Reservation($db);

// Get statistics
$allItems = $item->getAll();
$availableItems = $item->getAll('available');
$reservedItems = $item->getAll('reserved');
$pickedUpItems = $item->getAll('picked_up');
$defectiveItems = $item->getAll('defective');

// Get user's reservations
$userId = $_SESSION['user_id'];
$userReservations = $reservation->getByUserId($userId);
$upcomingReservations = array_filter($userReservations, function($r) {
    return in_array($r['status'], ['pending', 'confirmed', 'picked_up']) && 
           strtotime($r['reservation_date']) >= time();
});

// Get recent items
$recentItems = array_slice($allItems, 0, 10);

include __DIR__ . '/Includes/header.php';
?>
<div class="container">
    <div class="dashboard-header">
        <h1>Dashboard</h1>
        <p>Welkom terug, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card stat-card-blue">
            <div class="stat-icon">📦</div>
            <div class="stat-content">
                <div class="stat-label">Totaal Items</div>
                <div class="stat-value"><?= count($allItems) ?></div>
            </div>
        </div>
        <div class="stat-card stat-card-green">
            <div class="stat-icon">✅</div>
            <div class="stat-content">
                <div class="stat-label">Beschikbaar</div>
                <div class="stat-value"><?= count($availableItems) ?></div>
            </div>
        </div>
        <div class="stat-card stat-card-orange">
            <div class="stat-icon">📅</div>
            <div class="stat-content">
                <div class="stat-label">Gereserveerd</div>
                <div class="stat-value"><?= count($reservedItems) ?></div>
            </div>
        </div>
        <div class="stat-card stat-card-red">
            <div class="stat-icon">⚠️</div>
            <div class="stat-content">
                <div class="stat-label">Defect</div>
                <div class="stat-value"><?= count($defectiveItems) ?></div>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Upcoming Reservations -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2>Mijn Aankomende Reserveringen</h2>
            </div>
            <div class="card-body">
                <?php if (empty($upcomingReservations)): ?>
                    <p class="text-muted">Geen aankomende reserveringen.</p>
                <?php else: ?>
                    <div class="reservation-list">
                        <?php foreach (array_slice($upcomingReservations, 0, 5) as $res): ?>
                            <div class="reservation-item">
                                <div class="reservation-info">
                                    <strong><?= htmlspecialchars($res['item_name']) ?></strong>
                                    <span class="reservation-date">
                                        <?= date('d-m-Y H:i', strtotime($res['reservation_date'])) ?>
                                    </span>
                                </div>
                                <span class="status-badge status-<?= $res['status'] ?>">
                                    <?= ucfirst($res['status']) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="reservation.php" class="btn btn-link">Bekijk alle reserveringen</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Available Items -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2>Beschikbare Items</h2>
            </div>
            <div class="card-body">
                <?php if (empty($availableItems)): ?>
                    <p class="text-muted">Geen beschikbare items.</p>
                <?php else: ?>
                    <div class="item-list">
                        <?php foreach (array_slice($availableItems, 0, 5) as $itm): ?>
                            <div class="item-item">
                                <div class="item-info">
                                    <strong><?= htmlspecialchars($itm['name']) ?></strong>
                                    <span class="item-type"><?= htmlspecialchars($itm['type'] ?? 'N/A') ?></span>
                                </div>
                                <?php if ($itm['cabinet']): ?>
                                    <span class="item-location">
                                        <?= htmlspecialchars($itm['cabinet']) ?>
                                        <?= $itm['shelf'] ? ' - ' . htmlspecialchars($itm['shelf']) : '' ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="inventarisbeheer.php" class="btn btn-link">Bekijk alle items</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h2>Snelle Acties</h2>
        <div class="action-buttons">
            <a href="reservation.php" class="btn btn-primary">
                <span class="btn-icon">📅</span>
                Nieuwe Reservering
            </a>
            <a href="reservation.php?action=scan" class="btn btn-primary">
                <span class="btn-icon">📷</span>
                QR Code Scannen
            </a>
            <a href="inventarisbeheer.php" class="btn btn-secondary">
                <span class="btn-icon">📦</span>
                Inventaris Beheren
            </a>
            <a href="warehouse.php" class="btn btn-secondary">
                <span class="btn-icon">🏢</span>
                Magazijn Overzicht
            </a>
        </div>
    </div>
</div>
<?php include __DIR__ . '/Includes/footer.php'; ?>
