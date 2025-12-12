<?php
/**
 * Admin Orders/Reservations Management Page
 */
require_once __DIR__ . '/../Includes/db.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Reservation.php';

User::requireAdmin();

$reservation = new Reservation($db);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_status') {
        $reservationId = (int)($_POST['reservation_id'] ?? 0);
        $status = $_POST['status'] ?? 'pending';
        
        $result = $reservation->updateStatus($reservationId, $status);
        if ($result['success']) {
            $success = "Status succesvol bijgewerkt!";
        } else {
            $error = $result['message'] ?? "Fout bij bijwerken van status.";
        }
    }
}

$allReservations = $reservation->getAll();

include __DIR__ . '/../Includes/header.php';
?>
<div class="container">
    <div class="page-header">
        <h1>Reserveringen Beheer</h1>
        <a href="../admin.php" class="btn btn-secondary">← Terug</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h2>Alle Reserveringen</h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Gebruiker</th>
                            <th>Item</th>
                            <th>Datum/Tijd</th>
                            <th>Status</th>
                            <th>Locatie</th>
                            <th>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($allReservations)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Geen reserveringen gevonden.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($allReservations as $res): ?>
                                <tr>
                                    <td><?= $res['id'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($res['username']) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($res['email']) ?></small>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($res['item_name']) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($res['item_type'] ?? 'N/A') ?></small>
                                    </td>
                                    <td>
                                        <?= date('d-m-Y H:i', strtotime($res['reservation_date'])) ?>
                                        <?php if ($res['pickup_date']): ?>
                                            <br><small>Opgehaald: <?= date('d-m-Y H:i', strtotime($res['pickup_date'])) ?></small>
                                        <?php endif; ?>
                                        <?php if ($res['return_date']): ?>
                                            <br><small>Terug: <?= date('d-m-Y H:i', strtotime($res['return_date'])) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="reservation_id" value="<?= $res['id'] ?>">
                                            <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                                <option value="pending" <?= $res['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="confirmed" <?= $res['status'] === 'confirmed' ? 'selected' : '' ?>>Bevestigd</option>
                                                <option value="picked_up" <?= $res['status'] === 'picked_up' ? 'selected' : '' ?>>Opgehaald</option>
                                                <option value="returned" <?= $res['status'] === 'returned' ? 'selected' : '' ?>>Teruggebracht</option>
                                                <option value="cancelled" <?= $res['status'] === 'cancelled' ? 'selected' : '' ?>>Geannuleerd</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <?php if ($res['cabinet']): ?>
                                            <?= htmlspecialchars($res['cabinet']) ?>
                                            <?= $res['shelf'] ? ' - ' . htmlspecialchars($res['shelf']) : '' ?>
                                        <?php else: ?>
                                            <span class="text-muted">Geen locatie</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($res['notes']): ?>
                                            <span title="<?= htmlspecialchars($res['notes']) ?>">📝</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../Includes/footer.php'; ?>
