<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/product-functions.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<header>
	<div class="header-bottom">
		<div class="header-main-bar">
			<div class="header-logo">
				<a href="<?= htmlspecialchars(ve_url('index.php')) ?>" class="logo-wrapper">
					<img src="<?= htmlspecialchars(ve_url('images/certificates/vision logo color (1).png')) ?>" alt="Vision Exim Logo" style="height: 45px; width: auto;">
				</a>
			</div>
			<nav class="header-navbar">
				<div class="navbar-toggle-close navbar-toggle">
					<span></span>
					<span></span>
					<span></span>
				</div>
				<ul>
					<li class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
						<a href="<?= htmlspecialchars(ve_url('index.php')) ?>">Home</a>
					</li>
					<li class="<?php echo ($current_page == 'about-us.php') ? 'active' : ''; ?>">
						<a href="<?= htmlspecialchars(ve_url('about-us.php')) ?>">About Us</a>
					</li>
					<li class='dropdown <?php echo in_array($current_page, ['our-products.php', 'pure-ground-spices.php']) ? 'active' : ''; ?>'>
						<a href="#">Our Products
							<svg width="8" height="6" viewBox="0 0 8 6" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M1 1.5L4 4.5L7 1.5" stroke="currentColor" stroke-width="1.4"
									stroke-linecap="round" stroke-linejoin="round" />
							</svg>
						</a>
						<span class="sub-menu-toggle"></span>
						<ul class="sub-menu">
							<li><a href="<?= htmlspecialchars(ve_url('pure-ground-spices.php')) ?>">All Products</a></li>
							<?php
							$nav_categories = ve_get_parent_categories();
							if (!empty($nav_categories)) {
								foreach ($nav_categories as $cat) {
									$cat_url = ve_url('pure-ground-spices.php?category=' . urlencode($cat['slug']));
									$subs = ve_get_subcategories((int)$cat['id']);
									if (!empty($subs)) {
										?>
										<li class="dropdown">
											<a href="<?= htmlspecialchars($cat_url) ?>"><?= htmlspecialchars($cat['name']) ?>
												<svg width="8" height="6" viewBox="0 0 8 6" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M1 1.5L4 4.5L7 1.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
												</svg>
											</a>
											<span class="sub-menu-toggle"></span>
											<ul class="sub-menu">
												<?php foreach ($subs as $sub): ?>
													<li><a href="<?= htmlspecialchars(ve_url('pure-ground-spices.php?category=' . urlencode($sub['slug']))) ?>"><?= htmlspecialchars($sub['name']) ?></a></li>
												<?php endforeach; ?>
											</ul>
										</li>
										<?php
									} else {
										?>
										<li><a href="<?= htmlspecialchars($cat_url) ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
										<?php
									}
								}
							}
							?>
						</ul>
					</li>
					<li class="<?php echo ($current_page == 'categories.php') ? 'active' : ''; ?>">
						<a href="<?= htmlspecialchars(ve_url('categories.php')) ?>">Categories</a>
					</li>
					<li class="<?php echo ($current_page == 'harvest-chart.php') ? 'active' : ''; ?>">
						<a href="<?= htmlspecialchars(ve_url('harvest-chart.php')) ?>">Harvest Chart</a>
					</li>


				</ul>
			</nav>

			<div class="contact_wrp d-flex align-items-center">

				<a href="<?= htmlspecialchars(ve_url('contact-us.php')) ?>" class="btn">Contact Us</a>
			</div>

			<div class="navbar-toggle">
				<span></span>
				<span></span>
				<span></span>
			</div>
		</div>
	</div>
	<div class="header-menu-overlay"></div>
</header>