<?php
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $userModel;
    private $message = '';
    private $messageType = '';

    public function __construct() {
        $this->userModel = new User();
    }

    public function index() {
        $editUser = null;

        // Handle GET request for editing
        if (isset($_GET['edit'])) {
            $editUser = $this->userModel->getById($_GET['edit']);
        }

        // Handle POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost();
        }

        // Get all users
        $users = $this->userModel->getAll();

        // Load views
        $this->loadView('layout', [
            'title' => 'User Management System',
            'content' => $this->renderUserManagement($users, $editUser)
        ]);
    }

    private function handlePost() {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add':
                    $this->addUser();
                    break;
                case 'edit':
                    $this->editUser();
                    break;
                case 'delete':
                    $this->deleteUser();
                    break;
            }
        }
    }

    private function addUser() {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $gender = $_POST['gender'];
        $country = trim($_POST['country']);

        try {
            $this->userModel->create($name, $email, $gender, $country);
            $this->setMessage('User added successfully!', 'success');
        } catch (Exception $e) {
            $this->setMessage($e->getMessage(), 'error');
        }
    }

    private function editUser() {
        $id = $_POST['id'];
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $gender = $_POST['gender'];
        $country = trim($_POST['country']);

        try {
            $this->userModel->update($id, $name, $email, $gender, $country);
            $this->setMessage('User updated successfully!', 'success');
            // Redirect to clear the edit parameter
            header('Location: index.php');
            exit;
        } catch (Exception $e) {
            $this->setMessage($e->getMessage(), 'error');
        }
    }

    private function deleteUser() {
        $id = $_POST['id'];

        try {
            $this->userModel->delete($id);
            $this->setMessage('User deleted successfully!', 'success');
        } catch (Exception $e) {
            $this->setMessage($e->getMessage(), 'error');
        }
    }

    private function setMessage($message, $type) {
        $this->message = $message;
        $this->messageType = $type;
    }

    private function renderUserManagement($users, $editUser) {
        ob_start();
        ?>
        <!-- Message Display -->
        <?php if ($this->message): ?>
            <div class="mb-6 p-4 rounded-lg <?php echo $this->messageType === 'success' ? 'bg-green-100 text-green-700 border border-green-400' : 'bg-red-100 text-red-700 border border-red-400'; ?>">
                <?php echo htmlspecialchars($this->message); ?>
            </div>
        <?php endif; ?>

        <div class="grid md:grid-cols-2 gap-8">
            <?php
            // Load user form
            include __DIR__ . '/../views/user_form.php';

            // Load user list
            include __DIR__ . '/../views/user_list.php';
            ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private function loadView($view, $data = []) {
        extract($data);
        include __DIR__ . "/../views/$view.php";
    }

    public function getMessage() {
        return $this->message;
    }

    public function getMessageType() {
        return $this->messageType;
    }
}