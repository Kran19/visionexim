<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<style>
	/* Harvesting Chart Page Specific Styles */
	.harvest-page-hero {
		background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&q=80&w=1600') center/cover no-repeat;
		padding: 120px 0 80px;
		color: #fff;
		text-align: center;
		margin-top: 70px;
	}

	.harvest-section {
		background: #fdfaf5;
		padding: 100px 0;
	}

	.harvest-table-wrap {
		overflow-x: auto;
		background: #fff;
		border-radius: 30px;
		padding: 40px;
		box-shadow: 0 30px 60px rgba(0, 0, 0, 0.05);
		border: 1px solid rgba(0, 0, 0, 0.05);
	}

	.harvest-table {
		width: 100%;
		min-width: 900px;
		border-collapse: separate;
		border-spacing: 0 10px;
	}

	.harvest-table th {
		padding: 20px 10px;
		text-align: center;
		color: #4b5563;
		background: rgba(90, 130, 102, 0.05);
		font-weight: 700;
		text-transform: uppercase;
		font-size: 13px;
		letter-spacing: 0.1em;
	}

	.harvest-table td {
		padding: 15px 10px;
		vertical-align: middle;
	}

	.spice-name {
		font-weight: 700;
		color: #374151; /* Dark Slate */
		font-size: 16px;
		width: 180px;
		background: #fafafa;
		border-radius: 12px 0 0 12px;
		padding-left: 25px !important;
	}

	.month-cell {
		text-align: center;
		position: relative;
	}

	.harvest-status {
		width: 100%;
		height: 10px;
		background: #f0f0f0;
		border-radius: 5px;
		display: block;
		position: relative;
	}

	.harvest-status.active {
		background: #5a8266; /* Soothing Sage Green */
		box-shadow: 0 0 15px rgba(90, 130, 102, 0.2);
	}

	.harvest-status.active::after {
		content: "";
		position: absolute;
		inset: -4px;
		border-radius: 10px;
		border: 1px solid rgba(90, 130, 102, 0.15);
	}

	.harvest-legend {
		display: flex;
		justify-content: center;
		gap: 30px;
		margin-top: 40px;
	}

	.legend-item {
		display: flex;
		align-items: center;
		gap: 10px;
		font-size: 14px;
		font-weight: 600;
		color: #555;
	}

	.legend-dot {
		width: 12px;
		height: 12px;
		border-radius: 3px;
	}

	.dot-active { background: #5a8266; }
	.dot-inactive { background: #f0f0f0; }

	.info-card {
		background: #fff;
		padding: 40px;
		border-radius: 24px;
		box-shadow: 0 20px 40px rgba(0,0,0,0.04);
		margin-top: 50px;
	}

	.info-card h4 {
		color: #5a8266;
		margin-bottom: 15px;
	}

	.info-card p {
		color: #666;
		line-height: 1.8;
	}
</style>

<div class="harvest-page-hero">
	<div class="container">
		<h1 class="display-4 fw-bold mb-3" data-aos="fade-up">Harvesting Chart</h1>
		<p class="lead" data-aos="fade-up" data-aos-delay="100">Plan your procurement with our seasonal availability guide.</p>
	</div>
</div>

<section class="harvest-section">
	<div class="container">
		<div class="text-center mb-5">
			<span class="section-kicker" data-aos="fade-up">Nature's Cycle</span>
			<h2 class="section-heading" data-aos="fade-up">Spices Harvesting Calendar</h2>
			<p class="section-intro" data-aos="fade-up">Track the seasonal cycles of premium Indian spices to plan your procurement when they are at their freshest.</p>
		</div>

		<div class="harvest-table-wrap" data-aos="fade-up">
			<table class="harvest-table">
				<thead>
					<tr>
						<th>Product</th>
						<th>Jan</th>
						<th>Feb</th>
						<th>Mar</th>
						<th>Apr</th>
						<th>May</th>
						<th>Jun</th>
						<th>Jul</th>
						<th>Aug</th>
						<th>Sep</th>
						<th>Oct</th>
						<th>Nov</th>
						<th>Dec</th>
					</tr>
				</thead>
				<tbody>
					<?php
				// Dynamic: fetch from database
				require_once 'admin/includes/db.php';
				$check = $conn->query("SHOW COLUMNS FROM harvest_calendar LIKE 'image'");
				if ($check && $check->num_rows == 0) {
				    $conn->query("ALTER TABLE harvest_calendar ADD COLUMN image VARCHAR(255) DEFAULT NULL");
				}
				$month_cols = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec_month'];
				$harvest_result = $conn->query("SELECT * FROM harvest_calendar ORDER BY sort_order ASC, spice_name ASC");
				
				if ($harvest_result && $harvest_result->num_rows > 0) {
					while ($row = $harvest_result->fetch_assoc()) {
						$img = !empty($row['image']) ? ve_url(ltrim((string)$row['image'], '/')) : null;
						echo "<tr>";
						echo "<td class='spice-name'><div style='display:flex;align-items:center;gap:10px;'>";
						if ($img) echo "<img src='$img' alt='" . htmlspecialchars($row['spice_name']) . "' style='width:38px;height:38px;border-radius:8px;object-fit:cover;flex-shrink:0;border:1px solid #eee;'>";
						echo "<span>" . htmlspecialchars($row['spice_name']) . "</span></div></td>";
						foreach ($month_cols as $col) {
							if ((int)$row[$col] > 0 && $img) {
								echo "<td class='month-cell'><img src='$img' alt='' style='width:34px;height:34px;border-radius:6px;object-fit:cover;margin:auto;display:block;'></td>";
							} elseif ((int)$row[$col] > 0) {
								echo "<td class='month-cell'><span class='harvest-status active'></span></td>";
							} else {
								echo "<td class='month-cell'><span style='display:block;width:34px;height:34px;margin:auto;'></span></td>";
							}
						}
						echo "</tr>";
					}
				} else {
					echo "<tr><td colspan='13' style='text-align:center;padding:40px;color:#999;'>No harvest data available.</td></tr>";
				}
				?>
				</tbody>
			</table>
			<div class="harvest-legend">
				<div class="legend-item"><span class="legend-dot dot-active"></span> Harvesting Period</div>
				<div class="legend-item"><span class="legend-dot dot-inactive"></span> Off Season</div>
			</div>
		</div>

		<div class="row mt-5">
			<div class="col-lg-6" data-aos="fade-right">
				<div class="info-card">
					<h4>Why Harvest Timing Matters?</h4>
					<p>Buying spices during their peak harvest season ensures maximum flavor, aroma, and color intensity. It also allows for better pricing and consistent supply for long-term contracts.</p>
				</div>
			</div>
			<div class="col-lg-6" data-aos="fade-left">
				<div class="info-card">
					<h4>Our Sourcing Strategy</h4>
					<p>At Vision Exim, we work directly with farmers in Rajkot and surrounding regions to secure the best of the harvest. We monitor crop progress throughout the year to keep our clients updated on market trends.</p>
				</div>
			</div>
		</div>
	</div>
</section>

<?php include 'includes/footer.php'; ?>
