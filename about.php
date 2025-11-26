<?php
session_start();
include('includes/header.php'); 
?>


<style>
    .about-container {
        max-width: 900px;
        margin: 2rem auto;
        padding: 2rem;
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.05);
        border: 1px solid #e0d9ce;
    }
    .about-container h1, .about-container h2 {
        color: var(--primary-color);
        font-weight: 700;
        margin-bottom: 1rem;
        border-bottom: 2px solid var(--accent-color);
        padding-bottom: 0.5rem;
    }
    .about-container p {
        line-height: 1.7;
        margin-bottom: 1.5rem;
        font-size: 1.1rem;
        color: var(--dark-text);
    }
    .team-section {
        display: flex;
        gap: 2rem;
        margin-top: 2rem;
        flex-wrap: wrap;
    }
    .team-member {
        flex: 1;
        min-width: 280px;
        text-align: center;
    }
    .team-photo {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background-color: var(--neutral-bg);
        margin: 0 auto 1rem auto;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        color: var(--primary-color);
        border: 3px solid var(--primary-color);
    }
    .team-member h3 {
        color: var(--primary-color);
        margin-bottom: 0.25rem;
        font-size: 1.5rem;
    }
    .team-member span {
        color: var(--accent-color);
        font-weight: bold;
        display: block;
    }
</style>

<div class="about-container">
    <h1>Our Story</h1>
    <p>
        Welcome to XiCi Instruments! We believe that music is a universal language that connects us all, and every artist deserves the perfect instrument to express their voice. Our mission is to provide high-quality, reliable, and inspiring musical gear to creators at every stage of their journey from the first-time learner to the seasoned professional.
    </p>

    <h2>From a Project to a Passion</h2>
    <p>
        XiCi Instruments began as an ambitious academic project for our Information Management course. As second-year Bachelor of Science in Information Technology (BSIT) students, we were tasked with designing and building a comprehensive e-commerce platform. We saw this not just as a requirement, but as an opportunity to combine our passion for technology with our deep appreciation for music.
    </p>
    <p>
        We channeled our skills in database design, web development, and user experience to create more than just a functional website we aimed to build a hub for musicians. A place that is easy to navigate, secure, and packed with the instruments you need to bring your sound to life.
    </p>

    <h2>Meet the Founders</h2>
    <div class="team-section">
        <div class="team-member">
            <div class="team-photo">
                <i class="fa-solid fa-user-tie"></i>
            </div>
            <h3>Cyrus Pagayunan</h3>
            <span>BSIT-NS-2A Student</span>
        </div>
        <div class="team-member">
            <div class="team-photo">
                <i class="fa-solid fa-user-tie"></i>
            </div>
            <h3>Donn Torres</h3>
            <span>BSIT-NS-2A Student</span>
        </div>
    </div>
</div>

<?php
include('includes/footer.php');
?>