<?php
/**
 * Warehouse Overview Page for ResQ
 * Visual representation of warehouse layout
 */
require_once __DIR__ . '/Includes/db.php';
require_once __DIR__ . '/classes/User.php';
require_once __DIR__ . '/classes/Item.php';

User::requireLogin();

$item = new Item($db);

// Get all items with locations
$allItems = $item->getAll();

// Get locations
$locationsQuery = "SELECT l.*, w.name as warehouse_name, 
                   COUNT(i.id) as item_count
                   FROM locations l
                   LEFT JOIN warehouses w ON l.warehouse_id = w.id
                   LEFT JOIN items i ON i.location_id = l.id AND i.status != 'defective'
                   GROUP BY l.id
                   ORDER BY w.name, l.cabinet, l.shelf";
$locationsStmt = $db->prepare($locationsQuery);
$locationsStmt->execute();
$locations = $locationsStmt->fetchAll(PDO::FETCH_ASSOC);

// Get warehouses
$warehousesQuery = "SELECT * FROM warehouses ORDER BY name";
$warehousesStmt = $db->prepare($warehousesQuery);
$warehousesStmt->execute();
$warehouses = $warehousesStmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/Includes/header.php';
?>
<div class="container">
    <div class="page-header">
        <h1>Magazijn Overzicht</h1>
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card stat-card-blue">
            <div class="stat-icon">📦</div>
            <div class="stat-content">
                <div class="stat-label">Totaal Items</div>
                <div class="stat-value" id="kpi-total"><?= count($allItems) ?></div>
            </div>
        </div>
        <div class="stat-card stat-card-green">
            <div class="stat-icon">✅</div>
            <div class="stat-content">
                <div class="stat-label">Beschikbaar</div>
                <div class="stat-value" id="kpi-available"><?= count($item->getAll('available')) ?></div>
            </div>
        </div>
        <div class="stat-card stat-card-orange">
            <div class="stat-icon">📅</div>
            <div class="stat-content">
                <div class="stat-label">Gereserveerd</div>
                <div class="stat-value" id="kpi-reserved"><?= count($item->getAll('reserved')) ?></div>
            </div>
        </div>
        <div class="stat-card stat-card-red">
            <div class="stat-icon">⚠️</div>
            <div class="stat-content">
                <div class="stat-label">Bezet</div>
                <div class="stat-value" id="kpi-busy"><?= count($item->getAll('picked_up')) ?></div>
            </div>
        </div>
    </div>

    <!-- Warehouse Layout -->
    <div class="card">
        <div class="card-header">
            <h2>Magazijn Layout</h2>
        </div>
        <div class="card-body">
            <div id="warehouse" class="warehouse-layout">
                <?php if (empty($warehouses)): ?>
                    <p class="text-muted">Geen magazijnen gevonden.</p>
                <?php else: ?>
                    <?php foreach ($warehouses as $warehouse): ?>
                        <div class="warehouse-zone">
                            <h3 class="zone-title"><?= htmlspecialchars($warehouse['name']) ?></h3>
                            <div class="locations-grid">
                                <?php
                                $warehouseLocations = array_filter($locations, function($loc) use ($warehouse) {
                                    return $loc['warehouse_id'] == $warehouse['id'];
                                });
                                
                                if (empty($warehouseLocations)): ?>
                                    <p class="text-muted">Geen locaties toegewezen.</p>
                                <?php else: ?>
                                    <?php foreach ($warehouseLocations as $loc): ?>
                                        <?php
                                        $locationItems = array_filter($allItems, function($itm) use ($loc) {
                                            return $itm['location_id'] == $loc['id'];
                                        });
                                        $itemCount = count($locationItems);
                                        $statusClass = $itemCount === 0 ? 'empty' : ($itemCount === 1 ? 'available' : 'full');
                                        ?>
                                        <div class="location-slot location-<?= $statusClass ?>" 
                                             data-location-id="<?= $loc['id'] ?>"
                                             title="<?= htmlspecialchars($loc['cabinet']) ?> <?= htmlspecialchars($loc['shelf'] ?? '') ?>">
                                            <div class="slot-code"><?= htmlspecialchars($loc['cabinet']) ?></div>
                                            <?php if ($loc['shelf']): ?>
                                                <div class="slot-shelf"><?= htmlspecialchars($loc['shelf']) ?></div>
                                            <?php endif; ?>
                                            <div class="slot-count"><?= $itemCount ?> items</div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="legend">
                <span class="legend-item">
                    <span class="legend-dot legend-dot-green"></span> Beschikbaar (1 item)
                </span>
                <span class="legend-item">
                    <span class="legend-dot legend-dot-red"></span> Vol (2+ items)
                </span>
                <span class="legend-item">
                    <span class="legend-dot legend-dot-grey"></span> Leeg
                </span>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="card">
        <div class="card-header">
            <h2>Item Zoeken</h2>
        </div>
        <div class="card-body">
            <div class="form-group">
                <input type="text" id="search-input" class="form-control" 
                       placeholder="Zoek op naam, type of QR code...">
            </div>
            <div id="search-results" class="search-results"></div>
        </div>
    </div>
</div>

<style>
.warehouse-layout {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.warehouse-zone {
    margin-bottom: 2rem;
}

.zone-title {
    font-size: 1.5rem;
    color: var(--primary-blue);
    margin-bottom: 1rem;
    font-weight: 700;
}

.locations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
}

.location-slot {
    background: var(--white);
    border: 2px solid var(--border-color);
    border-radius: 8px;
    padding: 1rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    min-height: 100px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.location-slot:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.location-available {
    border-color: var(--success-green);
    background-color: #D4EDDA;
}

.location-full {
    border-color: var(--danger-red);
    background-color: #F8D7DA;
}

.location-empty {
    border-color: var(--gray);
    background-color: var(--light-gray);
}

.slot-code {
    font-weight: 700;
    font-size: 1.2rem;
    color: var(--dark-gray);
    margin-bottom: 0.25rem;
}

.slot-shelf {
    font-size: 0.875rem;
    color: var(--gray);
    margin-bottom: 0.5rem;
}

.slot-count {
    font-size: 0.75rem;
    color: var(--gray);
    margin-top: 0.5rem;
}

.legend {
    display: flex;
    gap: 2rem;
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
    flex-wrap: wrap;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--gray);
}

.legend-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
}

.legend-dot-green {
    background-color: var(--success-green);
}

.legend-dot-red {
    background-color: var(--danger-red);
}

.legend-dot-grey {
    background-color: var(--gray);
}

.search-results {
    margin-top: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.search-result-item {
    padding: 1rem;
    background: var(--light-gray);
    border-radius: 4px;
    border-left: 4px solid var(--primary-blue);
}

.search-result-item:hover {
    background: var(--white);
    box-shadow: var(--shadow);
}

@media (max-width: 768px) {
    .locations-grid {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    }
}
</style>

<script>
// Warehouse search functionality
const allItems = <?= json_encode($allItems) ?>;
const allLocations = <?= json_encode($locations) ?>;

document.getElementById('search-input').addEventListener('input', function(e) {
    const query = e.target.value.toLowerCase().trim();
    const resultsDiv = document.getElementById('search-results');
    
    if (!query) {
        resultsDiv.innerHTML = '';
        return;
    }
    
    const results = allItems.filter(item => {
        return item.name.toLowerCase().includes(query) ||
               (item.type && item.type.toLowerCase().includes(query)) ||
               (item.qr_code && item.qr_code.toLowerCase().includes(query));
    });
    
    if (results.length === 0) {
        resultsDiv.innerHTML = '<p class="text-muted">Geen resultaten gevonden.</p>';
        return;
    }
    
    resultsDiv.innerHTML = results.map(item => {
        const location = allLocations.find(loc => loc.id == item.location_id);
        const locationStr = location ? `${location.cabinet}${location.shelf ? ' - ' + location.shelf : ''}` : 'Geen locatie';
        
        return `
            <div class="search-result-item">
                <strong>${escapeHtml(item.name)}</strong>
                <div class="text-muted" style="margin-top: 0.25rem;">
                    Type: ${escapeHtml(item.type || 'N/A')} | 
                    Status: ${escapeHtml(item.status)} | 
                    Locatie: ${escapeHtml(locationStr)}
                </div>
            </div>
        `;
    }).join('');
});

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
<?php include __DIR__ . '/Includes/footer.php'; ?>
