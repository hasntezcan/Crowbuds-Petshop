<?php
$page_title = "Contact Us - Crawl Buds PetShop";
$active_page = "contact";
$assets_path = "../../assets";
include("../../includes/header.php");
include_once("../../includes/db_connect.php");

$success = '';
$error = '';

// Create table if not exists
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        subject VARCHAR(500) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_read TINYINT DEFAULT 0
    )");
} catch (PDOException $e) {
    // Table already exists or error, continue
}

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    try {
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $subject, $message]);
        $success = "Thank you! Your message has been sent. We'll respond shortly.";
    } catch (PDOException $e) {
        $error = "Error sending message. Please try again later.";
    }
}
?>

<div class="page-container">
    <div class="contact-header">
        <h1>Get in Touch</h1>
        <p>Have questions? We'd love to hear from you!</p>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="contact-grid">
        <div class="contact-info-section">
            <h2>Our Location</h2>
            <div class="map-container">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3009.3234567890123!2d28.9944!3d41.0555!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14cab7f9cfaad4f9%3A0x5c3f8f6d5c3f8f6d!2sKadir%20Has%20%C3%9Cniversitesi!5e0!3m2!1sen!2str!4v1234567890123!5m2!1sen!2str"
                    width="100%" height="300" style="border:0; border-radius: 12px;" allowfullscreen="" loading="lazy">
                </iframe>
            </div>

            <div class="info-cards">
                <div class="info-card">
                    <span class="material-symbols-outlined">location_on</span>
                    <div>
                        <h4>Address</h4>
                        <p>Kadir Has Üniversitesi<br>Cibali, İstanbul, Turkey</p>
                    </div>
                </div>

                <div class="info-card">
                    <span class="material-symbols-outlined">mail</span>
                    <div>
                        <h4>Email</h4>
                        <p>info@crawlbudspetshop.com</p>
                    </div>
                </div>

                <div class="info-card">
                    <span class="material-symbols-outlined">call</span>
                    <div>
                        <h4>Phone</h4>
                        <p>+90 (212) 533-6532</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="contact-form-section">
            <h2>Send us a Message</h2>
            <form method="POST" class="contact-form">
                <div class="form-group">
                    <label>Your Name *</label>
                    <input type="text" name="name" class="form-input" required>
                </div>

                <div class="form-group">
                    <label>Your Email *</label>
                    <input type="email" name="email" class="form-input" required>
                </div>

                <div class="form-group">
                    <label>Subject *</label>
                    <input type="text" name="subject" class="form-input" required>
                </div>

                <div class="form-group">
                    <label>Message *</label>
                    <textarea name="message" class="form-input" rows="5" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-lg">Send Message</button>
            </form>
        </div>
    </div>
</div>

<style>
    .contact-header {
        text-align: center;
        padding: 3rem 0;
    }

    .contact-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
    }

    .contact-header p {
        font-size: 1.125rem;
        color: var(--color-gray-600);
    }

    .contact-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
        margin-top: 2rem;
    }

    .contact-form-section,
    .contact-info-section {
        background: white;
        padding: 2rem;
        border-radius: var(--radius-lg);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .contact-form-section h2,
    .contact-info-section h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
    }

    .map-container {
        margin-bottom: 2rem;
    }

    .info-cards {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .info-card {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem;
        background: var(--color-gray-50);
        border-radius: var(--radius-sm);
    }

    .info-card .material-symbols-outlined {
        font-size: 2rem;
        color: var(--color-primary);
    }

    .info-card h4 {
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .info-card p {
        color: var(--color-gray-600);
        font-size: 0.9375rem;
    }

    .contact-form .form-group {
        margin-bottom: 1.5rem;
    }

    .contact-form label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .contact-form textarea {
        resize: vertical;
        min-height: 120px;
    }

    @media (max-width: 768px) {
        .contact-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php include("../../includes/footer.php"); ?>