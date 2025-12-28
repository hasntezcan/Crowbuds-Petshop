<?php
// Require admin login
include_once("../../includes/admin_guard.php");

$page_title = "Messages";
$active_page = "messages";
$assets_path = "../../assets";
include("../../includes/admin_header.php");
include_once("../../includes/db_connect.php");

// Handle mark as read
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'mark_read') {
        $msg_id = (int) $_POST['message_id'];
        $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
        $stmt->execute([$msg_id]);
    } elseif ($action == 'delete') {
        $msg_id = (int) $_POST['message_id'];
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->execute([$msg_id]);
    }
    header("Location: messages.php");
    exit;
}

// Get filter
$filter = $_GET['filter'] ?? 'all';
$where = '';
if ($filter == 'unread') {
    $where = 'WHERE is_read = 0';
} elseif ($filter == 'read') {
    $where = 'WHERE is_read = 1';
}

// Fetch messages
$stmt = $pdo->query("SELECT * FROM contact_messages $where ORDER BY created_at DESC");
$messages = $stmt->fetchAll();

// Count unread
$stmt = $pdo->query("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0");
$unread_count = $stmt->fetch()['count'];
?>

<div class="messages-container">
    <div class="page-header">
        <h1>Messages</h1>
        <?php if ($unread_count > 0): ?>
            <span class="unread-badge"><?php echo $unread_count; ?> Unread</span>
        <?php endif; ?>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <a href="messages.php?filter=all" class="filter-tab <?php echo $filter == 'all' ? 'active' : ''; ?>">
            All (<?php echo count($messages); ?>)
        </a>
        <a href="messages.php?filter=unread" class="filter-tab <?php echo $filter == 'unread' ? 'active' : ''; ?>">
            Unread (<?php echo $unread_count; ?>)
        </a>
        <a href="messages.php?filter=read" class="filter-tab <?php echo $filter == 'read' ? 'active' : ''; ?>">
            Read
        </a>
    </div>

    <!-- Messages List -->
    <?php if (empty($messages)): ?>
        <div class="empty-state">
            <span class="material-symbols-outlined">inbox</span>
            <h2>No Messages</h2>
            <p>You don't have any messages in this category.</p>
        </div>
    <?php else: ?>
        <div class="messages-list">
            <?php foreach ($messages as $msg): ?>
                <div class="message-card <?php echo $msg['is_read'] ? 'read' : 'unread'; ?>">
                    <div class="message-header">
                        <div class="sender-info">
                            <span class="material-symbols-outlined">account_circle</span>
                            <div>
                                <h3><?php echo htmlspecialchars($msg['name']); ?></h3>
                                <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>" class="email">
                                    <?php echo htmlspecialchars($msg['email']); ?>
                                </a>
                            </div>
                        </div>
                        <div class="message-meta">
                            <span class="date">
                                <span class="material-symbols-outlined">schedule</span>
                                <?php echo date('M d, Y - H:i', strtotime($msg['created_at'])); ?>
                            </span>
                            <?php if (!$msg['is_read']): ?>
                                <span class="badge badge-new">NEW</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="message-subject">
                        <strong>Subject:</strong> <?php echo htmlspecialchars($msg['subject']); ?>
                    </div>

                    <div class="message-content">
                        <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                    </div>

                    <div class="message-actions">
                        <?php if (!$msg['is_read']): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="mark_read">
                                <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-secondary">Mark as Read</button>
                            </form>
                        <?php endif; ?>

                        <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this message?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    .messages-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .page-header h1 {
        font-size: 2rem;
        font-weight: 700;
    }

    .unread-badge {
        background: var(--color-primary);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: var(--radius-sm);
        font-weight: 600;
    }

    .filter-tabs {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
        border-bottom: 2px solid var(--color-gray-200);
    }

    .filter-tab {
        padding: 0.75rem 1.5rem;
        color: var(--color-gray-600);
        font-weight: 600;
        text-decoration: none;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        transition: all 0.2s ease;
    }

    .filter-tab:hover {
        color: var(--color-primary);
    }

    .filter-tab.active {
        color: var(--color-primary);
        border-bottom-color: var(--color-primary);
    }

    .empty-state {
        text-align: center;
        padding: 4rem;
        background: white;
        border-radius: var(--radius-lg);
    }

    .empty-state .material-symbols-outlined {
        font-size: 4rem;
        color: var(--color-gray-400);
    }

    .empty-state h2 {
        margin: 1rem 0 0.5rem;
    }

    .empty-state p {
        color: var(--color-gray-600);
    }

    .messages-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .message-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border-left: 4px solid var(--color-gray-300);
    }

    .message-card.unread {
        border-left-color: var(--color-primary);
        background: #f0fdf4;
    }

    .message-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .sender-info {
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
    }

    .sender-info .material-symbols-outlined {
        font-size: 2.5rem;
        color: var(--color-gray-400);
    }

    .sender-info h3 {
        font-size: 1.125rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .sender-info .email {
        color: var(--color-primary);
        font-size: 0.875rem;
        text-decoration: none;
    }

    .sender-info .email:hover {
        text-decoration: underline;
    }

    .message-meta {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .date {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        color: var(--color-gray-600);
        font-size: 0.875rem;
    }

    .date .material-symbols-outlined {
        font-size: 1rem;
    }

    .badge-new {
        background: var(--color-primary);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: var(--radius-sm);
        font-size: 0.75rem;
        font-weight: 700;
    }

    .message-subject {
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--color-gray-200);
        font-size: 0.9375rem;
    }

    .message-content {
        padding: 1rem;
        background: var(--color-gray-50);
        border-radius: var(--radius-sm);
        margin-bottom: 1rem;
        line-height: 1.6;
        color: var(--color-gray-700);
    }

    .message-actions {
        display: flex;
        gap: 0.5rem;
    }
</style>

<?php include("../../includes/admin_footer.php"); ?>