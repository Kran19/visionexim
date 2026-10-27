<!-- ================= FOOTER START ================= -->
<?php require_once __DIR__ . '/config.php'; ?>
<footer>
	<div class="footer-top">
		<div class="container">
			<div class="row justify-content-between">

				<!-- Quick Links -->
				<div class="col-lg-4 col-md-7 order-1 order-lg-0">
					<div class="quick_links">
						<div class="row">

							<div class="col-md-6 mb-3 mb-md-0">
								<h4>Quick Links</h4>
								<ul>
									<li><a href="<?= htmlspecialchars(ve_url('index.php')) ?>">Home</a></li>
									<li><a href="<?= htmlspecialchars(ve_url('about-us.php')) ?>">About Us</a></li>
									<li><a href="<?= htmlspecialchars(ve_url('our-products.php')) ?>">Our Products</a></li>
									<!-- <li><a href="<?= htmlspecialchars(ve_url('infrastructure.php')) ?>">Infrastructure</a></li> -->
									<li><a href="<?= htmlspecialchars(ve_url('contact-us.php')) ?>">Contact Us</a></li>
								</ul>
							</div>

							<div class="col-md-6 mb-3 mb-md-0">
								<h4>Our Products</h4>
								<ul>
									<li><a href="<?= htmlspecialchars(ve_url('pure-ground-spices.php?category=spices')) ?>">Spices</a></li>
									<li><a href="<?= htmlspecialchars(ve_url('pure-ground-spices.php?category=grains')) ?>">Grains</a></li>
									<li><a href="<?= htmlspecialchars(ve_url('pure-ground-spices.php?category=rice')) ?>">Rice</a></li>
									<li><a href="<?= htmlspecialchars(ve_url('pure-ground-spices.php?category=flour')) ?>">Flour</a></li>
									<li><a href="<?= htmlspecialchars(ve_url('pure-ground-spices.php?category=penuts')) ?>">Peanut</a></li>
								</ul>
							</div>

						</div>
					</div>
				</div>

				<!-- Logo & Description -->
				<div class="col-md-12 col-lg-3">
					<div class="description_social text-center">
						<div class="footer-logo mb-3">
							<a href="<?= htmlspecialchars(ve_url('index.php')) ?>">
								<img src="<?= htmlspecialchars(ve_url('images/certificates/vision logo color (1).png')) ?>"
									alt="Vision Exim Logo"
									style="height:60px;width:auto;">
							</a>
						</div>

						<p>
							Vision Exim is a premier manufacturer and exporter of high-quality Indian spices and
							agricultural products. Committed to purity and authentic taste, we bring the best of
							Indian flavors to customers worldwide.
						</p>
					</div>
				</div>

				<!-- Contact Us -->
				<div class="col-lg-4 col-md-5 ps-lg-5 ps-md-0 order-2 order-lg-0">

					<div class="contect-information">

						<h4>Contact Us</h4>

						<ul>

							<!-- Address -->
							<li>

								<div class="icon location">
									<svg width="17" height="20" viewBox="0 0 17 20" fill="none"
										xmlns="http://www.w3.org/2000/svg">

										<path
											d="M1.27432 6.88245C2.93385 -0.591568 13.7419 -0.582937 15.393 6.89108C16.3617 11.2754 13.6997 14.9865 11.3663 17.2822C8.82119 19.9467 7.76419 19.9467 5.29258 17.2822C2.96755 14.9865 0.305561 11.2667 1.27432 6.88245Z"
											stroke="currentColor"
											stroke-width="1.4"></path>

										<path
											d="M8.33337 11.1452C9.78493 11.1452 10.9617 9.93963 10.9617 8.45248C10.9617 6.96534 9.78493 5.75977 8.33337 5.75977C6.8818 5.75977 5.70508 6.96534 5.70508 8.45248C5.70508 9.93963 6.8818 11.1452 8.33337 11.1452Z"
											stroke="currentColor"
											stroke-width="1.4"></path>
									</svg>
								</div>

								<div>
									<p class="mb-2">
										<strong>Registered Office:</strong>
										1008, The One World, B-wing, 10th floor,
										150ft ring road, Rajkot (India)-360005
									</p>

									<p class="mb-0">
										<strong>Factory:</strong>
										Bombay Super Industrial Zone-11,
										Plot No.10, Wankaner Road,
										Jhiyana, Gujarat-360023
									</p>
								</div>

							</li>

							<!-- Email -->
							<li>

								<div class="icon">
									✉
								</div>

								<a href="mailto:export@visionexims.com">
									export@visionexims.com
								</a>

							</li>

							<!-- General Info -->
							<li>

								<div class="icon">
									✉
								</div>

								<a href="mailto:info@visionexims.com">
									info@visionexims.com
								</a>

							</li>

							<!-- Gmail -->
							<li>

								<div class="icon">
									✉
								</div>

								<a href="mailto:visionexims@gmail.com">
									visionexims@gmail.com
								</a>

							</li>

							<!-- Phone -->
							<li>

								<div class="icon">
									📞
								</div>

								<a href="tel:+919998400058">
									+91 99984 00058
								</a>

							</li>

							<!-- Phone -->
							<li>

								<div class="icon">
									📞
								</div>

								<a href="tel:+919033555294">
									+91 90335 55294
								</a>

							</li>

						</ul>

					</div>

				</div>

			</div>
		</div>
	</div>

	<!-- Footer Bottom -->
	<div class="footer-bottom">
		<div class="container">
			<div class="row align-items-center justify-content-center">
				<div class="col-auto">
					<p>
						Copyright © 2026, Vision Exim. All rights reserved.
					</p>
				</div>
			</div>
		</div>
	</div>
</footer>

<!-- ================= FOOTER CSS ================= -->

<style>

/* ================= CONTACT US RESPONSIVE ================= */

.contect-information ul {
	padding: 0;
	margin: 0;
	list-style: none;
}

.contect-information ul li {
	display: flex;
	align-items: flex-start;
	gap: 15px;
	margin-bottom: 20px;
	word-break: break-word;
}

.contect-information ul li .icon {
	min-width: 22px;
	margin-top: 4px;
	font-size: 18px;
}

/* KEEP SAME ORIGINAL COLORS */
.contect-information ul li p,
.contect-information ul li a {
	margin: 0;
	font-size: 15px;
	line-height: 1.7;
	color: inherit;
	text-decoration: none;
	overflow-wrap: break-word;
}

.contect-information ul li p + p {
	margin-top: 10px;
}

/* Footer Bottom */
.footer-bottom {
	padding: 15px 0;
	text-align: center;
}

/* ================= MOBILE RESPONSIVE ================= */

@media (max-width: 991px) {

	.contect-information {
		margin-top: 25px;
	}

	.description_social {
		margin: 30px 0;
	}
}

@media (max-width: 767px) {

	.footer-top {
		padding: 40px 0;
	}

	.contect-information h4 {
		font-size: 22px;
		margin-bottom: 20px;
	}

	.contect-information ul li {
		gap: 12px;
		margin-bottom: 18px;
	}

	.contect-information ul li p,
	.contect-information ul li a {
		font-size: 14px;
		line-height: 1.6;
	}

	.footer-logo img {
		height: 50px !important;
	}

	.footer-bottom p {
		font-size: 13px;
		margin: 0;
	}
}

</style>

<!-- ================= JS FILES ================= -->

<script src="<?= htmlspecialchars(ve_url('assets/js/vendor/jquery-2.2.4.min.js')) ?>"></script>

<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script src="<?= htmlspecialchars(ve_url('assets/js/vendor/owl.carousel.min.js')) ?>"></script>

<script src="<?= htmlspecialchars(ve_url('assets/js/main.js')) ?>"></script>

<script>

	AOS.init();

	$(document).ready(function () {

		$('.certificate-carousel').owlCarousel({

			loop: true,
			items: 4,
			margin: 37,
			nav: true,
			dots: false,

			responsive: {

				0: {
					items: 1
				},

				768: {
					items: 3,
					margin: 20
				},

				992: {
					items: 4
				}
			}
		});
	});

	/* Counter Animation */

	const countElements = document.querySelectorAll('.count-number');

	if (countElements.length) {

		const animateCounter = (element) => {

			const target = Number(element.dataset.target || 0);

			const duration = 1600;

			const startTime = performance.now();

			const updateCounter = (currentTime) => {

				const progress = Math.min(
					(currentTime - startTime) / duration,
					1
				);

				element.textContent = Math.floor(progress * target);

				if (progress < 1) {
					requestAnimationFrame(updateCounter);
				} else {
					element.textContent = target;
				}
			};

			requestAnimationFrame(updateCounter);
		};

		const observer = new IntersectionObserver((entries, obs) => {

			entries.forEach((entry) => {

				if (entry.isIntersecting) {

					animateCounter(entry.target);

					obs.unobserve(entry.target);
				}
			});

		}, {
			threshold: 0.45
		});

		countElements.forEach((element) => observer.observe(element));
	}

</script>

<!-- ================= FOOTER END ================= -->