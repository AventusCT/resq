<?php
/**
 * Reservation Class for ResQ
 * Handles item reservations, pickups, and returns
 */
require_once __DIR__ . '/User.php';

class Reservation {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Create a new reservation
     */
    public function create($userId, $itemId, $reservationDate, $notes = null) {
        // Check if item is available
        $itemQuery = "SELECT status FROM items WHERE id = ?";
        $itemStmt = $this->db->prepare($itemQuery);
        $itemStmt->execute([$itemId]);
        $item = $itemStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$item || $item['status'] !== 'available') {
            return ['success' => false, 'message' => 'Item is niet beschikbaar'];
        }
        
        // Check for double reservations
        $checkQuery = "SELECT id FROM reservations 
                       WHERE item_id = ? AND reservation_date = ? 
                       AND status IN ('pending', 'confirmed', 'picked_up')";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute([$itemId, $reservationDate]);
        if ($checkStmt->fetch()) {
            return ['success' => false, 'message' => 'Item is al gereserveerd voor deze datum/tijd'];
        }
        
        // Create reservation
        $query = "INSERT INTO reservations (user_id, item_id, reservation_date, status, notes) 
                  VALUES (?, ?, ?, 'pending', ?)";
        $stmt = $this->db->prepare($query);
        
        if ($stmt->execute([$userId, $itemId, $reservationDate, $notes])) {
            // Update item status
            $updateItemQuery = "UPDATE items SET status = 'reserved' WHERE id = ?";
            $updateStmt = $this->db->prepare($updateItemQuery);
            $updateStmt->execute([$itemId]);
            
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        }
        
        return ['success' => false, 'message' => 'Reservering mislukt'];
    }

    /**
     * Get reservation by ID
     */
    public function getById($id) {
        $query = "SELECT r.*, u.username, u.email, i.name as item_name, i.type as item_type, 
                         i.qr_code, l.cabinet, l.shelf, w.name as warehouse_name
                  FROM reservations r
                  JOIN users u ON r.user_id = u.id
                  JOIN items i ON r.item_id = i.id
                  LEFT JOIN locations l ON i.location_id = l.id
                  LEFT JOIN warehouses w ON i.warehouse_id = w.id
                  WHERE r.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get reservations by user ID
     */
    public function getByUserId($userId) {
        $query = "SELECT r.*, i.name as item_name, i.type as item_type, i.qr_code,
                         l.cabinet, l.shelf, w.name as warehouse_name
                  FROM reservations r
                  JOIN items i ON r.item_id = i.id
                  LEFT JOIN locations l ON i.location_id = l.id
                  LEFT JOIN warehouses w ON i.warehouse_id = w.id
                  WHERE r.user_id = ?
                  ORDER BY r.reservation_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all reservations (admin)
     */
    public function getAll() {
        $query = "SELECT r.*, u.username, u.email, i.name as item_name, i.type as item_type,
                         l.cabinet, l.shelf, w.name as warehouse_name
                  FROM reservations r
                  JOIN users u ON r.user_id = u.id
                  JOIN items i ON r.item_id = i.id
                  LEFT JOIN locations l ON i.location_id = l.id
                  LEFT JOIN warehouses w ON i.warehouse_id = w.id
                  ORDER BY r.reservation_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update reservation status (pickup, return, cancel)
     */
    public function updateStatus($id, $status, $userId = null) {
        $reservation = $this->getById($id);
        if (!$reservation) {
            return ['success' => false, 'message' => 'Reservering niet gevonden'];
        }
        
        // Check if user owns the reservation (unless admin)
        if ($userId && $reservation['user_id'] != $userId) {
            // Check if user is admin
            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                return ['success' => false, 'message' => 'Geen toegang tot deze reservering'];
            }
        }
        
        $validStatuses = ['pending', 'confirmed', 'picked_up', 'returned', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            return ['success' => false, 'message' => 'Ongeldige status'];
        }
        
        // Update reservation
        $query = "UPDATE reservations SET status = ?, updated_at = NOW()";
        
        if ($status === 'picked_up') {
            $query .= ", pickup_date = NOW()";
        } elseif ($status === 'returned') {
            $query .= ", return_date = NOW()";
        }
        
        $query .= " WHERE id = ?";
        $stmt = $this->db->prepare($query);
        
        if ($stmt->execute([$status, $id])) {
            // Update item status accordingly
            $itemStatus = 'available';
            if ($status === 'picked_up') {
                $itemStatus = 'picked_up';
            } elseif ($status === 'returned' || $status === 'cancelled') {
                $itemStatus = 'available';
            }
            
            $updateItemQuery = "UPDATE items SET status = ? WHERE id = ?";
            $updateStmt = $this->db->prepare($updateItemQuery);
            $updateStmt->execute([$itemStatus, $reservation['item_id']]);
            
            return ['success' => true];
        }
        
        return ['success' => false, 'message' => 'Status update mislukt'];
    }

    /**
     * Cancel reservation
     */
    public function cancel($id, $userId = null) {
        return $this->updateStatus($id, 'cancelled', $userId);
    }

    /**
     * Pick up item (scan QR code)
     */
    public function pickupByQRCode($qrCode, $userId) {
        // Find item by QR code
        $itemQuery = "SELECT id FROM items WHERE qr_code = ?";
        $itemStmt = $this->db->prepare($itemQuery);
        $itemStmt->execute([$qrCode]);
        $item = $itemStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$item) {
            return ['success' => false, 'message' => 'Item niet gevonden'];
        }
        
        // Find active reservation for this item and user
        $reservationQuery = "SELECT id FROM reservations 
                             WHERE item_id = ? AND user_id = ? 
                             AND status IN ('pending', 'confirmed')
                             ORDER BY reservation_date ASC LIMIT 1";
        $reservationStmt = $this->db->prepare($reservationQuery);
        $reservationStmt->execute([$item['id'], $userId]);
        $reservation = $reservationStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$reservation) {
            return ['success' => false, 'message' => 'Geen actieve reservering gevonden voor dit item'];
        }
        
        return $this->updateStatus($reservation['id'], 'picked_up', $userId);
    }

    /**
     * Return item (scan QR code)
     */
    public function returnByQRCode($qrCode, $userId) {
        // Find item by QR code
        $itemQuery = "SELECT id FROM items WHERE qr_code = ?";
        $itemStmt = $this->db->prepare($itemQuery);
        $itemStmt->execute([$qrCode]);
        $item = $itemStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$item) {
            return ['success' => false, 'message' => 'Item niet gevonden'];
        }
        
        // Find picked up reservation for this item and user
        $reservationQuery = "SELECT id FROM reservations 
                             WHERE item_id = ? AND user_id = ? 
                             AND status = 'picked_up'
                             ORDER BY pickup_date DESC LIMIT 1";
        $reservationStmt = $this->db->prepare($reservationQuery);
        $reservationStmt->execute([$item['id'], $userId]);
        $reservation = $reservationStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$reservation) {
            return ['success' => false, 'message' => 'Geen opgehaalde reservering gevonden voor dit item'];
        }
        
        return $this->updateStatus($reservation['id'], 'returned', $userId);
    }
}
?>

