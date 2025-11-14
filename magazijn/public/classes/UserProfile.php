<?php
class UserProfile {
    private $userId;
    private $bio;
    private $profileImage;
    private $website;
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getBio() {
        return $this->bio;
    }

    public function getProfileImage() {
        return $this->profileImage;
    }

    public function getWebsite() {
        return $this->website;
    }

    public function setBio($bio) {
        $this->bio = $bio;
    }

    public function setProfileImage($profileImage) {
        $this->profileImage = $profileImage;
    }

    public function setWebsite($website) {
        $this->website = $website;
    }

    public function updateProfile($userId, $bio, $profileImage, $website) {
        $this->setBio($bio);
        $this->setProfileImage($profileImage);
        $this->setWebsite($website);
        $this->userId = $userId;

        $query = "UPDATE users SET bio = ?, profileImage = ?, website = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$this->bio, $this->profileImage, $this->website, $this->userId]);
    }

    public function getProfile($userId) {
        $query = "SELECT bio, profileImage, website FROM users WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>