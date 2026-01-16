<?php
/**
 * Reservation Page for ResQ
 * Employees can reserve items, scan QR codes to pick up/return
 */
require_once __DIR__ . '/Includes/db.php';
require_once __DIR__ . '/classes/User.php';
require_once __DIR__ . '/classes/Item.php';
require_once __DIR__ . '/classes/Reservation.php';

User::requireLogin();

$item = new Item($db);
$reservation = new Reservation($db);
$error = '';
$success = '';
$action = $_GET['action'] ?? 'list';

$userId = $_SESSION['user_id'];

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';
    
    if ($postAction === 'create') {
        $itemId = (int)($_POST['item_id'] ?? 0);
        $reservationDate = $_POST['reservation_date'] ?? '';
        $notes = trim($_POST['notes'] ?? '');
        
        if (empty($itemId) || empty($reservationDate)) {
            $error = "Vul alle verplichte velden in.";
        } else {
            $result = $reservation->create($userId, $itemId, $reservationDate, $notes);
            if ($result['success']) {
                $success = "Reservering succesvol aangemaakt!";
                $action = 'list';
            } else {
                $error = $result['message'] ?? "Fout bij aanmaken van reservering.";
            }
        }
    } elseif ($postAction === 'cancel') {
        $reservationId = (int)($_POST['reservation_id'] ?? 0);
        $result = $reservation->cancel($reservationId, $userId);
        if ($result['success']) {
            $success = "Reservering geannuleerd!";
        } else {
            $error = $result['message'] ?? "Fout bij annuleren van reservering.";
        }
    } elseif ($postAction === 'pickup_qr') {
        $qrCode = trim($_POST['qr_code'] ?? '');
        $result = $reservation->pickupByQRCode($qrCode, $userId);
        if ($result['success']) {
            $success = "Item succesvol opgehaald!";
        } else {
            $error = $result['message'] ?? "Fout bij ophalen van item.";
        }
    } elseif ($postAction === 'return_qr') {
        $qrCode = trim($_POST['qr_code'] ?? '');
        $result = $reservation->returnByQRCode($qrCode, $userId);
        if ($result['success']) {
            $success = "Item succesvol teruggebracht!";
        } else {
            $error = $result['message'] ?? "Fout bij terugbrengen van item.";
        }
    }
}

// Get user's reservations
$userReservations = $reservation->getByUserId($userId);

// Get available items for reservation
$availableItems = $item->getAll('available');

include __DIR__ . '/Includes/header.php';
?>
<div class="container">
    <div class="page-header">
        <h1>Reserveringen</h1>
        <div class="header-actions">
            <a href="?action=list" class="btn btn-secondary">Mijn Reserveringen</a>
            <a href="?action=new" class="btn btn-primary">Nieuwe Reservering</a>
            <a href="?action=scan" class="btn btn-primary">QR Code Scannen</a>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- QR Code Scanner -->
    <?php if ($action === 'scan'): ?>
        <div class="card">
            <div class="card-header">
                <h2>QR Code Scannen</h2>
            </div>
            <div class="card-body">
                <div class="qr-scanner-container">
                    <div class="scanner-tabs">
                        <button class="tab-btn active" onclick="showScanner('pickup')">Ophalen</button>
                        <button class="tab-btn" onclick="showScanner('return')">Terugbrengen</button>
                    </div>
                    
                    <div id="pickup-scanner" class="scanner-section">
                        <h3>Scan QR code om item op te halen</h3>
                        <video id="video-pickup" width="100%" style="max-width: 500px; display: none;"></video>
                        <canvas id="canvas-pickup" style="display: none;"></canvas>
                        <div id="scanner-status-pickup" class="scanner-status"></div>
                        <button id="start-camera-pickup" class="btn btn-primary">Camera Starten</button>
                        <form method="POST" id="pickup-form" style="display: none;">
                            <input type="hidden" name="action" value="pickup_qr">
                            <input type="hidden" name="qr_code" id="qr-code-pickup">
                        </form>
                    </div>
                    
                    <div id="return-scanner" class="scanner-section" style="display: none;">
                        <h3>Scan QR code om item terug te brengen</h3>
                        <video id="video-return" width="100%" style="max-width: 500px; display: none;"></video>
                        <canvas id="canvas-return" style="display: none;"></canvas>
                        <div id="scanner-status-return" class="scanner-status"></div>
                        <button id="start-camera-return" class="btn btn-primary">Camera Starten</button>
                        <form method="POST" id="return-form" style="display: none;">
                            <input type="hidden" name="action" value="return_qr">
                            <input type="hidden" name="qr_code" id="qr-code-return">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- New Reservation Form -->
    <?php if ($action === 'new'): ?>
        <div class="card">
            <div class="card-header">
                <h2>Nieuwe Reservering</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="reservation.php">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="form-group">
                        <label for="item_id">Item *</label>
                        <select name="item_id" id="item_id" class="form-control" required>
                            <option value="">-- Selecteer item --</option>
                            <?php foreach ($availableItems as $itm): ?>
                                <option value="<?= $itm['id'] ?>">
                                    <?= htmlspecialchars($itm['name']) ?> 
                                    (<?= htmlspecialchars($itm['type'] ?? 'N/A') ?>)
                                    <?php if ($itm['cabinet']): ?>
                                        - Locatie: <?= htmlspecialchars($itm['cabinet']) ?>
                                        <?= $itm['shelf'] ? ' - ' . htmlspecialchars($itm['shelf']) : '' ?>
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="reservation_date">Datum & Tijd *</label>
                        <input type="datetime-local" name="reservation_date" id="reservation_date" 
                               class="form-control" required 
                               min="<?= date('Y-m-d\TH:i') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Opmerkingen</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Reserveren</button>
                        <a href="?action=list" class="btn btn-secondary">Annuleren</a>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- My Reservations List -->
    <?php if ($action === 'list' || empty($action)): ?>
        <div class="card">
            <div class="card-header">
                <h2>Mijn Reserveringen</h2>
            </div>
            <div class="card-body">
                <?php if (empty($userReservations)): ?>
                    <p class="text-muted">U heeft nog geen reserveringen.</p>
                <?php else: ?>
                    <div class="reservations-table">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Datum/Tijd</th>
                                    <th>Status</th>
                                    <th>Locatie</th>
                                    <th>Acties</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($userReservations as $res): ?>
                                    <tr>
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
                                                <br><small>Teruggebracht: <?= date('d-m-Y H:i', strtotime($res['return_date'])) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?= $res['status'] ?>">
                                                <?= ucfirst(str_replace('_', ' ', $res['status'])) ?>
                                            </span>
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
                                            <?php if (in_array($res['status'], ['pending', 'confirmed'])): ?>
                                                <form method="POST" style="display: inline;" 
                                                      onsubmit="return confirm('Weet u zeker dat u deze reservering wilt annuleren?');">
                                                    <input type="hidden" name="action" value="cancel">
                                                    <input type="hidden" name="reservation_id" value="<?= $res['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-secondary">Annuleren</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
let stream = null;
let scanning = false;

function showScanner(type) {
    document.querySelectorAll('.scanner-section').forEach(s => s.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    
    if (type === 'pickup') {
        document.getElementById('pickup-scanner').style.display = 'block';
        event.target.classList.add('active');
    } else {
        document.getElementById('return-scanner').style.display = 'block';
        event.target.classList.add('active');
    }
    
    stopCamera();
}

function stopCamera() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
    scanning = false;
    document.querySelectorAll('video').forEach(v => {
        v.style.display = 'none';
        v.srcObject = null;
    });
}

function startCamera(type) {
    stopCamera();
    
    const videoId = type === 'pickup' ? 'video-pickup' : 'video-return';
    const canvasId = type === 'pickup' ? 'canvas-pickup' : 'canvas-return';
    const statusId = type === 'pickup' ? 'scanner-status-pickup' : 'scanner-status-return';
    const qrCodeId = type === 'pickup' ? 'qr-code-pickup' : 'qr-code-return';
    const formId = type === 'pickup' ? 'pickup-form' : 'return-form';
    
    const video = document.getElementById(videoId);
    const canvas = document.getElementById(canvasId);
    const status = document.getElementById(statusId);
    
    navigator.mediaDevices.getUserMedia({ 
        video: { facingMode: 'environment' } 
    }).then(function(mediaStream) {
        stream = mediaStream;
        video.srcObject = mediaStream;
        video.style.display = 'block';
        video.play();
        scanning = true;
        scanQR(type);
    }).catch(function(err) {
        status.innerHTML = '<div class="alert alert-error">Camera toegang geweigerd. Controleer uw browser instellingen.</div>';
        console.error('Camera error:', err);
    });
}

function scanQR(type) {
    if (!scanning) return;
    
    const videoId = type === 'pickup' ? 'video-pickup' : 'video-return';
    const canvasId = type === 'pickup' ? 'canvas-pickup' : 'canvas-return';
    const statusId = type === 'pickup' ? 'scanner-status-pickup' : 'scanner-status-return';
    const qrCodeId = type === 'pickup' ? 'qr-code-pickup' : 'qr-code-return';
    const formId = type === 'pickup' ? 'pickup-form' : 'return-form';
    
    const video = document.getElementById(videoId);
    const canvas = document.getElementById(canvasId);
    const context = canvas.getContext('2d');
    const status = document.getElementById(statusId);
    
    if (video.readyState === video.HAVE_ENOUGH_DATA) {
        canvas.height = video.videoHeight;
        canvas.width = video.videoWidth;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height);
        
        if (code) {
            document.getElementById(qrCodeId).value = code.data;
            status.innerHTML = '<div class="alert alert-success">QR code gescand: ' + code.data + '</div>';
            stopCamera();
            setTimeout(() => {
                document.getElementById(formId).submit();
            }, 500);
            return;
        }
    }
    
    requestAnimationFrame(() => scanQR(type));
}

document.getElementById('start-camera-pickup').addEventListener('click', () => startCamera('pickup'));
document.getElementById('start-camera-return').addEventListener('click', () => startCamera('return'));

// Cleanup on page unload
window.addEventListener('beforeunload', stopCamera);
</script>
<?php include __DIR__ . '/Includes/footer.php'; ?>
