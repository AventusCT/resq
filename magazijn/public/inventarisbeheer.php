<?php
/**
 * Inventory Management Page for ResQ
 * Admin can add/edit/delete items, generate QR codes
 */
require_once __DIR__ . '/Includes/db.php';
require_once __DIR__ . '/classes/User.php';
require_once __DIR__ . '/classes/Item.php';

User::requireLogin();

$item = new Item($db);
$error = '';
$success = '';
$selectedItem = null;

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add' && User::isAdmin()) {
        $name = trim($_POST['name'] ?? '');
        $type = trim($_POST['type'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $locationId = !empty($_POST['location_id']) ? (int)$_POST['location_id'] : null;
        $warehouseId = !empty($_POST['warehouse_id']) ? (int)$_POST['warehouse_id'] : null;
        
        if (empty($name)) {
            $error = "Naam is verplicht.";
        } else {
            $itemId = $item->create($name, $type, $description, $locationId, $warehouseId);
            if ($itemId) {
                $success = "Item succesvol toegevoegd!";
            } else {
                $error = "Fout bij toevoegen van item.";
            }
        }
    } elseif ($action === 'update' && User::isAdmin()) {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $type = trim($_POST['type'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $status = $_POST['status'] ?? 'available';
        $locationId = !empty($_POST['location_id']) ? (int)$_POST['location_id'] : null;
        $warehouseId = !empty($_POST['warehouse_id']) ? (int)$_POST['warehouse_id'] : null;
        
        if ($item->update($id, $name, $type, $description, $status, $locationId, $warehouseId)) {
            $success = "Item succesvol bijgewerkt!";
        } else {
            $error = "Fout bij bijwerken van item.";
        }
    } elseif ($action === 'delete' && User::isAdmin()) {
        $id = (int)($_POST['id'] ?? 0);
        if ($item->delete($id)) {
            $success = "Item succesvol verwijderd!";
        } else {
            $error = "Fout bij verwijderen van item.";
        }
    } elseif ($action === 'regenerate_qr' && User::isAdmin()) {
        $id = (int)($_POST['id'] ?? 0);
        if ($item->regenerateQRCode($id)) {
            $success = "QR code succesvol opnieuw gegenereerd!";
        } else {
            $error = "Fout bij genereren van QR code.";
        }
    }
}

// Get selected item for editing
if (isset($_GET['edit'])) {
    $selectedItem = $item->getById((int)$_GET['edit']);
}

// Get all items
$allItems = $item->getAll();

// Get locations and warehouses for dropdowns
$locationsQuery = "SELECT l.*, w.name as warehouse_name FROM locations l 
                   LEFT JOIN warehouses w ON l.warehouse_id = w.id 
                   ORDER BY w.name, l.cabinet, l.shelf";
$locationsStmt = $db->prepare($locationsQuery);
$locationsStmt->execute();
$locations = $locationsStmt->fetchAll(PDO::FETCH_ASSOC);

$warehousesQuery = "SELECT * FROM warehouses ORDER BY name";
$warehousesStmt = $db->prepare($warehousesQuery);
$warehousesStmt->execute();
$warehouses = $warehousesStmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/Includes/header.php';
?>
<div class="container">
    <div class="page-header">
        <h1>Inventarisbeheer</h1>
        <?php if (User::isAdmin()): ?>
            <button class="btn btn-primary" onclick="showAddForm()">+ Nieuw Item Toevoegen</button>
        <?php endif; ?>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Add/Edit Form (Admin Only) -->
    <?php if (User::isAdmin()): ?>
        <div class="card" id="itemForm" style="<?= $selectedItem ? '' : 'display:none;' ?>">
            <div class="card-header">
                <h2><?= $selectedItem ? 'Item Bewerken' : 'Nieuw Item Toevoegen' ?></h2>
            </div>
            <div class="card-body">
                <form method="POST" action="inventarisbeheer.php">
                    <input type="hidden" name="action" value="<?= $selectedItem ? 'update' : 'add' ?>">
                    <?php if ($selectedItem): ?>
                        <input type="hidden" name="id" value="<?= $selectedItem['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="name">Naam *</label>
                        <input type="text" name="name" id="name" class="form-control" 
                               value="<?= htmlspecialchars($selectedItem['name'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="type">Type</label>
                        <input type="text" name="type" id="type" class="form-control" 
                               value="<?= htmlspecialchars($selectedItem['type'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Beschrijving</label>
                        <textarea name="description" id="description" class="form-control" rows="3"><?= htmlspecialchars($selectedItem['description'] ?? '') ?></textarea>
                    </div>
                    
                    <?php if ($selectedItem): ?>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="available" <?= ($selectedItem['status'] ?? '') === 'available' ? 'selected' : '' ?>>Beschikbaar</option>
                                <option value="reserved" <?= ($selectedItem['status'] ?? '') === 'reserved' ? 'selected' : '' ?>>Gereserveerd</option>
                                <option value="picked_up" <?= ($selectedItem['status'] ?? '') === 'picked_up' ? 'selected' : '' ?>>Opgehaald</option>
                                <option value="defective" <?= ($selectedItem['status'] ?? '') === 'defective' ? 'selected' : '' ?>>Defect</option>
                                <option value="maintenance" <?= ($selectedItem['status'] ?? '') === 'maintenance' ? 'selected' : '' ?>>Onderhoud</option>
                            </select>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="warehouse_id">Magazijn</label>
                        <select name="warehouse_id" id="warehouse_id" class="form-control">
                            <option value="">-- Selecteer magazijn --</option>
                            <?php foreach ($warehouses as $wh): ?>
                                <option value="<?= $wh['id'] ?>" 
                                        <?= ($selectedItem['warehouse_id'] ?? '') == $wh['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($wh['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="location_id">Locatie</label>
                        <select name="location_id" id="location_id" class="form-control">
                            <option value="">-- Selecteer locatie --</option>
                            <?php foreach ($locations as $loc): ?>
                                <option value="<?= $loc['id'] ?>" 
                                        <?= ($selectedItem['location_id'] ?? '') == $loc['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($loc['warehouse_name'] ?? '') ?> - 
                                    <?= htmlspecialchars($loc['cabinet']) ?>
                                    <?= $loc['shelf'] ? ' - ' . htmlspecialchars($loc['shelf']) : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary"><?= $selectedItem ? 'Bijwerken' : 'Toevoegen' ?></button>
                        <button type="button" class="btn btn-secondary" onclick="hideForm()">Annuleren</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- Items Table -->
    <div class="card">
        <div class="card-header">
            <h2>Items Overzicht</h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Naam</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Locatie</th>
                            <th>QR Code</th>
                            <?php if (User::isAdmin()): ?>
                                <th>Acties</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($allItems)): ?>
                            <tr>
                                <td colspan="<?= User::isAdmin() ? '7' : '6' ?>" class="text-center text-muted">
                                    Geen items gevonden.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($allItems as $itm): ?>
                                <tr>
                                    <td><?= $itm['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($itm['name']) ?></strong></td>
                                    <td><?= htmlspecialchars($itm['type'] ?? 'N/A') ?></td>
                                    <td>
                                        <span class="status-badge status-<?= $itm['status'] ?>">
                                            <?= ucfirst(str_replace('_', ' ', $itm['status'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($itm['cabinet']): ?>
                                            <?= htmlspecialchars($itm['cabinet']) ?>
                                            <?= $itm['shelf'] ? ' - ' . htmlspecialchars($itm['shelf']) : '' ?>
                                        <?php else: ?>
                                            <span class="text-muted">Geen locatie</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($itm['qr_code']): ?>
                                            <a href="<?= htmlspecialchars($itm['qr_code_path'] ?? '#') ?>" 
                                               target="_blank" class="qr-link" title="<?= htmlspecialchars($itm['qr_code']) ?>">
                                                📷 QR Code
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Geen QR</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php if (User::isAdmin()): ?>
                                        <td class="actions">
                                            <a href="?edit=<?= $itm['id'] ?>" class="btn-icon" title="Bewerken">✏️</a>
                                            <form method="POST" style="display:inline;" 
                                                  onsubmit="return confirm('Weet u zeker dat u dit item wilt verwijderen?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $itm['id'] ?>">
                                                <button type="submit" class="btn-icon" title="Verwijderen">🗑️</button>
                                            </form>
                                            <?php if ($itm['qr_code']): ?>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="action" value="regenerate_qr">
                                                    <input type="hidden" name="id" value="<?= $itm['id'] ?>">
                                                    <button type="submit" class="btn-icon" title="QR Code Opnieuw Genereren">🔄</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function showAddForm() {
    document.getElementById('itemForm').style.display = 'block';
    document.getElementById('itemForm').scrollIntoView({ behavior: 'smooth' });
}

function hideForm() {
    document.getElementById('itemForm').style.display = 'none';
    window.location.href = 'inventarisbeheer.php';
}
</script>
<?php include __DIR__ . '/Includes/footer.php'; ?>
