<?php
require_once 'includes/Database.php';
require_once 'includes/config.php';

class AdminController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                $error = 'Token CSRF invalide';
                include 'views/admin/login.php';
                return;
            }

            $stmt = $this->db->prepare("SELECT id, username, password FROM admin_users WHERE username = ? AND is_active = 1");
            $stmt->execute([$username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                
                $stmt = $this->db->prepare("UPDATE admin_users SET last_login = datetime('now') WHERE id = ?");
                $stmt->execute([$admin['id']]);
                
                redirect('admin/dashboard');
            } else {
                $error = 'Nom d\'utilisateur ou mot de passe incorrect';
            }
        }

        include 'views/admin/login.php';
    }

    public function dashboard() {
        if (!isLoggedIn()) {
            redirect('admin');
        }

        $stats = $this->getStats();
        $recent_contacts = $this->getRecentContacts();
        $upcoming_appointments = $this->getUpcomingAppointments();

        include 'views/admin/dashboard.php';
    }

    public function content() {
        if (!isLoggedIn()) {
            redirect('admin');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleContentUpdate();
        }

        $content = $this->getContent();
        $services = $this->getServices();
        $team = $this->getTeam();
        $news = $this->getNews();
        $events = $this->getEvents();

        include 'views/admin/content.php';
    }

    public function contacts() {
        if (!isLoggedIn()) {
            redirect('admin');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleContactAction();
        }

        $contacts = $this->getContacts();
        include 'views/admin/contacts.php';
    }

    public function schedule() {
        if (!isLoggedIn()) {
            redirect('admin');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleScheduleAction();
        }

        $slots = $this->getAppointmentSlots();
        $stats = $this->getStats();
        include 'views/admin/schedule.php';
    }

    public function settings() {
        if (!isLoggedIn()) {
            redirect('admin');
        }

        include 'views/admin/settings.php';
    }

    public function messageDetail($id) {
        if (!isLoggedIn()) {
            redirect('admin');
        }

        $contact = $this->getContactById($id);
        if (!$contact) {
            redirect('admin/contacts');
        }

        $files = $this->getContactFiles($id);
        
        // Mark as read
        $this->markContactAsRead($id);

        include 'views/admin/message-detail.php';
    }

    public function logout() {
        destroySession();
        redirect('admin');
    }

    private function getStats() {
        $stats = [];
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM contacts");
        $stats['contacts'] = $stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM contacts WHERE status = 'new'");
        $stats['new_contacts'] = $stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM appointments WHERE status IN ('pending', 'confirmed')");
        $stats['appointments'] = $stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM services WHERE is_active = 1");
        $stats['services'] = $stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM team_members WHERE is_active = 1");
        $stats['team_members'] = $stmt->fetchColumn();

        return $stats;
    }

    private function getRecentContacts() {
        $stmt = $this->db->prepare("
            SELECT c.*, 
                   a.status as appointment_status,
                   s.start_time as appointment_time
            FROM contacts c
            LEFT JOIN appointments a ON c.appointment_id = a.id
            LEFT JOIN appointment_slots s ON a.slot_id = s.id
            ORDER BY c.created_at DESC 
            LIMIT 5
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getUpcomingAppointments() {
        $stmt = $this->db->prepare("
            SELECT c.name, c.email, 
                   a.status as appointment_status,
                   s.start_time as appointment_time
            FROM contacts c
            JOIN appointments a ON c.appointment_id = a.id
            JOIN appointment_slots s ON a.slot_id = s.id
            WHERE s.start_time > datetime('now')
            ORDER BY s.start_time ASC 
            LIMIT 5
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getContent() {
        $stmt = $this->db->query("SELECT section, key_name, value FROM site_content");
        $content = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $content[$row['section']][$row['key_name']] = $row['value'];
        }
        return $content;
    }

    private function getServices() {
        $stmt = $this->db->query("SELECT * FROM services ORDER BY order_position");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getTeam() {
        $stmt = $this->db->query("SELECT * FROM team_members ORDER BY order_position");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getNews() {
        $stmt = $this->db->query("SELECT * FROM news ORDER BY publish_date DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getEvents() {
        $stmt = $this->db->query("SELECT * FROM events ORDER BY event_date DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getContacts() {
        $stmt = $this->db->query("SELECT * FROM contacts ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getAppointmentSlots() {
        $stmt = $this->db->prepare("
            SELECT s.*, 
                   COUNT(a.id) as appointment_count
            FROM appointment_slots s
            LEFT JOIN appointments a ON s.id = a.slot_id
            GROUP BY s.id
            ORDER BY s.start_time ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getContactById($id) {
        $stmt = $this->db->prepare("SELECT * FROM contacts WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getContactFiles($contactId) {
        $stmt = $this->db->prepare("SELECT * FROM contact_files WHERE contact_id = ? ORDER BY uploaded_at DESC");
        $stmt->execute([$contactId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function markContactAsRead($id) {
        $stmt = $this->db->prepare("UPDATE contacts SET status = 'read', updated_at = datetime('now') WHERE id = ?");
        $stmt->execute([$id]);
    }

    private function handleContentUpdate() {
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'Token CSRF invalide'];
            return;
        }

        $action = $_POST['action'] ?? '';

        try {
            switch ($action) {
                case 'update_content':
                    $this->updateSiteContent();
                    break;
                case 'add_service':
                    $this->addService();
                    break;
                case 'update_service':
                    $this->updateService();
                    break;
                case 'delete_service':
                    $this->deleteService();
                    break;
                case 'add_team_member':
                    $this->addTeamMember();
                    break;
                case 'update_team_member':
                    $this->updateTeamMember();
                    break;
                case 'delete_team_member':
                    $this->deleteTeamMember();
                    break;
                case 'add_news':
                    $this->addNews();
                    break;
                case 'update_news':
                    $this->updateNews();
                    break;
                case 'delete_news':
                    $this->deleteNews();
                    break;
                case 'add_event':
                    $this->addEvent();
                    break;
                case 'update_event':
                    $this->updateEvent();
                    break;
                case 'delete_event':
                    $this->deleteEvent();
                    break;
            }
        } catch (Exception $e) {
            error_log("Content update error: " . $e->getMessage());
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'Erreur lors de la mise à jour'];
        }
    }

    private function updateSiteContent() {
        $sections = ['hero', 'about', 'services', 'team', 'news', 'events', 'contact', 'footer', 'values'];
        
        foreach ($sections as $section) {
            if (isset($_POST[$section]) && is_array($_POST[$section])) {
                foreach ($_POST[$section] as $key => $value) {
                    $stmt = $this->db->prepare("
                        INSERT OR REPLACE INTO site_content (section, key_name, value, updated_at) 
                        VALUES (?, ?, ?, datetime('now'))
                    ");
                    $stmt->execute([$section, $key, $value]);
                }
            }
        }
        
        $_SESSION['flash_message'] = ['success' => true, 'message' => 'Contenu mis à jour avec succès!'];
    }

    private function addService() {
        $title = $_POST['service_title'] ?? '';
        $description = $_POST['service_description'] ?? '';
        $icon = $_POST['service_icon'] ?? 'fas fa-gavel';
        $color = $_POST['service_color'] ?? '#3b82f6';
        $detailed_content = $_POST['service_detailed_content'] ?? '';

        if (empty($title) || empty($description)) {
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'Titre et description requis'];
            return;
        }

        $stmt = $this->db->prepare("
            INSERT INTO services (title, description, icon, color, detailed_content, order_position, is_active, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, (SELECT COALESCE(MAX(order_position), 0) + 1 FROM services), 1, datetime('now'), datetime('now'))
        ");
        $stmt->execute([$title, $description, $icon, $color, $detailed_content]);
        
        $_SESSION['flash_message'] = ['success' => true, 'message' => 'Service ajouté avec succès!'];
    }

    private function updateService() {
        $id = $_POST['service_id'] ?? '';
        $title = $_POST['service_title'] ?? '';
        $description = $_POST['service_description'] ?? '';
        $icon = $_POST['service_icon'] ?? 'fas fa-gavel';
        $color = $_POST['service_color'] ?? '#3b82f6';
        $detailed_content = $_POST['service_detailed_content'] ?? '';
        $is_active = isset($_POST['service_is_active']) ? 1 : 0;

        if (empty($id) || empty($title) || empty($description)) {
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'Données manquantes'];
            return;
        }

        $stmt = $this->db->prepare("
            UPDATE services 
            SET title = ?, description = ?, icon = ?, color = ?, detailed_content = ?, is_active = ?, updated_at = datetime('now')
            WHERE id = ?
        ");
        $stmt->execute([$title, $description, $icon, $color, $detailed_content, $is_active, $id]);
        
        $_SESSION['flash_message'] = ['success' => true, 'message' => 'Service mis à jour avec succès!'];
    }

    private function deleteService() {
        $id = $_POST['service_id'] ?? '';
        
        if (empty($id)) {
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'ID manquant'];
            return;
        }

        $stmt = $this->db->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['flash_message'] = ['success' => true, 'message' => 'Service supprimé avec succès!'];
    }

    private function addTeamMember() {
        $name = $_POST['team_name'] ?? '';
        $position = $_POST['team_position'] ?? '';
        $description = $_POST['team_description'] ?? '';
        
        if (empty($name) || empty($position) || empty($description)) {
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'Tous les champs sont requis'];
            return;
        }

        $image_path = $this->handleImageUpload('team_image', 'team');

        $stmt = $this->db->prepare("
            INSERT INTO team_members (name, position, description, image_path, order_position, is_active, created_at, updated_at) 
            VALUES (?, ?, ?, ?, (SELECT COALESCE(MAX(order_position), 0) + 1 FROM team_members), 1, datetime('now'), datetime('now'))
        ");
        $stmt->execute([$name, $position, $description, $image_path]);
        
        $_SESSION['flash_message'] = ['success' => true, 'message' => 'Membre d\'équipe ajouté avec succès!'];
    }

    private function updateTeamMember() {
        $id = $_POST['team_id'] ?? '';
        $name = $_POST['team_name'] ?? '';
        $position = $_POST['team_position'] ?? '';
        $description = $_POST['team_description'] ?? '';
        $is_active = isset($_POST['team_is_active']) ? 1 : 0;

        if (empty($id) || empty($name) || empty($position) || empty($description)) {
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'Données manquantes'];
            return;
        }

        $image_path = $this->handleImageUpload('team_image', 'team');
        
        if ($image_path) {
            $stmt = $this->db->prepare("
                UPDATE team_members 
                SET name = ?, position = ?, description = ?, image_path = ?, is_active = ?, updated_at = datetime('now')
                WHERE id = ?
            ");
            $stmt->execute([$name, $position, $description, $image_path, $is_active, $id]);
        } else {
            $stmt = $this->db->prepare("
                UPDATE team_members 
                SET name = ?, position = ?, description = ?, is_active = ?, updated_at = datetime('now')
                WHERE id = ?
            ");
            $stmt->execute([$name, $position, $description, $is_active, $id]);
        }
        
        $_SESSION['flash_message'] = ['success' => true, 'message' => 'Membre d\'équipe mis à jour avec succès!'];
    }

    private function deleteTeamMember() {
        $id = $_POST['team_id'] ?? '';
        
        if (empty($id)) {
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'ID manquant'];
            return;
        }

        // Get image path to delete file
        $stmt = $this->db->prepare("SELECT image_path FROM team_members WHERE id = ?");
        $stmt->execute([$id]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($member && $member['image_path'] && file_exists($_SERVER['DOCUMENT_ROOT'] . $member['image_path'])) {
            unlink($_SERVER['DOCUMENT_ROOT'] . $member['image_path']);
        }

        $stmt = $this->db->prepare("DELETE FROM team_members WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['flash_message'] = ['success' => true, 'message' => 'Membre d\'équipe supprimé avec succès!'];
    }

    private function addNews() {
        $title = $_POST['news_title'] ?? '';
        $content = $_POST['news_content'] ?? '';
        $publish_date = $_POST['news_publish_date'] ?? date('Y-m-d H:i:s');
        
        if (empty($title) || empty($content)) {
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'Titre et contenu requis'];
            return;
        }

        $image_path = $this->handleImageUpload('news_image', 'news');

        $stmt = $this->db->prepare("
            INSERT INTO news (title, content, image_path, publish_date, order_position, is_active, created_at, updated_at) 
            VALUES (?, ?, ?, ?, (SELECT COALESCE(MAX(order_position), 0) + 1 FROM news), 1, datetime('now'), datetime('now'))
        ");
        $stmt->execute([$title, $content, $image_path, $publish_date]);
        
        $_SESSION['flash_message'] = ['success' => true, 'message' => 'Actualité ajoutée avec succès!'];
    }

    private function updateNews() {
        $id = $_POST['news_id'] ?? '';
        $title = $_POST['news_title'] ?? '';
        $content = $_POST['news_content'] ?? '';
        $publish_date = $_POST['news_publish_date'] ?? '';
        $is_active = isset($_POST['news_is_active']) ? 1 : 0;

        if (empty($id) || empty($title) || empty($content)) {
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'Données manquantes'];
            return;
        }

        $image_path = $this->handleImageUpload('news_image', 'news');
        
        if ($image_path) {
            $stmt = $this->db->prepare("
                UPDATE news 
                SET title = ?, content = ?, image_path = ?, publish_date = ?, is_active = ?, updated_at = datetime('now')
                WHERE id = ?
            ");
            $stmt->execute([$title, $content, $image_path, $publish_date, $is_active, $id]);
        } else {
            $stmt = $this->db->prepare("
                UPDATE news 
                SET title = ?, content = ?, publish_date = ?, is_active = ?, updated_at = datetime('now')
                WHERE id = ?
            ");
            $stmt->execute([$title, $content, $publish_date, $is_active, $id]);
        }
        
        $_SESSION['flash_message'] = ['success' => true, 'message' => 'Actualité mise à jour avec succès!'];
    }

    private function deleteNews() {
        $id = $_POST['news_id'] ?? '';
        
        if (empty($id)) {
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'ID manquant'];
            return;
        }

        // Get image path to delete file
        $stmt = $this->db->prepare("SELECT image_path FROM news WHERE id = ?");
        $stmt->execute([$id]);
        $news = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($news && $news['image_path'] && file_exists($_SERVER['DOCUMENT_ROOT'] . $news['image_path'])) {
            unlink($_SERVER['DOCUMENT_ROOT'] . $news['image_path']);
        }

        $stmt = $this->db->prepare("DELETE FROM news WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['flash_message'] = ['success' => true, 'message' => 'Actualité supprimée avec succès!'];
    }

    private function addEvent() {
        $title = $_POST['event_title'] ?? '';
        $content = $_POST['event_content'] ?? '';
        $event_date = $_POST['event_date'] ?? date('Y-m-d H:i:s');
        
        if (empty($title) || empty($content)) {
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'Titre et contenu requis'];
            return;
        }

        $image_path = $this->handleImageUpload('event_image', 'events');

        $stmt = $this->db->prepare("
            INSERT INTO events (title, content, image_path, event_date, order_position, is_active, created_at, updated_at) 
            VALUES (?, ?, ?, ?, (SELECT COALESCE(MAX(order_position), 0) + 1 FROM events), 1, datetime('now'), datetime('now'))
        ");
        $stmt->execute([$title, $content, $image_path, $event_date]);
        
        $_SESSION['flash_message'] = ['success' => true, 'message' => 'Événement ajouté avec succès!'];
    }

    private function updateEvent() {
        $id = $_POST['event_id'] ?? '';
        $title = $_POST['event_title'] ?? '';
        $content = $_POST['event_content'] ?? '';
        $event_date = $_POST['event_date'] ?? '';
        $is_active = isset($_POST['event_is_active']) ? 1 : 0;

        if (empty($id) || empty($title) || empty($content)) {
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'Données manquantes'];
            return;
        }

        $image_path = $this->handleImageUpload('event_image', 'events');
        
        if ($image_path) {
            $stmt = $this->db->prepare("
                UPDATE events 
                SET title = ?, content = ?, image_path = ?, event_date = ?, is_active = ?, updated_at = datetime('now')
                WHERE id = ?
            ");
            $stmt->execute([$title, $content, $image_path, $event_date, $is_active, $id]);
        } else {
            $stmt = $this->db->prepare("
                UPDATE events 
                SET title = ?, content = ?, event_date = ?, is_active = ?, updated_at = datetime('now')
                WHERE id = ?
            ");
            $stmt->execute([$title, $content, $event_date, $is_active, $id]);
        }
        
        $_SESSION['flash_message'] = ['success' => true, 'message' => 'Événement mis à jour avec succès!'];
    }

    private function deleteEvent() {
        $id = $_POST['event_id'] ?? '';
        
        if (empty($id)) {
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'ID manquant'];
            return;
        }

        // Get image path to delete file
        $stmt = $this->db->prepare("SELECT image_path FROM events WHERE id = ?");
        $stmt->execute([$id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($event && $event['image_path'] && file_exists($_SERVER['DOCUMENT_ROOT'] . $event['image_path'])) {
            unlink($_SERVER['DOCUMENT_ROOT'] . $event['image_path']);
        }

        $stmt = $this->db->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['flash_message'] = ['success' => true, 'message' => 'Événement supprimé avec succès!'];
    }

    private function handleImageUpload($fieldName, $folder) {
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $_FILES[$fieldName];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }

        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/public/uploads/{$folder}/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $folder . '_' . uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return "/public/uploads/{$folder}/{$filename}";
        }

        return null;
    }

    private function handleContactAction() {
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'Token CSRF invalide'];
            return;
        }

        $action = $_POST['action'] ?? '';
        $id = $_POST['id'] ?? '';

        if (empty($id)) {
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'ID manquant'];
            return;
        }

        try {
            switch ($action) {
                case 'mark_read':
                    $stmt = $this->db->prepare("UPDATE contacts SET status = 'read', updated_at = datetime('now') WHERE id = ?");
                    $stmt->execute([$id]);
                    $_SESSION['flash_message'] = ['success' => true, 'message' => 'Message marqué comme lu'];
                    break;
                case 'mark_new':
                    $stmt = $this->db->prepare("UPDATE contacts SET status = 'new', updated_at = datetime('now') WHERE id = ?");
                    $stmt->execute([$id]);
                    $_SESSION['flash_message'] = ['success' => true, 'message' => 'Message marqué comme nouveau'];
                    break;
                case 'delete':
                    $stmt = $this->db->prepare("DELETE FROM contacts WHERE id = ?");
                    $stmt->execute([$id]);
                    $_SESSION['flash_message'] = ['success' => true, 'message' => 'Message supprimé'];
                    break;
            }
        } catch (Exception $e) {
            error_log("Contact action error: " . $e->getMessage());
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'Erreur lors de l\'action'];
        }
    }

    private function handleScheduleAction() {
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'Token CSRF invalide'];
            return;
        }

        $action = $_POST['action'] ?? '';

        try {
            switch ($action) {
                case 'add_daily_slots':
                    $this->addDailySlots();
                    break;
                case 'delete_slot':
                    $this->deleteSlot();
                    break;
            }
        } catch (Exception $e) {
            error_log("Schedule action error: " . $e->getMessage());
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'Erreur lors de l\'action'];
        }
    }

    private function addDailySlots() {
        $date = $_POST['date'] ?? '';
        $allDay = isset($_POST['all_day']);
        $startTime = $allDay ? '09:00' : ($_POST['start_time'] ?? '09:00');
        $endTime = $allDay ? '18:00' : ($_POST['end_time'] ?? '18:00');
        $breakStart = $_POST['break_start'] ?? '';
        $breakEnd = $_POST['break_end'] ?? '';

        if (empty($date)) {
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'Date requise'];
            return;
        }

        $slots = [];
        $current = new DateTime("$date $startTime");
        $end = new DateTime("$date $endTime");
        $breakStartTime = $breakStart ? new DateTime("$date $breakStart") : null;
        $breakEndTime = $breakEnd ? new DateTime("$date $breakEnd") : null;

        while ($current < $end) {
            $slotEnd = clone $current;
            $slotEnd->add(new DateInterval('PT30M'));

            // Skip break time
            if ($breakStartTime && $breakEndTime && 
                $current >= $breakStartTime && $current < $breakEndTime) {
                $current->add(new DateInterval('PT30M'));
                continue;
            }

            if ($slotEnd <= $end) {
                $slots[] = [
                    'start_time' => $current->format('Y-m-d H:i:s'),
                    'end_time' => $slotEnd->format('Y-m-d H:i:s')
                ];
            }

            $current->add(new DateInterval('PT30M'));
        }

        $stmt = $this->db->prepare("
            INSERT INTO appointment_slots (start_time, end_time, is_booked, created_at, updated_at) 
            VALUES (?, ?, 0, datetime('now'), datetime('now'))
        ");

        $count = 0;
        foreach ($slots as $slot) {
            // Check if slot already exists
            $checkStmt = $this->db->prepare("SELECT id FROM appointment_slots WHERE start_time = ? AND end_time = ?");
            $checkStmt->execute([$slot['start_time'], $slot['end_time']]);
            
            if (!$checkStmt->fetch()) {
                $stmt->execute([$slot['start_time'], $slot['end_time']]);
                $count++;
            }
        }

        $_SESSION['flash_message'] = ['success' => true, 'message' => "$count créneaux ajoutés avec succès!"];
    }

    private function deleteSlot() {
        $slotId = $_POST['slot_id'] ?? '';
        
        if (empty($slotId)) {
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'ID de créneau manquant'];
            return;
        }

        $stmt = $this->db->prepare("DELETE FROM appointment_slots WHERE id = ? AND is_booked = 0");
        $stmt->execute([$slotId]);
        
        if ($stmt->rowCount() > 0) {
            $_SESSION['flash_message'] = ['success' => true, 'message' => 'Créneau supprimé avec succès!'];
        } else {
            $_SESSION['flash_message'] = ['success' => false, 'message' => 'Impossible de supprimer ce créneau'];
        }
    }
}
?>