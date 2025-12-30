<?php
$page_title = "About Us - Crawl Buds PetShop";
$active_page = "about";
$assets_path = "../../assets";
include("../../includes/header.php");

// Team members
$team = [
    ['name' => 'Hasan Tezcan', 'image' => 'hasan-tezcan.png'],
    ['name' => 'Sezai Araplarlı', 'image' => 'sezai-araplarlı.png'],
    ['name' => 'Yiğit Kaan Pepeoğlu', 'image' => 'yigit-kaan.png'],
    ['name' => 'Samet Aba', 'image' => 'samet-aba.png'],
    ['name' => 'Melek Sadiki', 'image' => 'melek-sadiki.png'],
];
?>

<div class="page-container">
    <div class="about-header">
        <h1>About Crawl Buds PetShop</h1>
        <p>Your trusted partner for pet care and supplies</p>
    </div>

    <div class="about-content">
        <section class="mission-section">
            <h2>Our Mission</h2>
            <p>At Crawl Buds PetShop, we're passionate about providing the best products and services for your beloved
                pets. Whether you have a dog, cat, bird, or exotic pet, we have everything you need to keep them happy
                and healthy.</p>
            <p>We believe that every pet deserves the best care, and we're committed to offering high-quality products
                at affordable prices. Our team works hard to source the finest pet supplies from trusted brands around
                the world.</p>
        </section>

        <section class="values-section">
            <h2>Our Values</h2>
            <div class="values-grid">
                <div class="value-card">
                    <span class="material-symbols-outlined">verified</span>
                    <h3>Quality First</h3>
                    <p>We only stock products that meet our high standards</p>
                </div>
                <div class="value-card">
                    <span class="material-symbols-outlined">favorite</span>
                    <h3>Pet Welfare</h3>
                    <p>Your pet's health and happiness is our priority</p>
                </div>
                <div class="value-card">
                    <span class="material-symbols-outlined">support_agent</span>
                    <h3>Customer Care</h3>
                    <p>We're here to help you every step of the way</p>
                </div>
            </div>
        </section>

        <section class="team-section">
            <h2>Meet Our Team</h2>
            <p class="team-intro">Our dedicated team of 5 passionate individuals working together to bring you the best
                pet shopping experience.</p>
            <div class="team-grid">
                <?php foreach ($team as $member): ?>
                    <div class="team-card">
                        <div class="team-avatar">
                            <img src="<?php echo $assets_path; ?>/images/<?php echo htmlspecialchars($member['image']); ?>"
                                alt="<?php echo htmlspecialchars($member['name']); ?>">
                        </div>
                        <h3><?php echo htmlspecialchars($member['name']); ?></h3>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</div>

<style>
    .about-header {
        text-align: center;
        padding: 3rem 0;
    }

    .about-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
    }

    .about-header p {
        font-size: 1.125rem;
        color: var(--color-gray-600);
    }

    .about-content {
        max-width: 1000px;
        margin: 0 auto;
    }

    .mission-section,
    .values-section,
    .team-section {
        margin-bottom: 4rem;
    }

    .mission-section h2,
    .values-section h2,
    .team-section h2 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
    }

    .mission-section p {
        font-size: 1.125rem;
        line-height: 1.8;
        color: var(--color-gray-700);
        margin-bottom: 1rem;
    }

    .values-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }

    .value-card {
        background: white;
        padding: 2rem;
        border-radius: var(--radius-lg);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .value-card .material-symbols-outlined {
        font-size: 3rem;
        color: var(--color-primary);
        margin-bottom: 1rem;
    }

    .value-card h3 {
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .value-card p {
        color: var(--color-gray-600);
    }

    .team-intro {
        font-size: 1.125rem;
        color: var(--color-gray-600);
        margin-bottom: 2rem;
    }

    .team-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1.5rem;
        justify-items: center;
    }

    .team-card {
        background: white;
        padding: 2rem;
        border-radius: var(--radius-lg);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        text-align: center;
        transition: transform 0.2s ease;
    }

    .team-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
    }

    .team-avatar {
        width: 120px;
        height: 120px;
        margin: 0 auto 1rem;
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid var(--color-primary-100);
    }

    .team-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .team-card h3 {
        font-weight: 700;
        margin-bottom: 0.5rem;
    }



    @media (max-width: 1024px) {
        .team-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {

        .values-grid,
        .team-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .team-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php include("../../includes/footer.php"); ?>