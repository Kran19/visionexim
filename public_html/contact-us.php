<?php
$page_title = "Contact Us | Vision Exim";
include 'includes/header.php';
?>
<?php include 'includes/navbar.php'; ?>

<style>
    /* ── SECTION ── */
    .contactus-page-section {
        padding: 60px 0 80px;
    }

    .contactus-row {
        display: grid;
        grid-template-columns: 1fr 1.55fr;
        gap: 30px;
        align-items: start;
    }

    /* ── SHARED CARD ── */
    .contactus-content,
    .contactus-form {
        background: #fff;
        border-radius: 26px;
        box-shadow: 0 20px 45px rgba(24, 24, 36, 0.06);
        padding: 40px 38px;
        height: 100%;
        box-sizing: border-box;
    }

    .contactus-content::after {
        display: none;
    }

    /* ── LEFT PANEL ── */
    .contactus-title h6 {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: var(--primary);
        margin: 0 0 10px;
    }

    .contactus-title h2.title {
        font-size: 30px;
        margin: 0 0 12px;
        line-height: 1.25;
    }

    .contactus-title p {
        font-size: 15px;
        color: var(--text);
        line-height: 1.7;
        margin: 0 0 32px;
    }

    /* ── HEX ICON ITEMS ── */
    .ci-items {
        display: flex;
        flex-direction: column;
        gap: 22px;
    }

    .ci-item {
        display: flex;
        align-items: flex-start;
        gap: 16px;
    }

    .ci-hex {
        flex-shrink: 0;
        width: 50px;
        height: 50px;
        position: relative;
    }

    .ci-hex-bg {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
    }

    .ci-hex-inner {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .ci-hex-inner svg {
        width: 19px;
        height: 19px;
        stroke: var(--primary);
        fill: none;
        stroke-width: 1.8px;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .ci-item-label {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: var(--primary);
        margin-bottom: 5px;
    }

    .ci-item-val {
        font-size: 15px;
        color: var(--dark);
        font-weight: 500;
        line-height: 1.65;
    }

    .ci-item-val a {
        color: var(--dark);
        text-decoration: none;
        transition: color 0.2s;
    }

    .ci-item-val a:hover {
        color: var(--primary);
    }

    .contact-quick-links {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 32px;
        list-style: none;
        padding: 0;
    }

    .contact-quick-links a {
        min-width: 130px;
        text-align: center;
    }

    /* ── RIGHT PANEL (FORM) ── */
    .contactus-form .title h2 {
        font-size: 30px;
        margin: 0 0 8px;
    }

    .form-helper {
        font-size: 14px;
        color: var(--text);
        margin: 0 0 24px;
        line-height: 1.6;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .form-grid .form-full {
        grid-column: 1 / -1;
    }

    .form-grid .form-feild {
        margin: 0;
    }

    .form-grid .form-feild input,
    .form-grid .form-feild textarea {
        width: 100%;
        box-sizing: border-box;
    }

    .form-submit {
        margin-top: 22px;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .contact-form-status {
        display: none;
        font-size: 13px;
        color: #1f6b35;
        font-weight: 600;
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 991px) {
        .contactus-row {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 575px) {

        .contactus-content,
        .contactus-form {
            padding: 28px 22px;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-grid .form-full {
            grid-column: auto;
        }

        .contact-quick-links a {
            width: 100%;
        }
    }
</style>

<section class="inner_banner banner-contact">
    <div class="container">
        <div class="title text-center">
            <h2>Contact Us</h2>
            <p style="max-width:760px;margin:14px auto 0;">Reach Vision Exim for quotations, export discussions, product
                inquiries, and partnership opportunities.</p>
        </div>
    </div>
</section>

<section class="contactus-page-section">
    <div class="container">
        <div class="contactus-row">

            <!-- ── LEFT: Contact Info ── -->
            <div class="contactus-content">
                <div class="contactus-title">
                    <h6>Vision Exim</h6>
                    <h2 class="title">Get In Touch</h2>
                    <p>Feel free to reach out for quotations, export inquiries, or partnership discussions. We're here
                        to assist and eager to help.</p>
                </div>

                <div class="ci-items">

                    <!-- Address -->
                    <div class="ci-item">
                        <div class="ci-hex">
                            <svg class="ci-hex-bg" viewBox="0 0 52 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26 2L49 15V45L26 58L3 45V15L26 2Z" fill="var(--primary)" fill-opacity="0.09"
                                    stroke="var(--primary)" stroke-opacity="0.22" stroke-width="1.2" />
                            </svg>
                            <div class="ci-hex-inner">
                                <svg viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" />
                                    <circle cx="12" cy="9" r="2.5" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="ci-item-label">Address</div>
                            <div class="ci-item-val">
                                <strong>Registered Office:</strong><br>
                                1008, The One World, B-wing, 10th floor,<br>
                                150ft ring road, Rajkot (India)-360005<br><br>
                                <strong>Factory:</strong><br>
                                Bombay Super Industrial Zone-11, Plot No.10,<br>
                                Wankaner Road, Jhiyana, Gujarat-360023
                            </div>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="ci-item">
                        <div class="ci-hex">
                            <svg class="ci-hex-bg" viewBox="0 0 52 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26 2L49 15V45L26 58L3 45V15L26 2Z" fill="var(--primary)" fill-opacity="0.09"
                                    stroke="var(--primary)" stroke-opacity="0.22" stroke-width="1.2" />
                            </svg>
                            <div class="ci-hex-inner">
                                <svg viewBox="0 0 24 24">
                                    <path
                                        d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.7 11.3a19.79 19.79 0 01-3.07-8.68A2 2 0 012.61 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 9.91a16 16 0 006.18 6.18l.98-.98a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="ci-item-label">Phone</div>
                            <div class="ci-item-val">
                                <a href="tel:+919998400058">+91 99984 00058</a><br>
                                <a href="tel:+919033555294">+91 90335 55294</a>
                            </div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="ci-item">
                        <div class="ci-hex">
                            <svg class="ci-hex-bg" viewBox="0 0 52 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26 2L49 15V45L26 58L3 45V15L26 2Z" fill="var(--primary)" fill-opacity="0.09"
                                    stroke="var(--primary)" stroke-opacity="0.22" stroke-width="1.2" />
                            </svg>
                            <div class="ci-hex-inner">
                                <svg viewBox="0 0 24 24">
                                    <path
                                        d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                    <polyline points="22,6 12,13 2,6" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="ci-item-label">E-mail</div>
                            <div class="ci-item-val">
                                <a href="mailto:export@visionexims.com">export@visionexims.com</a><br>
                                <a href="mailto:info@visionexims.com">info@visionexims.com</a><br>
                                <a href="mailto:visionexims@gmail.com">visionexims@gmail.com</a>
                            </div>
                        </div>
                    </div>

                    <!-- Working Hours -->
                    <div class="ci-item">
                        <div class="ci-hex">
                            <svg class="ci-hex-bg" viewBox="0 0 52 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26 2L49 15V45L26 58L3 45V15L26 2Z" fill="var(--primary)" fill-opacity="0.09"
                                    stroke="var(--primary)" stroke-opacity="0.22" stroke-width="1.2" />
                            </svg>
                            <div class="ci-hex-inner">
                                <svg viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 6 12 12 16 14" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="ci-item-label">Working Hours</div>
                            <div class="ci-item-val">Mon – Fri: 9am – 6pm<br>Sun: 12pm – 6pm</div>
                        </div>
                    </div>

                </div><!-- /ci-items -->

                <ul class="contact-quick-links">
                    <li><a href="tel:+919998400058" class="btn">Call Now</a></li>
                    <li><a href="mailto:export@visionexims.com" class="btn">Email Us</a></li>
                </ul>
            </div>

            <!-- ── RIGHT: Form ── -->
            <div class="contactus-form">
                <div class="title">
                    <h2>Send Your Inquiry</h2>
                </div>
                <p class="form-helper">Fill in your details and our team will respond within 24 hours.</p>

                <!-- Success State -->
                <div id="formSuccess" style="display:none;padding:30px 0;text-align:center;">
                    <div style="width:60px;height:60px;background:rgba(90,130,102,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#5a8266" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <h3 style="color:#1f2937;font-size:20px;margin-bottom:8px;">Inquiry Received!</h3>
                    <p style="color:#6b7280;font-size:14px;line-height:1.6;">Thank you for reaching out. Our export team will get back to you within 24 hours.</p>
                </div>

                <!-- Error Alert -->
                <div id="formError" style="display:none;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:12px;color:#dc2626;font-size:13px;font-weight:600;margin-bottom:16px;"></div>

                <form id="contactForm">
                    <div class="form-grid">
                        <div class="form-feild">
                            <input type="text" class="form-control" id="contactName" name="name" placeholder="Your Name" required>
                        </div>
                        <div class="form-feild">
                            <input type="email" class="form-control" id="contactEmail" name="email" placeholder="Email Address" required>
                        </div>
                        <div class="form-feild">
                            <input type="tel" class="form-control" id="contactPhone" name="phone" placeholder="Phone Number">
                        </div>
                        <div class="form-feild">
                            <input type="text" class="form-control" id="contactCompany" name="company" placeholder="Company / Business Name">
                        </div>
                        <div class="form-feild form-full">
                            <input type="text" class="form-control" id="contactSubject" name="subject" placeholder="Subject (e.g. Cumin Seeds Export Inquiry)">
                        </div>
                        <div class="form-feild form-full">
                            <textarea class="form-control" id="contactMessage" name="message" placeholder="Tell us about your requirement" required></textarea>
                        </div>
                    </div>

                    <div class="form-submit">
                        <button type="submit" class="btn" id="submitBtn">Submit Inquiry</button>
                        <span id="submitSpinner" style="display:none;font-size:13px;color:#5a8266;font-weight:600;">Sending…</span>
                    </div>
                </form>
            </div>

        </div>
    </div>
</section>

<script>
    document.getElementById('contactForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const btn     = document.getElementById('submitBtn');
        const spinner = document.getElementById('submitSpinner');
        const errBox  = document.getElementById('formError');

        // Show loading state
        btn.disabled = true;
        btn.style.opacity = '0.6';
        spinner.style.display = 'inline';
        errBox.style.display = 'none';

        const formData = new FormData(this);

        fetch('submit-inquiry.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Hide form, show success panel
                document.getElementById('contactForm').style.display = 'none';
                document.getElementById('formSuccess').style.display = 'block';
            } else {
                errBox.textContent = data.message || 'Something went wrong. Please try again.';
                errBox.style.display = 'block';
                btn.disabled = false;
                btn.style.opacity = '1';
            }
        })
        .catch(() => {
            errBox.textContent = 'Network error. Please check your connection and try again.';
            errBox.style.display = 'block';
            btn.disabled = false;
            btn.style.opacity = '1';
        })
        .finally(() => {
            spinner.style.display = 'none';
        });
    });
</script>

<?php include 'includes/footer.php'; ?>
