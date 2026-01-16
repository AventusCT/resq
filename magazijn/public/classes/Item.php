<?php
/**
 * Item Class for ResQ
 * Handles inventory item management, QR code generation
 */
// QR code library is optional - loaded via Composer if available
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}

class Item {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Create a new item
     */
    public function create($name, $type, $description, $locationId = null, $warehouseId = null) {
        $qrCode = $this->generateQRCode($name);
        $qrCodePath = $this->saveQRCode($qrCode, $name);
        
        $query = "INSERT INTO items (name, type, description, status, qr_code, qr_code_path, location_id, warehouse_id) 
                  VALUES (?, ?, ?, 'available', ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        
        if ($stmt->execute([$name, $type, $description, $qrCode, $qrCodePath, $locationId, $warehouseId])) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Get item by ID
     */
    public function getById($id) {
        $query = "SELECT i.*, l.cabinet, l.shelf, w.name as warehouse_name 
                  FROM items i 
                  LEFT JOIN locations l ON i.location_id = l.id 
                  LEFT JOIN warehouses w ON i.warehouse_id = w.id 
                  WHERE i.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get item by QR code
     */
    public function getByQRCode($qrCode) {
        $query = "SELECT i.*, l.cabinet, l.shelf, w.name as warehouse_name 
                  FROM items i 
                  LEFT JOIN locations l ON i.location_id = l.id 
                  LEFT JOIN warehouses w ON i.warehouse_id = w.id 
                  WHERE i.qr_code = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$qrCode]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all items
     */
    public function getAll($status = null) {
        if ($status) {
            $query = "SELECT i.*, l.cabinet, l.shelf, w.name as warehouse_name 
                      FROM items i 
                      LEFT JOIN locations l ON i.location_id = l.id 
                      LEFT JOIN warehouses w ON i.warehouse_id = w.id 
                      WHERE i.status = ? 
                      ORDER BY i.name ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$status]);
        } else {
            $query = "SELECT i.*, l.cabinet, l.shelf, w.name as warehouse_name 
                      FROM items i 
                      LEFT JOIN locations l ON i.location_id = l.id 
                      LEFT JOIN warehouses w ON i.warehouse_id = w.id 
                      ORDER BY i.name ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update item
     */
    public function update($id, $name, $type, $description, $status, $locationId = null, $warehouseId = null) {
        $query = "UPDATE items SET name = ?, type = ?, description = ?, status = ?, 
                  location_id = ?, warehouse_id = ?, updated_at = NOW() 
                  WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$name, $type, $description, $status, $locationId, $warehouseId, $id]);
    }

    /**
     * Update item status
     */
    public function updateStatus($id, $status) {
        $validStatuses = ['available', 'reserved', 'picked_up', 'returned', 'defective', 'maintenance'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        $query = "UPDATE items SET status = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$status, $id]);
    }

    /**
     * Delete item
     */
    public function delete($id) {
        // Delete QR code file if exists
        $item = $this->getById($id);
        if ($item && isset($item['qr_code_path']) && $item['qr_code_path']) {
            $filePath = __DIR__ . '/../' . $item['qr_code_path'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
        
        $query = "DELETE FROM items WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }

    /**
     * Generate QR code string
     */
    private function generateQRCode($name) {
        return 'RESQ-' . strtoupper(substr(md5($name . time()), 0, 8));
    }

    /**
     * Save QR code image
     */
    private function saveQRCode($qrCode, $name) {
        // Create qr_codes directory if it doesn't exist
        $qrDir = __DIR__ . '/../assets/qr_codes/';
        if (!file_exists($qrDir)) {
            mkdir($qrDir, 0755, true);
        }
        
        $filename = $qrDir . $qrCode . '.png';
        
        // Try to generate QR code using Endroid QR Code library (if installed via Composer)
        if (class_exists('Endroid\QrCode\QrCode')) {
            try {
                $qrCodeObj = new \Endroid\QrCode\QrCode($qrCode);
                $writer = new \Endroid\QrCode\Writer\PngWriter();
                $result = $writer->write($qrCodeObj);
                file_put_contents($filename, $result->getString());
            } catch (Exception $e) {
                // Fallback if library fails
                $this->createQRPlaceholder($qrCode, $filename);
            }
        } else {
            // Fallback: create a simple text-based placeholder
            // The QR code data is stored in the database, so it can be generated client-side
            $this->createQRPlaceholder($qrCode, $filename);
        }
        
        return 'assets/qr_codes/' . $qrCode . '.png';
    }
    
    /**
     * Create a simple placeholder QR code file
     */
    private function createQRPlaceholder($qrCode, $filename) {
        // Create a simple text file with QR code data
        // In production, use a QR code library or generate client-side
        $content = "QR Code: $qrCode\n";
        $content .= "Scan this code or enter manually: $qrCode\n";
        $content .= "Generated: " . date('Y-m-d H:i:s');
        file_put_contents($filename, $content);
    }

    /**
     * Regenerate QR code for item
     */
    public function regenerateQRCode($id) {
        $item = $this->getById($id);
        if (!$item) {
            return false;
        }
        
        // Delete old QR code file if exists
        if (isset($item['qr_code_path']) && $item['qr_code_path']) {
            $oldPath = __DIR__ . '/../' . $item['qr_code_path'];
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }
        
        $qrCode = $this->generateQRCode($item['name']);
        $qrCodePath = $this->saveQRCode($qrCode, $item['name']);
        
        $query = "UPDATE items SET qr_code = ?, qr_code_path = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$qrCode, $qrCodePath, $id]);
    }

    /**
     * Search items
     */
    public function search($searchTerm) {
        $query = "SELECT i.*, l.cabinet, l.shelf, w.name as warehouse_name 
                  FROM items i 
                  LEFT JOIN locations l ON i.location_id = l.id 
                  LEFT JOIN warehouses w ON i.warehouse_id = w.id 
                  WHERE i.name LIKE ? OR i.type LIKE ? OR i.qr_code LIKE ? 
                  ORDER BY i.name ASC";
        $stmt = $this->db->prepare($query);
        $searchPattern = '%' . $searchTerm . '%';
        $stmt->execute([$searchPattern, $searchPattern, $searchPattern]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

