<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
require_once '../includes/db.php';

$base_url = '/work/Gurkul_Project';

// Get contact messages data
try {
    $stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $messages = [];
}

// Handle message deletion
if (isset($_GET['delete_id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->execute([$_GET['delete_id']]);
        header('Location: contact_messages.php?success=Message deleted successfully');
        exit;
    } catch (PDOException $e) {
        header('Location: contact_messages.php?error=Error deleting message');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Bhaktivedanta Gurukul</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="../images/bvgLogo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php 
    $current_page = 'contact_messages.php';
    $current_directory = 'admin';
    include '../includes/header.php'; 
    ?>

    <div class="container mx-auto px-4 py-8 mt-16">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Contact Messages</h1>
                    <p class="text-gray-600">Manage all contact form submissions</p>
                </div>
                <div class="bg-indigo-100 text-indigo-800 px-4 py-2 rounded-lg">
                    <i class="fas fa-envelope mr-2"></i>
                    Total: <?php echo count($messages); ?>
                </div>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <?php if (empty($messages)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-envelope-open-text text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Contact Messages</h3>
                        <p class="text-gray-500">Contact form submissions will appear here.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message Preview</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($messages as $message): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($message['name'] ?? 'N/A'); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($message['email'] ?? 'N/A'); ?></div>
                                        <?php if (!empty($message['phone'])): ?>
                                        <div class="text-sm text-gray-500">
                                            <i class="fas fa-phone mr-1"></i><?php echo htmlspecialchars($message['phone']); ?>
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-medium"><?php echo htmlspecialchars($message['subject'] ?? 'No Subject'); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-600 max-w-xs truncate">
                                            <?php echo htmlspecialchars($message['message'] ?? 'No message'); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($message['ip_address'] ?? 'N/A'); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo date('M j, Y g:i A', strtotime($message['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="viewMessage(<?php echo $message['id']; ?>)" class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <a href="contact_messages.php?delete_id=<?php echo $message['id']; ?>" 
                                           class="text-red-600 hover:text-red-900"
                                           onclick="return confirm('Are you sure you want to delete this message?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
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

    <!-- View Message Modal -->
    <div id="messageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Message Details</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="messageContent" class="space-y-4">
                    <!-- Message content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        function viewMessage(messageId) {
            fetch(`get_message.php?id=${messageId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('messageContent').innerHTML = `
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <h4 class="font-semibold text-gray-800 mb-2">Contact Information</h4>
                                <p><strong>Name:</strong> ${data.name}</p>
                                <p><strong>Email:</strong> ${data.email}</p>
                                ${data.phone ? `<p><strong>Phone:</strong> ${data.phone}</p>` : ''}
                                <p><strong>Subject:</strong> ${data.subject || 'No Subject'}</p>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800 mb-2">Message Details</h4>
                                <p><strong>Submitted:</strong> ${new Date(data.created_at).toLocaleString()}</p>
                                ${data.ip_address ? `<p><strong>IP Address:</strong> ${data.ip_address}</p>` : ''}
                            </div>
                            <div class="md:col-span-2">
                                <h4 class="font-semibold text-gray-800 mb-2">Message</h4>
                                <div class="bg-gray-50 p-4 rounded-lg border">
                                    <p class="text-gray-700 whitespace-pre-wrap">${data.message || 'No message content'}</p>
                                </div>
                            </div>
                        </div>
                    `;
                    document.getElementById('messageModal').classList.remove('hidden');
                });
        }

        function closeModal() {
            document.getElementById('messageModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('messageModal').addEventListener('click', function(e) {
            if (e.target.id === 'messageModal') {
                closeModal();
            }
        });
    </script>
</body>
</html>