<?php
require_once __DIR__ . '/includes/product-functions.php';
$featured_products = ve_get_featured_products(4);
include 'includes/header.php';
?>

<!-- Loading Screen -->
<div class="loading-screen" id="loading-screen">
	<div class="loading-logo">
		<img src="images/certificates/vision logo color (1).png" alt="Vision Exim Logo" style="height: 80px; width: auto; margin-bottom: 20px;">
	</div>
	<div class="loading-bar"></div>
</div>

<script>
	window.addEventListener('load', function () {
		const loader = document.getElementById('loading-screen');
		setTimeout(() => {
			loader.classList.add('fade-out');
		}, 1000); // Small delay for effect
	});
</script>

<?php include 'includes/navbar.php'; ?>

<style>
	.modern-home-section {
		padding-top: 90px;
		padding-bottom: 90px;
	}

	/* ==============================
	   HERO SLIDER
	============================== */
	/* ==============================
	   PREMIUM HERO SLIDER (SPLIT LAYOUT)
	============================== */
	.hero-slider-section {
		position: relative;
		width: 100%;
		height: 720px;
		overflow: hidden;
		margin-top: 70px;
		background: #fdfaf5;
		/* Premium beige/cream */
	}

	.hero-slide {
		position: absolute;
		inset: 0;
		opacity: 0;
		visibility: hidden;
		transition: opacity 1.2s ease, visibility 1.2s;
		z-index: 1;
	}

	.hero-slide.active {
		opacity: 1;
		visibility: visible;
		z-index: 2;
	}

	.hero-slide-content {
		height: 100%;
		display: flex;
		align-items: center;
	}

	.hero-slide-text {
		padding-right: 40px;
	}

	.hero-slide-kicker {
		display: inline-flex;
		align-items: center;
		padding: 6px 16px;
		background: rgba(192, 25, 26, 0.08);
		border-radius: 50px;
		font-size: 13px;
		font-weight: 700;
		letter-spacing: 0.12em;
		text-transform: uppercase;
		color: #c0191a;
		margin-bottom: 24px;
	}

	.hero-slide-title {
		font-size: 60px;
		font-weight: 800;
		line-height: 1.1;
		color: #1a1a1a;
		margin-bottom: 24px;
	}

	.hero-slide-title span {
		color: #c0191a;
	}

	.hero-slide-desc {
		font-size: 18px;
		line-height: 1.8;
		color: #555;
		margin-bottom: 40px;
		max-width: 520px;
	}

	.hero-slide-cta {
		display: flex;
		gap: 16px;
	}

	.hero-btn {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		padding: 16px 36px;
		background: #c0191a;
		color: #fff;
		border-radius: 12px;
		font-weight: 700;
		text-decoration: none;
		transition: all 0.3s ease;
		box-shadow: 0 12px 24px rgba(192, 25, 26, 0.25);
	}

	.hero-btn:hover {
		background: #a31516;
		transform: translateY(-3px);
		box-shadow: 0 15px 30px rgba(192, 25, 26, 0.35);
		color: #fff;
	}

	.hero-btn-outline {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		padding: 16px 36px;
		border: 2px solid #c0191a;
		color: #c0191a;
		border-radius: 12px;
		font-weight: 700;
		text-decoration: none;
		transition: all 0.3s ease;
		background: transparent;
	}

	.hero-btn-outline:hover {
		background: rgba(192, 25, 26, 0.05);
		transform: translateY(-3px);
		color: #c0191a;
	}

	/* Right Side - Visuals */
	.hero-slide-visual {
		position: relative;
		height: 520px;
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.hero-image-container {
		position: relative;
		width: 100%;
		height: 100%;
		border-radius: 40px;
		overflow: hidden;
		box-shadow: 0 40px 80px rgba(0, 0, 0, 0.12);
		z-index: 1;
	}

	.hero-image-container img {
		width: 100%;
		height: 100%;
		object-fit: cover;
		transition: transform 10s linear;
	}

	.hero-slide.active .hero-image-container img {
		transform: scale(1.15);
	}

	/* Decorative Blobs */
	.hero-blob {
		position: absolute;
		width: 400px;
		height: 400px;
		background: radial-gradient(circle, rgba(192, 25, 26, 0.05) 0%, transparent 70%);
		border-radius: 50%;
		z-index: 0;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
	}

	/* Glassmorphism Cards */
	.glass-card {
		position: absolute;
		background: rgba(255, 255, 255, 0.7);
		backdrop-filter: blur(12px);
		-webkit-backdrop-filter: blur(12px);
		border: 1px solid rgba(255, 255, 255, 0.4);
		padding: 18px 24px;
		border-radius: 20px;
		box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
		display: flex;
		align-items: center;
		gap: 15px;
		z-index: 3;
		transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
		opacity: 0;
		transform: translateY(20px);
	}

	.hero-slide.active .glass-card {
		opacity: 1;
		transform: translateY(0);
	}

	.glass-card i {
		width: 44px;
		height: 44px;
		background: #fff;
		border-radius: 12px;
		display: flex;
		align-items: center;
		justify-content: center;
		color: #b8860b;
		/* Premium Golden */
		font-size: 18px;
		box-shadow: 0 8px 16px rgba(0, 0, 0, 0.05);
	}

	.glass-card strong {
		display: block;
		font-size: 15px;
		color: #1a1a1a;
		line-height: 1.2;
	}

	.glass-card span {
		display: block;
		font-size: 12px;
		color: #777;
		margin-top: 2px;
	}

	.card-1 {
		top: 10%;
		right: -30px;
		transition-delay: 0.5s;
	}

	.card-2 {
		bottom: 15%;
		left: -40px;
		transition-delay: 0.7s;
	}

	.card-3 {
		bottom: -20px;
		right: 40px;
		transition-delay: 0.9s;
	}

	/* Pagination Dots */
	.hero-dots {
		position: absolute;
		bottom: 40px;
		left: 7%;
		z-index: 10;
		display: flex;
		gap: 12px;
	}

	.hero-dot {
		width: 12px;
		height: 12px;
		border-radius: 50%;
		background: rgba(192, 25, 26, 0.15);
		border: none;
		cursor: pointer;
		transition: all 0.4s ease;
		padding: 0;
	}

	.hero-dot.active {
		background: #c0191a;
		width: 36px;
		border-radius: 6px;
	}

	@media (max-width: 991px) {
		.hero-slider-section {
			height: auto;
			padding: 60px 0;
		}

		.hero-slide {
			position: relative;
			inset: auto;
			opacity: 1;
			visibility: visible;
			display: none;
		}

		.hero-slide.active {
			display: block;
		}

		.hero-slide-text {
			padding-right: 0;
			margin-bottom: 50px;
			text-align: center;
		}

		.hero-slide-cta {
			justify-content: center;
		}

		.hero-slide-desc {
			margin-left: auto;
			margin-right: auto;
		}

		.hero-slide-title {
			font-size: 42px;
		}

		.glass-card {
			display: none;
		}

		.hero-dots {
			left: 50%;
			transform: translateX(-50%);
			bottom: 20px;
		}
	}

	.hero-visual-card {
		position: relative;
		z-index: 1;
	}

	.hero-image-wrap {
		position: relative;
		border-radius: 40px;
		overflow: hidden;
		box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
		transform: perspective(1000px) rotateY(-5deg);
	}

	.hero-image-wrap img {
		width: 100%;
		height: auto;
		display: block;
	}

	.hero-floating-badge {
		position: absolute;
		background: #fff;
		padding: 20px;
		border-radius: 20px;
		box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
		display: flex;
		align-items: center;
		gap: 15px;
		z-index: 2;
	}

	.badge-top-right {
		top: -20px;
		right: -20px;
	}

	.badge-bottom-left {
		bottom: 30px;
		left: -40px;
	}

	.hero-floating-badge i {
		font-size: 24px;
		color: #c0191a;
	}

	.hero-floating-badge strong {
		display: block;
		font-size: 18px;
		color: #1a1a1a;
	}

	.hero-floating-badge span {
		font-size: 13px;
		color: #777;
	}

	.hero-trust-icon {
		width: 46px;
		height: 46px;
		margin-bottom: 12px;
		border-radius: 50%;
		border: 1px solid rgba(31, 107, 53, 0.2);
		background: #fff;
		display: flex;
		align-items: center;
		justify-content: center;
		color: #1f6b35;
		font-size: 18px;
		box-shadow: 0 10px 20px rgba(31, 107, 53, 0.08);
	}

	.hero-trust-item span {
		display: block;
		color: #17181c;
		font-weight: 700;
		font-size: 14px;
		line-height: 1.45;
	}

	.hero-visual-card {
		position: relative;
		min-height: 590px;
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.hero-visual-card::before {
		content: "";
		position: absolute;
		inset: 22px 0 0 40px;
		border-radius: 280px 0 0 280px;
		background: linear-gradient(180deg, rgba(255, 190, 87, 0.94) 0%, rgba(255, 145, 43, 0.78) 100%);
	}

	.hero-visual-card::after {
		content: "";
		position: absolute;
		left: 24px;
		top: 6px;
		width: 76px;
		height: 76px;
		border-radius: 50%;
		border: 2px solid rgba(255, 193, 94, 0.7);
		opacity: 0.9;
	}

	.hero-visual-frame {
		position: relative;
		z-index: 1;
		width: 100%;
		max-width: 600px;
		height: 560px;
		border-radius: 280px 0 0 280px;
		overflow: hidden;
		box-shadow: 0 30px 70px rgba(65, 37, 10, 0.22);
	}

	.hero-visual-frame img {
		width: 100%;
		height: 100%;
		object-fit: cover;
		object-position: center;
	}

	.hero-visual-glow {
		position: absolute;
		inset: auto auto 22px 24px;
		width: 180px;
		height: 180px;
		background: radial-gradient(circle, rgba(34, 112, 52, 0.4), rgba(34, 112, 52, 0) 68%);
		filter: blur(2px);
		z-index: 1;
	}

	.hero-seal {
		position: absolute;
		left: -28px;
		top: 48%;
		z-index: 3;
		width: 122px;
		height: 122px;
		border-radius: 50%;
		background: #195c2d;
		border: 6px solid #fff8ec;
		box-shadow: 0 18px 34px rgba(25, 92, 45, 0.24);
		display: flex;
		align-items: center;
		justify-content: center;
		text-align: center;
		padding: 16px;
		color: #fff;
		transform: translateY(-50%);
	}

	.hero-seal::before {
		content: "";
		position: absolute;
		inset: 8px;
		border-radius: 50%;
		border: 1px solid rgba(255, 255, 255, 0.4);
	}

	.hero-seal strong {
		display: block;
		font-size: 34px;
		line-height: 1;
		margin: 4px 0;
	}

	.hero-seal span {
		display: block;
		font-size: 11px;
		letter-spacing: 0.08em;
		text-transform: uppercase;
		line-height: 1.35;
	}

	.hero-leaf-accent {
		position: absolute;
		left: -34px;
		bottom: 40px;
		width: 100%;
		max-width: 220px;
		z-index: 2;
	}

	.hero-badge-grid {
		position: absolute;
		left: 50%;
		right: 50px;
		bottom: 14px;
		transform: translateX(-50%);
		z-index: 5;
		display: grid;
		grid-template-columns: repeat(4, 1fr);
		gap: 0;
		padding: 18px 26px;
		border-radius: 24px;
		background: linear-gradient(90deg, #184f28 0%, #113920 100%);
		box-shadow: 0 24px 42px rgba(17, 57, 32, 0.28);
	}

	.hero-mini-badge {
		display: flex;
		align-items: center;
		gap: 14px;
		padding: 4px 18px;
		border-right: 1px solid rgba(255, 255, 255, 0.12);
	}

	.hero-mini-badge:last-child {
		border-right: 0;
	}

	.hero-mini-icon {
		width: 52px;
		height: 52px;
		flex: 0 0 52px;
		border-radius: 50%;
		border: 1px solid rgba(255, 255, 255, 0.18);
		background: rgba(255, 255, 255, 0.08);
		display: flex;
		align-items: center;
		justify-content: center;
		color: #fff;
		font-size: 20px;
	}

	.hero-mini-badge strong {
		display: block;
		font-size: 18px;
		color: #fff;
		line-height: 1.1;
	}

	.hero-mini-badge span {
		display: block;
		font-size: 14px;
		font-weight: 500;
		color: rgba(255, 255, 255, 0.86);
		line-height: 1.35;
	}

	.section-kicker {
		display: inline-block;
		font-size: 14px;
		font-weight: 700;
		letter-spacing: 0.18em;
		text-transform: uppercase;
		color: var(--primary);
		margin-bottom: 14px;
	}

	.section-heading {
		font-size: 52px;
		line-height: 1.05;
		margin-bottom: 16px;
	}

	.section-intro {
		max-width: 700px;
		margin: 0 auto;
		color: rgba(24, 24, 36, 0.72);
	}

	/* Featured Products — equal-height cards, aligned buttons */
	.featured-products-section .row > [class*="col-"] {
		display: flex;
	}

	.featured-products-section .collections-card {
		display: flex;
		flex-direction: column;
		width: 100%;
		height: 100%;
		background: #fff;
		padding: 18px 18px 24px;
		border-radius: 24px;
		box-shadow: 0 18px 38px rgba(24, 24, 36, 0.06);
		text-align: center;
	}

	.featured-products-section .collections-img {
		flex-shrink: 0;
		height: 220px;
		display: flex;
		align-items: center;
		justify-content: center;
		overflow: hidden;
		margin-bottom: 8px;
	}

	.featured-products-section .collections-img img {
		width: auto;
		max-width: 100%;
		max-height: 200px;
		height: auto;
		object-fit: contain;
		position: relative;
	}

	.featured-products-section .collections-card-body {
		flex: 1;
		display: flex;
		flex-direction: column;
		align-items: center;
		min-height: 0;
		width: 100%;
	}

	.featured-products-section .collections-card h5 {
		font-size: 22px;
		margin-top: 12px;
		margin-bottom: 10px;
		flex-shrink: 0;
	}

	.featured-products-section .featured-product-note {
		flex: 1;
		width: 100%;
		min-height: 42px;
		font-size: 14px;
		font-weight: 600;
		line-height: 1.45;
		color: rgba(24, 24, 36, 0.68);
		margin: 0 0 16px;
	}

	.featured-products-section .featured-product-note--empty {
		visibility: hidden;
		margin-bottom: 0;
	}

	.featured-products-section .collections-card-footer {
		margin-top: auto;
		width: 100%;
		display: flex;
		justify-content: center;
		padding-top: 4px;
	}

	.featured-products-section .collections-card-footer .btn {
		margin-top: 0;
	}

	.view-all-wrap {
		margin-top: 34px;
	}

	.about-snapshot {
		background: linear-gradient(180deg, #fff 0%, #fff8f4 100%);
	}

	.about-copy-card {
		background: #fff;
		border-radius: 28px;
		padding: 42px;
		box-shadow: 0 24px 50px rgba(24, 24, 36, 0.08);
	}

	.about-pillars {
		display: flex;
		flex-direction: column;
		gap: 16px;
	}

	.about-pillar-item {
		display: flex;
		align-items: flex-start;
		gap: 14px;
		padding: 14px 18px;
		background: rgba(192, 25, 26, 0.04);
		border-left: 3px solid var(--primary);
		border-radius: 0 12px 12px 0;
	}

	.about-pillar-item .pillar-icon {
		font-size: 22px;
		flex-shrink: 0;
		margin-top: 2px;
	}

	.about-pillar-item strong {
		display: block;
		font-size: 15px;
		margin-bottom: 2px;
		color: var(--dark);
	}

	.about-pillar-item p {
		margin: 0;
		font-size: 13px;
		color: rgba(24, 24, 36, 0.65);
		line-height: 1.5;
	}

	.vision-section {
		background: linear-gradient(180deg, #fff 0%, #fff8f4 100%);
	}

	.vision-block {
		background: #fff;
		border-radius: 28px;
		padding: 42px;
		box-shadow: 0 24px 50px rgba(24, 24, 36, 0.08);
		height: 100%;
	}

	.vision-block--accent {
		background: linear-gradient(145deg, rgba(192, 25, 26, 0.04) 0%, rgba(192, 25, 26, 0.08) 100%);
		border: 1px solid rgba(192, 25, 26, 0.12);
	}

	.vision-list {
		list-style: none;
		padding: 0;
		margin: 0;
		display: flex;
		flex-direction: column;
		gap: 12px;
	}

	.vision-list li {
		display: flex;
		align-items: flex-start;
		gap: 10px;
		font-size: 15px;
		color: var(--dark);
		line-height: 1.5;
	}

	.vision-list li::before {
		content: "→";
		color: var(--primary);
		font-weight: 700;
		flex-shrink: 0;
		margin-top: 1px;
	}

	.cat-tag {
		display: inline-block;
		margin-top: 10px;
		padding: 4px 12px;
		border-radius: 999px;
		background: rgba(192, 25, 26, 0.08);
		color: var(--primary);
		font-size: 12px;
		font-weight: 700;
		letter-spacing: 0.06em;
		text-transform: uppercase;
	}

	.stats-band {
		background:
			linear-gradient(rgba(192, 25, 26, 0.92), rgba(149, 18, 18, 0.92)),
			url('https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&q=80&w=1600') center/cover fixed;
		color: #fff;
	}

	.stat-card {
		text-align: center;
		padding: 28px 18px;
		background: rgba(255, 255, 255, 0.08);
		border: 1px solid rgba(255, 255, 255, 0.15);
		border-radius: 24px;
		height: 100%;
	}

	.stat-icon {
		width: 58px;
		height: 58px;
		margin: 0 auto 18px;
		border-radius: 18px;
		display: flex;
		align-items: center;
		justify-content: center;
		background: rgba(255, 255, 255, 0.14);
		font-size: 22px;
		font-weight: 700;
	}

	.stat-card h3 {
		font-size: 52px;
		line-height: 1;
		color: #fff;
		margin-bottom: 10px;
	}

	.stat-card p {
		color: rgba(255, 255, 255, 0.8);
		font-weight: 600;
		text-transform: uppercase;
		letter-spacing: 0.08em;
		font-size: 13px;
	}

	.process-box {
		position: relative;
		padding: 34px 28px;
		background: #fff;
		border-radius: 24px;
		box-shadow: 0 18px 38px rgba(24, 24, 36, 0.06);
		transition: 0.3s;
		height: 100%;
	}

	.process-box:hover {
		transform: translateY(-10px);
		background: var(--primary);
		color: #fff;
	}

	.process-box h4 {
		font-family: var(--titlefont);
		margin: 18px 0 12px;
	}

	.process-box p {
		margin-bottom: 0;
	}

	.process-box:hover p {
		color: rgba(255, 255, 255, 0.82);
	}

	.process-num {
		font-size: 56px;
		font-weight: 900;
		color: rgba(24, 24, 36, 0.06);
		position: absolute;
		top: 18px;
		right: 20px;
		font-family: var(--titlefont);
		line-height: 1;
	}

	.process-box:hover .process-num {
		color: rgba(255, 255, 255, 0.12);
	}

	.process-icon {
		width: 58px;
		height: 58px;
		border-radius: 18px;
		background: rgba(192, 25, 26, 0.1);
		color: var(--primary);
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 24px;
		font-weight: 700;
	}

	.process-box:hover .process-icon {
		background: rgba(255, 255, 255, 0.14);
		color: #fff;
	}

	.reach-section {
		background: #fff;
	}

	.reach-map-card {
		position: relative;
		background: radial-gradient(circle at center, rgba(192, 25, 26, 0.1), rgba(192, 25, 26, 0.02) 60%, transparent 60%);
		border: 1px solid rgba(24, 24, 36, 0.08);
		border-radius: 30px;
		padding: 34px;
		min-height: 100%;
	}

	.reach-map-shell {
		position: relative;
		min-height: 360px;
		border-radius: 24px;
		background:
			linear-gradient(180deg, #fff8f4 0%, #fff 100%);
		overflow: hidden;
	}

	.reach-map-shell::before {
		content: "";
		position: absolute;
		inset: 10% 8%;
		background: url('assets/images/truck.png') center/contain no-repeat;
		opacity: 0.14;
	}

	.region-pill {
		position: absolute;
		padding: 10px 16px;
		border-radius: 999px;
		background: #fff;
		box-shadow: 0 12px 22px rgba(24, 24, 36, 0.12);
		font-size: 13px;
		font-weight: 700;
		color: var(--dark);
	}

	.region-pill::before {
		content: "";
		display: inline-block;
		width: 10px;
		height: 10px;
		margin-right: 8px;
		border-radius: 50%;
		background: var(--primary);
		box-shadow: 0 0 0 6px rgba(192, 25, 26, 0.14);
	}

	.region-middle-east {
		top: 28%;
		left: 58%;
	}

	.region-europe {
		top: 16%;
		left: 44%;
	}

	.region-asia {
		top: 42%;
		left: 68%;
	}

	.region-africa {
		top: 58%;
		left: 40%;
	}

	.region-far-east {
		top: 22%;
		left: 80%;
	}

	.category-card,
	.testimonial-card,
	.cta-panel {
		background: #fff;
		border-radius: 24px;
		box-shadow: 0 18px 38px rgba(24, 24, 36, 0.06);
	}

	.category-card {
		padding: 34px 26px;
		height: 100%;
		text-align: center;
		border: 1px solid transparent;
		transition: 0.3s ease;
	}

	.category-card:hover {
		transform: translateY(-8px);
		border-color: rgba(192, 25, 26, 0.2);
	}

	.category-icon {
		width: 68px;
		height: 68px;
		margin: 0 auto 18px;
		border-radius: 20px;
		background: rgba(192, 25, 26, 0.08);
		color: var(--primary);
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 28px;
		font-family: var(--titlefont);
	}

	.testimonials-section {
		background: linear-gradient(180deg, #fff8f4 0%, #fff 100%);
	}

	.testimonial-card {
		padding: 34px 30px;
		height: 100%;
	}

	.testimonial-card blockquote {
		font-size: 18px;
		line-height: 1.7;
		color: var(--dark);
		margin-bottom: 24px;
	}

	.testimonial-author {
		font-size: 14px;
		font-weight: 700;
		letter-spacing: 0.1em;
		text-transform: uppercase;
		color: var(--primary);
	}

	.cta-panel {
		padding: 48px;
		background:
			linear-gradient(135deg, rgba(24, 24, 36, 0.96), rgba(192, 25, 26, 0.92)),
			url('https://images.unsplash.com/photo-1518977676601-b53f82aba655?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;
		color: #fff;
	}

	.cta-panel p {
		color: rgba(255, 255, 255, 0.82);
		max-width: 720px;
	}

	.cta-panel .btn-outline-light {
		border-radius: 26px;
		padding: 10px 28px;
		font-weight: 700;
	}

	@media (max-width: 991px) {
		.modern-export-hero {
			padding: 78px 0 42px;
		}

		.hero-banner-section {
			padding: 20px 0 16px;
		}

		.hero-shell {
			padding: 34px 26px 30px;
			background: linear-gradient(180deg, rgba(255, 251, 245, 0.98) 0%, rgba(255, 247, 236, 0.96) 100%);
		}

		.section-heading {
			font-size: 40px;
		}

		.hero-title {
			font-size: 44px;
			margin-bottom: 20px;
		}

		.hero-visual-card {
			min-height: auto;
			padding-top: 10px;
		}

		.hero-visual-card::before {
			inset: 0;
			border-radius: 40px;
		}

		.hero-visual-card::after {
			display: none;
		}

		.hero-visual-frame {
			height: 380px;
			border-radius: 36px;
		}

		.hero-seal {
			left: 18px;
			top: auto;
			bottom: 18px;
			transform: none;
		}

		.hero-leaf-accent {
			left: -8px;
			bottom: 6px;
			max-width: 160px;
		}

		.hero-badge-grid {
			position: relative;
			left: auto;
			right: auto;
			bottom: auto;
			transform: none;
			grid-template-columns: repeat(2, 1fr);
			margin-top: 24px;
			padding: 16px 14px;
		}
	}

	@media (max-width: 767px) {
		.modern-home-section {
			padding-top: 70px;
			padding-bottom: 70px;
		}

		.section-heading {
			font-size: 32px;
		}

		.about-copy-card,
		.cta-panel {
			padding: 30px 24px;
		}

		.hero-title {
			font-size: 38px;
		}

		.hero-lead {
			font-size: 16px;
		}

		.hero-image-wrap {
			border-radius: 20px;
			transform: none;
		}

		.hero-floating-badge {
			padding: 10px;
			gap: 10px;
		}

		.badge-top-right {
			right: 0;
			top: -10px;
		}

		.badge-bottom-left {
			left: 0;
			bottom: 10px;
		}

		.hero-floating-badge i {
			font-size: 18px;
		}

		.hero-floating-badge strong {
			font-size: 14px;
		}

		.reach-map-shell {
			min-height: 300px;
		}

		.region-middle-east {
			top: 30%;
			left: 46%;
		}

		.region-europe {
			top: 15%;
			left: 20%;
		}

		.region-asia {
			top: 54%;
			left: 54%;
		}
	}
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
	/* Collaboration Section */
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
		border-color: var(--primary);
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

</style>

<section class="hero-slider-section" id="hero-slider">

	<!-- Slide 1: Spices -->
	<div class="hero-slide active">
		<div class="container hero-slide-content">
			<div class="row align-items-center w-100">
				<div class="col-lg-6">
					<div class="hero-slide-text" data-aos="fade-right">
						<span class="hero-slide-kicker">Premium Indian Spices</span>
						<h1 class="hero-slide-title">Delivering Premium <span>Indian Products</span> Worldwide</h1>
						<p class="hero-slide-desc">Trusted exporter of spices, pulses, grains, and FMCG products with
							global quality standards and authentic Indian heritage.</p>
						<div class="hero-slide-cta">
							<a href="our-products.php" class="hero-btn">Explore Products</a>
							<a href="contact-us.php" class="hero-btn-outline ms-2">Get a Quote</a>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="hero-slide-visual">
						<div class="hero-blob"></div>
						<div class="hero-image-container">
							<img src="https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=1200&q=80"
								alt="Premium Spices">
						</div>
						<!-- Glass Cards -->
						<div class="glass-card card-1">
							<i class="fa-solid fa-award"></i>
							<div>
								<strong>#1 Export Quality</strong>
								<span>Certified Standards</span>
							</div>
						</div>
						<div class="glass-card card-2">
							<i class="fa-solid fa-leaf"></i>
							<div>
								<strong>100% Natural</strong>
								<span>Purity Guaranteed</span>
							</div>
						</div>
						<div class="glass-card card-3">
							<i class="fa-solid fa-globe"></i>
							<div>
								<strong>Worldwide Shipping</strong>
								<span>30+ Countries</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Slide 2: Pulses & Grains -->
	<div class="hero-slide">
		<div class="container hero-slide-content">
			<div class="row align-items-center w-100">
				<div class="col-lg-6">
					<div class="hero-slide-text">
						<span class="hero-slide-kicker">Quality Pulses & Grains</span>
						<h1 class="hero-slide-title">Nutrient-Rich <span>Pulses & Grains</span> Sourced Directly</h1>
						<p class="hero-slide-desc">Naturally sourced, cleaned and processed directly from trusted Indian
							farms for international markets and wholesale buyers.</p>
						<div class="hero-slide-cta">
							<a href="our-products.php" class="hero-btn">View Range</a>
							<a href="contact-us.php" class="hero-btn-outline ms-2">Contact Us</a>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="hero-slide-visual">
						<div class="hero-blob"></div>
						<div class="hero-image-container">
							<img src="images/certificates/SLIDER IMAGE.jpeg"
								alt="Premium Pulses">
						</div>
						<!-- Glass Cards -->
						<div class="glass-card card-1">
							<i class="fa-solid fa-seedling"></i>
							<div>
								<strong>Farm Fresh</strong>
								<span>Direct from Origin</span>
							</div>
						</div>
						<div class="glass-card card-2">
							<i class="fa-solid fa-box"></i>
							<div>
								<strong>Bulk Supply</strong>
								<span>Wholesale Ready</span>
							</div>
						</div>
						<div class="glass-card card-3">
							<i class="fa-solid fa-circle-check"></i>
							<div>
								<strong>Quality Checked</strong>
								<span>Strict Lab Testing</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Slide 3: FMCG Products -->
	<div class="hero-slide">
		<div class="container hero-slide-content">
			<div class="row align-items-center w-100">
				<div class="col-lg-6">
					<div class="hero-slide-text">
						<span class="hero-slide-kicker">Trusted FMCG Partner</span>
						<h1 class="hero-slide-title">Global Supply of <span>FMCG Products</span> & Packaging</h1>
						<p class="hero-slide-desc">Bulk supply with private labeling and custom packaging solutions for
							retail and wholesale partners worldwide with timely logistics.</p>
						<div class="hero-slide-cta">
							<a href="contact-us.php" class="hero-btn">Request Quote</a>
							<a href="our-products.php" class="hero-btn-outline ms-2">Our Products</a>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="hero-slide-visual">
						<div class="hero-blob"></div>
						<div class="hero-image-container">
							<img src="https://images.unsplash.com/photo-1586201375761-83865001e31c?auto=format&fit=crop&w=1200&q=80"
								alt="FMCG Products">
						</div>
						<!-- Glass Cards -->
						<div class="glass-card card-1">
							<i class="fa-solid fa-tags"></i>
							<div>
								<strong>Trusted Brands</strong>
								<span>Quality Packaging</span>
							</div>
						</div>
						<div class="glass-card card-2">
							<i class="fa-solid fa-truck-fast"></i>
							<div>
								<strong>Fast Delivery</strong>
								<span>Air & Sea Freight</span>
							</div>
						</div>
						<div class="glass-card card-3">
							<i class="fa-solid fa-truck"></i>
							<div>
								<strong>Global Distribution</strong>
								<span>Efficient Logistics</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Pagination Dots -->
	<div class="hero-dots" id="hero-dots">
		<button class="hero-dot active" data-slide="0" aria-label="Slide 1"></button>
		<button class="hero-dot" data-slide="1" aria-label="Slide 2"></button>
		<button class="hero-dot" data-slide="2" aria-label="Slide 3"></button>
	</div>

</section>

<script>
	(function () {
		const slides = document.querySelectorAll('#hero-slider .hero-slide');
		const dots = document.querySelectorAll('#hero-dots .hero-dot');
		let current = 0;
		let timer;
		function goTo(n) {
			slides[current].classList.remove('active');
			dots[current].classList.remove('active');
			current = (n + slides.length) % slides.length;
			slides[current].classList.add('active');
			dots[current].classList.add('active');
		}
		function startAuto() { timer = setInterval(function () { goTo(current + 1); }, 6000); }
		dots.forEach(function (dot) {
			dot.addEventListener('click', function () {
				clearInterval(timer);
				goTo(parseInt(dot.dataset.slide));
				startAuto();
			});
		});
		startAuto();
	})();
</script>

<section class="modern-home-section featured-products-section bg-white">
	<div class="container">
		<div class="text-center mb-5">
			<span class="section-kicker" data-aos="fade-up">Handpicked Quality</span>
			<h2 class="section-heading" data-aos="fade-up">Featured Products</h2>
		</div>
		<div class="row justify-content-center align-items-stretch">
			<?php if (empty($featured_products)): ?>
			<div class="col-12 text-center py-4">
				<p class="text-muted mb-0">No featured products yet. In admin, set status to <strong>Active</strong> and enable <strong>Featured Product</strong>.</p>
			</div>
			<?php else: ?>
			<?php foreach ($featured_products as $i => $fp):
				$fp_url = ve_product_url($fp['slug']);
				$fp_img = ve_product_image_url($fp['image'] ?? null);
				$fp_name = $fp['name'];
				$aos_delay = $i > 0 ? ' data-aos-delay="' . ($i * 100) . '"' : '';
			?>
			<div class="col-lg-3 col-md-6 mb-4 d-flex" data-aos="fade-up"<?= $aos_delay ?>>
				<div class="collections-card">
					<div class="collections-img">
						<img src="<?= htmlspecialchars($fp_img) ?>" alt="<?= htmlspecialchars($fp_name) ?>">
					</div>
					<div class="collections-card-body">
						<h5><a href="<?= htmlspecialchars($fp_url) ?>"><?= htmlspecialchars($fp_name) ?></a></h5>
						<?php
						$fp_desc = !empty($fp['short_description'])
							? (strlen($fp['short_description']) > 90 ? substr($fp['short_description'], 0, 87) . '…' : $fp['short_description'])
							: '';
						?>
						<p class="featured-product-note<?= $fp_desc === '' ? ' featured-product-note--empty' : '' ?>"><?= $fp_desc !== '' ? htmlspecialchars($fp_desc) : '&nbsp;' ?></p>
						<div class="collections-card-footer">
							<a href="<?= htmlspecialchars($fp_url) ?>" class="btn">
								VIEW Details
								<svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M1 10.5203L6 6.02026L1 1.52026" stroke="currentColor" stroke-width="1.6"
										stroke-linecap="round" stroke-linejoin="round"></path>
								</svg>
							</a>
						</div>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
			<?php endif; ?>
		</div>
		<div class="text-center mt-5">
			<a href="<?= htmlspecialchars(ve_url('pure-ground-spices.php')) ?>" class="hero-btn">View All Products</a>
		</div>
	</div>
</section>


<section class="modern-home-section about-snapshot">
	<div class="container">
		<div class="row align-items-center gy-5">
			<div class="col-lg-6" data-aos="fade-right">
				<div class="about-image-card">
					<img src="images/certificates/WhatsApp Image 2026-05-13 at 11.44.53 AM.jpeg"
						alt="Vision Exim spice and export operations">
				</div>
			</div>
			<div class="col-lg-6" data-aos="fade-left">
				<div class="about-copy-card">
					<span class="section-kicker">Who We Are</span>
					<h2 class="section-heading">India-Based Export Company, Trusted Globally</h2>
					<p>Vision Exim is an India-based export company engaged in supplying high-quality spices, grains,
						pulses, and rice to international markets. Based in <strong>Rajkot, Gujarat</strong>, we work
						closely with trusted farmers and suppliers to ensure consistent quality and reliable supply.</p>
					<p class="mt-3">We focus on building long-term relationships with our clients by maintaining
						<strong>transparency</strong>, <strong>professionalism</strong>, and <strong>commitment</strong>
						in every transaction.
					</p>
					<div class="about-pillars mt-4">
						<div class="about-pillar-item">
							<span class="pillar-icon">🌾</span>
							<div>
								<strong>Direct Farm Sourcing</strong>
								<p>Procuring premium agricultural products directly from origin</p>
							</div>
						</div>
						<div class="about-pillar-item">
							<span class="pillar-icon">✅</span>
							<div>
								<strong>Export-Grade Quality Checks</strong>
								<p>Processing and quality checking as per international export standards</p>
							</div>
						</div>
						<div class="about-pillar-item">
							<span class="pillar-icon">📦</span>
							<div>
								<strong>Custom Packaging & Logistics</strong>
								<p>Customized packaging solutions and smooth, timely shipment management</p>
							</div>
						</div>
					</div>
					<a href="about-us.php" class="btn mt-4">Learn More About Us<svg width="7" height="12"
							viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M1 10.5203L6 6.02026L1 1.52026" stroke="currentColor" stroke-width="1.6"
								stroke-linecap="round" stroke-linejoin="round" />
						</svg></a>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="modern-home-section bg-white vision-section">
	<div class="container">
		<div class="row gy-4 justify-content-center">
			<div class="col-lg-5" data-aos="fade-right">
				<div class="vision-block">
					<span class="section-kicker">What We Do</span>
					<h2 class="section-heading">Our Specialization</h2>
					<ul class="vision-list mt-3">
						<li>Sourcing premium agricultural products directly from origin</li>
						<li>Processing and quality checking as per export standards</li>
						<li>Providing customized packaging solutions</li>
						<li>Managing smooth logistics and timely shipment</li>
					</ul>
					<p class="mt-3">Our product range — <strong>Spices, Grains, Pulses, and Rice</strong> — is carefully
						selected to meet global market requirements.</p>
				</div>
			</div>
			<div class="col-lg-5" data-aos="fade-left">
				<div class="vision-block vision-block--accent">
					<span class="section-kicker">Our Vision</span>
					<h2 class="section-heading">Globally Trusted Indian Exporter</h2>
					<p class="mt-3">Our vision is to become a globally trusted exporter of Indian agricultural products
						by delivering <strong>consistent quality</strong>, <strong>competitive pricing</strong>, and
						<strong>reliable service</strong>.
					</p>
					<p class="mt-3">We aim to create long-term value for our customers by being a dependable supply
						partner in the international market.</p>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="modern-home-section stats-band">
	<div class="container">
		<div class="text-center mb-5">
			<span class="section-kicker text-white" data-aos="fade-up" style="color:#fff;">Trusted Numbers</span>
			<h2 class="section-heading text-white" data-aos="fade-up">Built for trust, scalability, and long-term
				partnerships</h2>
		</div>
		<div class="row gy-4">
			<div class="col-md-6 col-xl-3" data-aos="zoom-in">
				<div class="stat-card">
					<div class="stat-icon">P</div>
					<h3><span class="count-number" data-target="200">0</span>+</h3>
					<p>Products</p>
				</div>
			</div>
			<div class="col-md-6 col-xl-3" data-aos="zoom-in">
				<div class="stat-card">
					<div class="stat-icon">C</div>
					<h3><span class="count-number" data-target="33">0</span>+</h3>
					<p>Happy Customers</p>
				</div>
			</div>
			<div class="col-md-6 col-xl-3" data-aos="zoom-in">
				<div class="stat-card">
					<div class="stat-icon">S</div>
					<h3><span class="count-number" data-target="100">0</span>+</h3>
					<p>Shipments Completed</p>
				</div>
			</div>
			<div class="col-md-6 col-xl-3" data-aos="zoom-in">
				<div class="stat-card">
					<div class="stat-icon">Y</div>
					<h3><span class="count-number" data-target="10">0</span>+</h3>
					<p>Years of Experience</p>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="modern-home-section bg-white">
	<div class="container">
		<div class="text-center mb-5">
			<span class="section-kicker" data-aos="fade-up">Our Working Process</span>
			<h2 class="section-heading" data-aos="fade-up">Professional Handling from Sourcing to Delivery</h2>
			<p class="section-intro" data-aos="fade-up">A reliable export workflow that keeps product quality, hygiene,
				and delivery timelines under control.</p>
		</div>
		<div class="row gy-4">
			<div class="col-md-6 col-xl-3" data-aos="fade-up">
				<div class="process-box">
					<div class="process-num">01</div>
					<div class="process-icon">S</div>
					<h4>Sourcing</h4>
					<p>We procure directly from trusted farms and supply partners to maintain authenticity at origin.
					</p>
				</div>
			</div>
			<div class="col-md-6 col-xl-3" data-aos="fade-up">
				<div class="process-box">
					<div class="process-num">02</div>
					<div class="process-icon">P</div>
					<h4>Processing</h4>
					<p>Products are cleaned, sorted, blended, and packed with care using quality-focused methods.</p>
				</div>
			</div>
			<div class="col-md-6 col-xl-3" data-aos="fade-up">
				<div class="process-box">
					<div class="process-num">03</div>
					<div class="process-icon">Q</div>
					<h4>Quality Check</h4>
					<p>Strict inspection standards help us deliver consistent flavor, color, purity, and safe packaging.
					</p>
				</div>
			</div>
			<div class="col-md-6 col-xl-3" data-aos="fade-up">
				<div class="process-box">
					<div class="process-num">04</div>
					<div class="process-icon">D</div>
					<h4>Global Delivery</h4>
					<p>Safe logistics planning and export-ready dispatch ensure timely shipments to overseas buyers.</p>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="modern-home-section reach-section">
	<div class="container">
		<div class="row align-items-center gy-5">
			<div class="col-lg-5" data-aos="fade-right">
				<span class="section-kicker">Anywhere You Need Us</span>
				<h2 class="section-heading">Global Export Reach</h2>
				<p>Vision Exim supplies premium spices across international markets with a focus on:</p>
				<ul class="mb-4">
					<li><strong>Safe packaging</strong></li>
					<li><strong>Transparent communication</strong></li>
					<li><strong>On-time delivery</strong></li>
				</ul>
				<div class="row gy-3 mt-4">
					<div class="col-sm-6">
						<div class="category-card text-start">
							<div class="category-icon mx-0">ME</div>
							<h4>Middle East</h4>
							<p>Serving high-demand markets with efficient export documentation and reliable supply.</p>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="category-card text-start">
							<div class="category-icon mx-0">EU</div>
							<h4>Europe</h4>
							<p>Premium packaging suited for retail chains and private label buyers.</p>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="category-card text-start">
							<div class="category-icon mx-0">AF</div>
							<h4>Africa</h4>
							<p>Growing export partnerships with bulk buyers across East and West African markets.</p>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="category-card text-start">
							<div class="category-icon mx-0">AS</div>
							<h4>Asia</h4>
							<p>Supplying competitive-grade spices to South and Southeast Asian distributors.</p>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="category-card text-start">
							<div class="category-icon mx-0">FE</div>
							<h4>Far East</h4>
							<p>Expanding reach into Japan, South Korea, and China with export-compliant quality
								standards.</p>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-7" data-aos="fade-left">
				<div class="reach-map-card">
					<div class="reach-map-shell">
						<div class="region-pill region-europe">Europe</div>
						<div class="region-pill region-middle-east">Middle East</div>
						<div class="region-pill region-africa">Africa</div>
						<div class="region-pill region-asia">Asia</div>
						<div class="region-pill region-far-east">Far East</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="modern-home-section bg-white">
	<div class="container">
		<div class="text-center mb-5">
			<span class="section-kicker" data-aos="fade-up">Product Categories</span>
			<h2 class="section-heading" data-aos="fade-up">What We Export</h2>
			<p class="section-intro" data-aos="fade-up">Carefully sourced and export-ready agricultural products from
				the heart of India.</p>
		</div>
		<div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 gy-4 justify-content-center">
			<div class="col" data-aos="zoom-in">
				<div class="category-card">
					<div class="category-icon">🌶</div>
					<h4>Spices</h4>
					<p>Whole &amp; blended spices — turmeric, chilli, coriander, cumin, garam masala and more, processed
						to export standards.</p>
					<span class="cat-tag">Whole &amp; Blended</span>
				</div>
			</div>
			<div class="col" data-aos="zoom-in">
				<div class="category-card">
					<div class="category-icon">🫘</div>
					<h4>Pulses</h4>
					<p>High-protein lentils, chickpeas, moong, and more — cleaned, graded, and packed for international
						buyers.</p>
					<span class="cat-tag">Export Grade</span>
				</div>
			</div>
			<div class="col" data-aos="zoom-in">
				<div class="category-card">
					<div class="category-icon">🌾</div>
					<h4>Grain</h4>
					<p>Nutrient-rich wheat, millet, and other grains — cleaned and sorted to meet global market
						requirements.</p>
					<span class="cat-tag">Cleaned &amp; Sorted</span>
				</div>
			</div>
			<div class="col" data-aos="zoom-in">
				<div class="category-card">
					<div class="category-icon">🍚</div>
					<h4>Rice</h4>
					<p>Long-grain Basmati and non-Basmati rice varieties, hygienically processed for wholesale and
						retail export.</p>
					<span class="cat-tag">Basmati &amp; Non-Basmati</span>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="modern-home-section testimonials-section">
	<div class="container">
		<div class="text-center mb-5">
			<span class="section-kicker" data-aos="fade-up">Happy Clients</span>
			<h2 class="section-heading" data-aos="fade-up">What Our Buyers Say</h2>
		</div>
		<div class="row gy-4">
			<div class="col-lg-6" data-aos="fade-up">
				<div class="testimonial-card">
					<blockquote>“Excellent quality spices and timely delivery. Their team understands export
						requirements clearly and the product consistency has been very reliable.”</blockquote>
					<div class="testimonial-author">Client from UAE</div>
				</div>
			</div>
			<div class="col-lg-6" data-aos="fade-up">
				<div class="testimonial-card">
					<blockquote>“Packaging, communication, and shipment coordination were handled professionally. We
						value the purity and color consistency across repeat orders.”</blockquote>
					<div class="testimonial-author">Buyer from Europe</div>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="certifications-section">
	<div class="container">
		<div class="text-center mb-5">
			<span class="section-kicker" data-aos="fade-up">Accreditations</span>
			<h2 class="section-heading" data-aos="fade-up" style="font-size: 32px;">Our Certifications & Memberships</h2>
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

<!-- Our Collaboration / Branding Partners Section -->
<section class="collaboration-section">
	<div class="container">
		<div class="text-center mb-5">
			<span class="section-kicker" data-aos="fade-up">Our Network</span>
			<h2 class="section-heading" data-aos="fade-up">Branding Partners</h2>
			<p class="section-intro" data-aos="fade-up">We are proud to collaborate with some of the most trusted names in the food and spice industry.</p>
		</div>
		
		<div class="brand-grid">
			<?php
			$brands = [
				["name" => "Parle", "img" => "images/certificates/parle.png"],
				["name" => "Haldiram's", "img" => "images/certificates/haldiram.webp"],
				["name" => "MTR", "img" => "images/certificates/MTR_LOGO_-_New.png"],
				["name" => "Patanjali", "img" => "images/certificates/Patanjali-removebg-preview-1.png"],
				["name" => "MDH", "img" => "images/certificates/mdh-150x150-removebg-preview.webp"],
				["name" => "Suhana", "img" => "images/certificates/suhana.png"],
				["name" => "Bikano", "img" => "images/certificates/bikano.webp"],
				["name" => "Gulab Oils", "img" => "images/certificates/gulab-oil-150x150-removebg-preview.webp"],
				["name" => "Jagdish", "img" => "images/certificates/jagdish-logo2__1_-removebg-preview.webp"],
				["name" => "Bombaywalla", "img" => "images/certificates/Bombay_wala_buddha__1_-removebg-preview.webp"],
				["name" => "Beyond Snack", "img" => "images/certificates/Beyond_Snaks__1_-removebg-preview.webp"],
				["name" => "Mo'pleez", "img" => "images/certificates/mopleez.webp"],
			];

			foreach ($brands as $index => $brand) {
				$delay = $index * 100;
				echo "
				<div class='brand-item' data-aos='zoom-in' data-aos-delay='$delay'>
					<img src='{$brand['img']}' alt='{$brand['name']}'>
				</div>";
			}
			?>
		</div>
	</div>
</section>

<section class="modern-home-section bg-white pt-0">
	<div class="container">
		<div class="cta-panel" data-aos="zoom-in">
			<span class="section-kicker text-white" style="color:#fff;">Bulk Orders</span>
			<h2 class="section-heading text-white">Looking for Bulk Supply?</h2>
			<p>Partner with Vision Exim for export-quality spices at competitive prices. We offer wholesale supply,
				private labeling support, and global shipping coordination.</p>
			<div class="d-flex flex-wrap gap-3 mt-4">
				<a href="contact-us.php" class="hero-btn">👉 Get a Quote Today</a>
				<a href="contact-us.php" class="btn btn-outline-light">Contact Us</a>
			</div>
		</div>
	</div>
</section>

<?php include 'includes/footer.php'; ?>