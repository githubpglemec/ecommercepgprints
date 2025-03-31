<?php
session_start();
include '../includes/db.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ../login.php');
    exit;
}

// Mark message as read
if (isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
    $message_id = $_GET['mark_read'];
    $sql = "UPDATE contact_messages SET is_read = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    header('Location: messages.php');
    exit;
}

// Get all messages
$sql = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = $conn->query($sql);
$messages = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - PG Prints Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-sidebar">
            <div class="admin-logo">
                <h2>PG<span>Prints</span></h2>
                <p>Admin Panel</p>
            </div>
            <nav class="admin-nav">
                <ul>
                    <li><a href="index.php"><i class="fas fa-shopping-bag"></i> Orders</a></li>
                    <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
                    <li class="active"><a href="messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
                    <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                    <li><a href="../index.php" target="_blank"><i class="fas fa-store"></i> View Store</a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
        <div class="admin-content">
            <div class="admin-header">
                <h1>Contact Messages</h1>
                <div class="admin-user">
                    <span>Welcome, Admin</span>
                </div>
            </div>
            <div class="admin-main">
                <div class="admin-card">
                    <div class="card-header">
                        <h2>Customer Messages</h2>
                    </div>
                    <div class="card-body">
                        <?php if(empty($messages)): ?>
                            <div class="empty-data">
                                <i class="fas fa-envelope-open"></i>
                                <p>No messages found</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="admin-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Subject</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($messages as $message): ?>
                                            <tr class="<?php echo $message['is_read'] ? '' : 'unread'; ?>">
                                                <td>#<?php echo $message['id']; ?></td>
                                                <td><?php echo htmlspecialchars($message['name']); ?></td>
                                                <td><?php echo htmlspecialchars($message['email']); ?></td>
                                                <td><?php echo htmlspecialchars($message['subject']); ?></td>
                                                <td><?php echo date('M d, Y g:i A', strtotime($message['created_at'])); ?></td>
                                                <td>
                                                    <span class="status-badge <?php echo $message['is_read'] ? 'read' : 'unread'; ?>">
                                                        <?php echo $message['is_read'] ? 'Read' : 'Unread'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <a href="#" class="btn-icon view message-view" data-id="<?php echo $message['id']; ?>" title="View Message">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <?php if(!$message['is_read']): ?>
                                                            <a href="messages.php?mark_read=<?php echo $message['id']; ?>" class="btn-icon edit" title="Mark as Read">
                                                                <i class="fas fa-check"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Message View Modal -->
    <div id="messageModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="messageDetails">
                <!-- Message details will be loaded here -->
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Message view modal
        const modal = document.getElementById('messageModal');
        const messageViewBtns = document.querySelectorAll('.message-view');
        const closeBtn = document.querySelector('.close');
        const messageDetails = document.getElementById('messageDetails');
        
        messageViewBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const messageId = this.getAttribute('data-id');
                
                // Fetch message details
                fetch(`get_message.php?id=${messageId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            messageDetails.innerHTML = `
                                <div class="message-modal-content">
                                    <h2>${data.message.subject}</h2>
                                    <div class="message-sender-info">
                                        <p><strong>From:</strong> ${data.message.name} (${data.message.email})</p>
                                        <p><strong>Date:</strong> ${new Date(data.message.created_at).toLocaleString()}</p>
                                    </div>
                                    <div class="message-body">
                                        <h3>Message</h3>
                                        <p>${data.message.message}</p>
                                    </div>
                                    <div class="message-actions">
                                        <a href="mailto:${data.message.email}" class="btn btn-primary">Reply via Email</a>
                                        <a href="messages.php?mark_read=${data.message.id}" class="btn btn-secondary">Mark as Read</a>
                                    </div>
                                </div>
                            `;
                            modal.style.display = 'block';
                        } else {
                            alert('Failed to load message details.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while loading message details.');
                    });
            });
        });
        
        // Close modal
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });
    });
    </script>
</body>
</html>
