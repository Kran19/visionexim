<?php
$page_title = "About Us - Vision Exim";
include 'includes/header.php';
?>
<?php include 'includes/navbar.php'; ?>

<style>
  :root {
    --red: #c0191a;
    --dark-red: #9e1212;
    --cream: #f0e8df;
    --light-cream: #f7f1eb;
    --text-dark: #2b1a1a;
    --text-mid: #555;
    --white: #fff;
  }

  body {
    background: var(--white);
    color: var(--text-dark);
    overflow-x: hidden;
  }

  .glance {
    background: var(--white);
    padding: 70px 80px;
    display: flex;
    gap: 60px;
    align-items: flex-start;
  }

  .glance-img-wrap {
    flex: 0 0 300px;
    position: relative;
  }

  .glance-img-wrap::before {
    content: '';
    position: absolute;
    top: -12px;
    left: -12px;
    width: 100%;
    height: 100%;
    border: 3px solid var(--red);
    border-radius: 2px;
    z-index: 0;
  }

  .glance-img-wrap img {
    width: 100%;
    aspect-ratio: 4/3;
    object-fit: cover;
    position: relative;
    z-index: 1;
    border-radius: 2px;
    filter: sepia(40%) contrast(1.1);
  }

  .glance-text h2 {
    font-family: 'Playfair Display', serif;
    font-size: 26px;
    color: var(--red);
    margin-bottom: 20px;
    text-align: center;
  }

  .glance-text p {
    font-size: 15px;
    line-height: 1.85;
    color: var(--text-mid);
  }

  .section-label {
    display: block;
    text-align: center;
    font-size: 11px;
    letter-spacing: 4px;
    text-transform: uppercase;
    color: var(--text-mid);
    margin-bottom: 4px;
  }

  .history-section {
    background: var(--cream);
    padding: 80px 60px;
    position: relative;
  }

  .history-section::before,
  .history-section::after {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    height: 50px;
    background: var(--white);
    clip-path: ellipse(55% 100% at 50% 0%);
  }

  .history-section::before {
    top: 0;
  }

  .history-section::after {
    bottom: 0;
    background: var(--cream);
    clip-path: ellipse(55% 100% at 50% 100%);
  }

  .history-section h2 {
    font-family: 'Playfair Display', serif;
    font-size: 52px;
    color: var(--red);
    text-align: center;
    margin-bottom: 6px;
  }

  .timeline {
    display: flex;
    justify-content: center;
    gap: 0;
    position: relative;
    margin-top: 60px;
  }

  .timeline::before {
    content: '';
    position: absolute;
    top: 48px;
    left: 10%;
    right: 10%;
    height: 2px;
    background: linear-gradient(to right, transparent, var(--red) 10%, var(--red) 90%, transparent);
  }

  .timeline-item {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 0 16px;
    animation: fadeUp .6s ease both;
  }

  .timeline-item:nth-child(1) { animation-delay: .1s; }
  .timeline-item:nth-child(2) { animation-delay: .2s; }
  .timeline-item:nth-child(3) { animation-delay: .3s; }
  .timeline-item:nth-child(4) { animation-delay: .4s; }
  .timeline-item:nth-child(5) { animation-delay: .5s; }

  .plant-icon {
    width: 64px;
    height: 64px;
    margin-bottom: 16px;
    position: relative;
  }

  .plant-icon svg {
    width: 100%;
    height: 100%;
  }

  .timeline-year {
    font-family: 'Playfair Display', serif;
    font-size: 22px;
    font-weight: 700;
    color: var(--red);
    margin-bottom: 8px;
  }

  .timeline-desc {
    font-size: 13px;
    color: var(--text-mid);
    line-height: 1.6;
    max-width: 140px;
  }

  .timeline-btns {
    display: flex;
    justify-content: center;
    gap: 16px;
    margin-top: 50px;
  }

  .btn {
    padding: 12px 32px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
    border: none;
    cursor: pointer;
    transition: all .25s;
    text-decoration: none;
  }

  .btn-red {
    background: var(--red);
    color: var(--white);
  }
  .btn-red:hover {
    background: var(--dark-red);
  }
  .btn-outline {
    background: transparent;
    color: var(--red);
    border: 2px solid var(--red);
  }
  .btn-outline:hover {
    background: var(--red);
    color: var(--white);
  }

  /* Certification Section (merged) */
  .certifications-section {
    background: #fdfaf5;
    padding: 60px 0;
    border-top: 1px solid rgba(0,0,0,0.05);
    border-bottom: 1px solid rgba(0,0,0,0.05);
    overflow: hidden;
  }
  .cert-track {
    display: flex;
    align-items: center;
    gap: 60px;
    animation: scroll 30s linear infinite;
    width: max-content;
  }
  .cert-item {
    flex: 0 0 auto;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    filter: grayscale(100%);
    opacity: 0.6;
    transition: all 0.4s ease;
  }
  .cert-item:hover {
    filter: grayscale(0%);
    opacity: 1;
    transform: scale(1.1);
  }
  .cert-item img {
    max-height: 100%;
    max-width: 180px;
    object-fit: contain;
  }
  @keyframes scroll {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
  }
  .cert-container {
    position: relative;
    mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
    -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
  }

  /* Collaboration / Branding section */
  .collaboration-section {
    background: #fff;
    padding: 80px 0;
  }
  .brand-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 40px;
    align-items: center;
    justify-items: center;
  }
  .brand-item {
    width: 100%;
    max-width: 180px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    border-radius: 16px;
    background: #fdfaf5;
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.03);
  }
  .brand-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.05);
    background: #fff;
    border-color: var(--red);
  }
  .brand-item img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    filter: grayscale(100%);
    opacity: 0.7;
    transition: all 0.3s ease;
  }
  .brand-item:hover img {
    filter: grayscale(0%);
    opacity: 1;
  }

  .cta-section {
    background: var(--light-cream);
    padding: 90px 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 24px;
    position: relative;
    overflow: hidden;
  }
  .cta-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 60px;
    background: var(--red);
    clip-path: ellipse(55% 100% at 50% 0%);
  }
  .cta-trucks {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 160px;
    opacity: .18;
  }
  .cta-trucks.left { left: 20px; }
  .cta-trucks.right { right: 20px; transform: translateY(-50%) scaleX(-1); }
  .cta-section h2 {
    font-family: 'Playfair Display', serif;
    font-size: clamp(26px, 4vw, 46px);
    text-align: center;
    line-height: 1.2;
  }
  .cta-section h2 span { color: var(--red); }
  .cta-section p {
    font-size: 14px;
    color: var(--text-mid);
    text-align: center;
    max-width: 420px;
  }
  .whatsapp-float {
    position: fixed;
    right: 20px;
    bottom: 80px;
    width: 52px;
    height: 52px;
    background: #25d366;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 16px rgba(37,211,102,.4);
    cursor: pointer;
    transition: transform .3s;
    z-index: 999;
  }
  .whatsapp-float:hover { transform: scale(1.1); }
  .whatsapp-float svg { width: 28px; height: 28px; fill: white; }
  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
  }
  .fade-up {
    opacity: 0;
    transform: translateY(40px);
    transition: opacity .7s ease, transform .7s ease;
  }
  .fade-up.visible {
    opacity: 1;
    transform: none;
  }
  @media (max-width: 900px) {
    .glance { flex-direction: column; padding: 50px 24px; }
    .glance-img-wrap { flex: unset; width: 100%; }
    .timeline { flex-wrap: wrap; }
    .timeline::before { display: none; }
    .timeline-item { flex: 0 0 50%; margin-bottom: 28px; }
    .history-section, .certifications-section, .collaboration-section, .cta-section { padding: 60px 24px; }
  }
  @media (max-width: 640px) {
    .glance { gap: 28px; }
    .timeline-item { flex: 0 0 100%; }
    .cta-trucks { display: none; }
    .brand-grid { gap: 20px; }
  }
</style>

<section class="inner_banner banner-about">
  <div class="container">
    <div class="title text-center">
      <h2>About Us</h2>
      <p>Learn more about Vision Exim's journey, our commitment to quality, and our mission to deliver authentic Indian spices worldwide.</p>
    </div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb justify-content-center">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item">About Us</li>
      </ol>
    </nav>
  </div>
</section>

<section class="glance">
  <div class="glance-img-wrap fade-up">
    <img src="assets/images/about-us-welcome.png" alt="Traditional farming" />
  </div>
  <div class="glance-text fade-up">
    <span class="section-label">Vision Exim At A Glance</span>
    <h2>Vision Exim At A Glance</h2>
    <p>
      Vision Exim is a reliable exporter of high-quality spices, dedicated to delivering authentic Indian flavors across
      the globe. Founded on the principle of consistency in taste, we ensure a supply of the finest Indian spices. Our
      manufacturing unit and products comply with international standards as we strive to deliver the authentic taste of
      Indian cuisine at its best quality. As a manufacturer and exporter of spices in India, Vision Exim offers a wide
      gamut of Indian spices and condiments in bulk quantities. All our commodities are processed using premium grade
      ingredients and advanced agricultural practices for freshness, consistent taste, and high nutritional content.
    </p>
  </div>
</section>
<!-- Certification Section (merged from homepage) -->
<section class="certifications-section">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-label" data-aos="fade-up">Accreditations</span>
      <h2 class="fade-up" style="font-family:'Playfair Display',serif; font-size:32px; color:var(--red);">Our Certifications & Memberships</h2>
    </div>
    <div class="cert-container">
      <div class="cert-track">
        <!-- Original items -->
        <div class="cert-item"><img src="images/certificates/APEDA.png" alt="APEDA"></div>
        <div class="cert-item"><img src="images/certificates/FDA–USA.png" alt="FDA USA"></div>
        <div class="cert-item"><img src="images/certificates/FIEO.png" alt="FIEO"></div>
        <div class="cert-item"><img src="images/certificates/FSSAI.png" alt="FSSAI"></div>
        <div class="cert-item"><img src="images/certificates/HACCP.png" alt="HACCP"></div>
        <div class="cert-item"><img src="images/certificates/ISO.png" alt="ISO"></div>
        <div class="cert-item"><img src="images/certificates/SPICES-BOARD-OF-INDIA.png" alt="Spices Board of India"></div>
        <!-- Duplicate for seamless scroll -->
        <div class="cert-item"><img src="images/certificates/APEDA.png" alt="APEDA"></div>
        <div class="cert-item"><img src="images/certificates/FDA–USA.png" alt="FDA USA"></div>
        <div class="cert-item"><img src="images/certificates/FIEO.png" alt="FIEO"></div>
        <div class="cert-item"><img src="images/certificates/FSSAI.png" alt="FSSAI"></div>
        <div class="cert-item"><img src="images/certificates/HACCP.png" alt="HACCP"></div>
        <div class="cert-item"><img src="images/certificates/ISO.png" alt="ISO"></div>
        <div class="cert-item"><img src="images/certificates/SPICES-BOARD-OF-INDIA.png" alt="Spices Board of India"></div>
      </div>
    </div>
  </div>
</section>
<section class="cta-section">
  <svg class="cta-trucks left" viewBox="0 0 200 100" fill="none"><rect x="20" y="30" width="120" height="50" rx="4" fill="#c0191a"/><rect x="140" y="45" width="45" height="35" rx="4" fill="#c0191a"/><circle cx="50" cy="84" r="12" fill="#333"/><circle cx="50" cy="84" r="5" fill="#ccc"/><circle cx="155" cy="84" r="12" fill="#333"/><circle cx="155" cy="84" r="5" fill="#ccc"/><rect x="140" y="50" width="20" height="18" rx="2" fill="#add8e6" opacity=".6"/></svg>
  <h2 class="fade-up"><span>Interested In</span><br />Working Together?</h2>
  <p class="fade-up">Vision Exim manufactures and exports a wide range of spices and masala across the world with a focus on purity, hygiene, and customer satisfaction.</p>
  <a href="contact-us.php" class="btn btn-red fade-up">Get In Touch</a>
  <svg class="cta-trucks right" viewBox="0 0 200 100" fill="none"><rect x="20" y="30" width="120" height="50" rx="4" fill="#c0191a"/><rect x="140" y="45" width="45" height="35" rx="4" fill="#c0191a"/><circle cx="50" cy="84" r="12" fill="#333"/><circle cx="50" cy="84" r="5" fill="#ccc"/><circle cx="155" cy="84" r="12" fill="#333"/><circle cx="155" cy="84" r="5" fill="#ccc"/><rect x="140" y="50" width="20" height="18" rx="2" fill="#add8e6" opacity=".6"/></svg>
</section>

<script>
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15 });
  document.querySelectorAll('.fade-up').forEach((element) => observer.observe(element));
</script>

<?php include 'includes/footer.php'; ?>