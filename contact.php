<?php
$conn = new mysqli("localhost", "root", "", "myscript_db");
$success = "";
$error   = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($conn->connect_error) {
        $error = "Database connection failed. Please try again later.";
    } else {
        $fullname = trim($_POST['fullname'] ?? '');
        $email    = trim($_POST['email']    ?? '');
        $phone    = trim($_POST['phone']    ?? '');
        $subject  = trim($_POST['subject']  ?? '');
        $message  = trim($_POST['message']  ?? '');

        if (!empty($fullname) && !empty($email)) {
            $stmt = $conn->prepare("INSERT INTO contacts (fullname, email, phone, message) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $fullname, $email, $phone, $message);
            if ($stmt->execute()) {
                $success = "Thank you, {$fullname}! Your message has been received. We'll be in touch within 24 hours.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
            $stmt->close();
        } else {
            $error = "Name and Email are required fields.";
        }
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact Us — MindSpace</title>
  <link rel="stylesheet" href="style.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>

    /* ---- LAYOUT ---- */
    .contact-layout {
      display: grid;
      grid-template-columns: 1fr 380px;
      gap: 32px;
      padding: 56px 5% 72px;
      max-width: 1100px;
      margin: 0 auto;
      align-items: start;
    }

    /* ---- FORM SECTION ---- */
    .form-card {
      background: white;
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      overflow: hidden;
      box-shadow: var(--shadow-sm);
    }

    .form-card-header {
      background: var(--forest);
      padding: 28px 32px;
      color: white;
      position: relative;
      overflow: hidden;
    }

    .form-card-header::after {
      content: '';
      position: absolute;
      top: -50px; right: -50px;
      width: 200px; height: 200px;
      border-radius: 50%;
      background: rgba(122,171,138,0.15);
      pointer-events: none;
    }

    .form-card-header h2 {
      font-family: var(--font-display);
      font-size: 1.45rem;
      margin-bottom: 5px;
      position: relative; z-index: 1;
    }

    .form-card-header p {
      opacity: 0.7;
      font-size: 0.88rem;
      position: relative; z-index: 1;
    }

    .form-body { padding: 32px; }

    /* Alert messages */
    .alert {
      padding: 14px 18px;
      border-radius: var(--radius-sm);
      margin-bottom: 20px;
      font-size: 0.88rem;
      font-weight: 600;
      display: flex;
      align-items: flex-start;
      gap: 10px;
    }

    .alert-success {
      background: var(--sage-xlight);
      color: var(--forest);
      border: 1px solid var(--sage);
    }

    .alert-error {
      background: #fef2f2;
      color: #dc2626;
      border: 1px solid #fecaca;
    }

    /* Form grid */
    .form-grid-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    .subject-select {
      width: 100%;
      padding: 11px 16px;
      border: 1.5px solid var(--border-dark);
      border-radius: var(--radius-sm);
      font-family: var(--font-body);
      font-size: 0.95rem;
      color: var(--ink);
      background: var(--ivory);
      outline: none;
      cursor: pointer;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    .subject-select:focus {
      border-color: var(--forest-lit);
      box-shadow: 0 0 0 3px rgba(61,122,92,0.12);
    }

    .form-submit-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: 8px;
      flex-wrap: wrap;
      gap: 12px;
    }

    .form-notice {
      font-size: 0.78rem;
      color: var(--ink-xlight);
      display: flex;
      align-items: center;
      gap: 6px;
      font-family: var(--font-mono);
    }

    /* ---- RIGHT SIDEBAR ---- */
    .contact-sidebar {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    /* Counselors */
    .counselors-card {
      background: white;
      border: 1px solid var(--border);
      border-radius: var(--radius-md);
      overflow: hidden;
      box-shadow: var(--shadow-sm);
    }

    .counselors-header {
      padding: 16px 22px;
      border-bottom: 1px solid var(--border);
    }

    .counselors-header h3 {
      font-family: var(--font-display);
      font-size: 1rem;
      font-weight: 400;
      color: var(--ink);
    }

    .counselors-header p {
      font-size: 0.76rem;
      color: var(--ink-xlight);
      margin-top: 2px;
    }

    .counselor-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 14px 22px;
      border-bottom: 1px solid var(--border);
      transition: background 0.15s;
    }

    .counselor-item:last-child { border-bottom: none; }
    .counselor-item:hover { background: var(--ivory); }

    .c-avatar {
      width: 40px; height: 40px;
      border-radius: 50%;
      color: white;
      display: flex; align-items: center; justify-content: center;
      font-weight: 700;
      font-size: 0.82rem;
      flex-shrink: 0;
      font-family: var(--font-mono);
    }

    .c-info { flex: 1; min-width: 0; }
    .c-info h5 {
      font-size: 0.86rem;
      font-weight: 700;
      color: var(--ink);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .c-info p { font-size: 0.74rem; color: var(--ink-xlight); }

    .call-btn {
      background: var(--sage-xlight);
      color: var(--forest);
      font-size: 0.74rem;
      font-weight: 700;
      padding: 6px 12px;
      border-radius: 99px;
      white-space: nowrap;
      transition: background 0.15s;
      font-family: var(--font-mono);
    }

    .call-btn:hover { background: var(--sage-light); }

    /* Hours card */
    .hours-card {
      background: white;
      border: 1px solid var(--border);
      border-radius: var(--radius-md);
      overflow: hidden;
      box-shadow: var(--shadow-sm);
    }

    .hours-header {
      padding: 14px 22px;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .hours-header h3 {
      font-family: var(--font-display);
      font-size: 1rem;
      font-weight: 400;
    }

    .open-badge {
      margin-left: auto;
      font-size: 0.7rem;
      font-weight: 700;
      font-family: var(--font-mono);
      padding: 3px 9px;
      border-radius: 99px;
    }
    .open-badge.open { background: var(--sage-xlight); color: var(--forest); }
    .open-badge.closed { background: var(--coral-light); color: var(--coral); }

    .hours-body { padding: 8px 0; }

    .hours-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 22px;
      font-size: 0.84rem;
      transition: background 0.15s;
    }

    .hours-row:hover { background: var(--ivory); }
    .hours-row.today { background: var(--sage-xlight); font-weight: 700; }

    .hours-row .day { color: var(--ink-mid); }
    .hours-row .time { color: var(--forest); font-family: var(--font-mono); font-size: 0.78rem; }
    .hours-row.closed .time { color: var(--ink-xlight); }

    /* WhatsApp */
    .wa-card {
      background: #075E54;
      border-radius: var(--radius-md);
      padding: 24px 22px;
      color: white;
    }

    .wa-card-top {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 12px;
    }

    .wa-icon {
      width: 42px; height: 42px;
      background: #25D366;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.2rem;
      flex-shrink: 0;
    }

    .wa-card h4 { font-family: var(--font-display); font-size: 1rem; font-weight: 400; }
    .wa-card > p { font-size: 0.84rem; opacity: 0.75; line-height: 1.65; margin-bottom: 16px; }

    .wa-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: #25D366;
      color: white;
      font-weight: 700;
      font-size: 0.84rem;
      padding: 10px 18px;
      border-radius: var(--radius-sm);
      transition: background 0.15s;
    }
    .wa-btn:hover { background: #1eb855; }

    /* ---- EMERGENCY SECTION ---- */
    .emergency-section {
      background: #1a0505;
      padding: 72px 5%;
      text-align: center;
    }

    .emergency-section .section-eyebrow { color: #fca5a5; }

    .emergency-section h2 {
      font-family: var(--font-display);
      font-size: 1.9rem;
      color: #fecaca;
      margin-bottom: 10px;
    }

    .emergency-section p {
      color: rgba(255,200,200,0.65);
      max-width: 480px;
      margin: 0 auto 36px;
      font-size: 0.92rem;
    }

    .emer-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 14px;
      max-width: 860px;
      margin: 0 auto;
    }

    .emer-card {
      background: rgba(255,255,255,0.06);
      border: 1px solid rgba(255,100,100,0.2);
      border-radius: var(--radius-md);
      padding: 22px 18px;
      text-align: center;
      transition: background 0.2s;
    }

    .emer-card:hover { background: rgba(255,255,255,0.1); }

    .emer-card-icon {
      width: 42px; height: 42px;
      background: rgba(220,38,38,0.25);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      color: #fca5a5;
      font-size: 1rem;
      margin: 0 auto 12px;
    }

    .emer-card h4 { font-size: 0.8rem; font-weight: 700; color: #fca5a5; margin-bottom: 6px; font-family: var(--font-mono); text-transform: uppercase; letter-spacing: 0.05em; }
    .emer-card a  { font-size: 1rem; font-weight: 800; color: white; display: block; margin-bottom: 4px; }
    .emer-card span { font-size: 0.72rem; color: rgba(255,255,255,0.4); }

    /* ---- INFO ROW ---- */
    .info-row {
      background: var(--ivory-dark);
      padding: 56px 5%;
    }

    .info-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      max-width: 900px;
      margin: 36px auto 0;
    }

    .info-card {
      background: white;
      border: 1px solid var(--border);
      border-radius: var(--radius-md);
      padding: 24px 22px;
      display: flex;
      gap: 16px;
      align-items: flex-start;
    }

    .info-card-icon {
      width: 40px; height: 40px;
      background: var(--sage-xlight);
      border-radius: var(--radius-sm);
      display: flex; align-items: center; justify-content: center;
      color: var(--forest);
      font-size: 1rem;
      flex-shrink: 0;
    }

    .info-card h4 {
      font-size: 0.86rem;
      font-weight: 700;
      color: var(--ink);
      margin-bottom: 4px;
    }

    .info-card p {
      font-size: 0.82rem;
      color: var(--ink-light);
      line-height: 1.6;
    }

    .info-card a {
      font-size: 0.82rem;
      font-weight: 700;
      color: var(--forest);
      text-decoration: underline;
      text-underline-offset: 2px;
    }

    @media (max-width: 900px) {
      .contact-layout { grid-template-columns: 1fr; gap: 24px; }
      .form-grid-2 { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<nav>
  <a href="index.html" class="nav-logo">
    <span class="logo-leaf">🌿</span> MindSpace
  </a>
  <ul class="nav-links">
    <li><a href="index.html">Home</a></li>
    <li><a href="about.html">About Us</a></li>
    <li><a href="mood.html">Mood Tracker</a></li>
    <li><a href="journal.html">Journal</a></li>
    <li><a href="contact.php" class="active btn-nav">Contact Us</a></li>
  </ul>
  <button class="hamburger" aria-label="Toggle menu"><span></span><span></span><span></span></button>
</nav>

<header class="page-hero">
  <p class="breadcrumb"><a href="index.html">Home</a> › Contact Us</p>
  <h1>We're Here for You</h1>
  <p class="hero-sub">Reach out for support, connect with a counselor, or just let us know how we can help.</p>
</header>

<!-- MAIN CONTENT -->
<div class="contact-layout">

  <!-- FORM -->
  <div>
    <div class="form-card">
      <div class="form-card-header">
        <h2>Send Us a Message</h2>
        <p>We read every message and respond within 24 hours.</p>
      </div>
      <div class="form-body">

        <?php if($success): ?>
          <div class="alert alert-success">
            <i class="fa fa-check-circle"></i>
            <?= htmlspecialchars($success) ?>
          </div>
        <?php endif; ?>

        <?php if($error): ?>
          <div class="alert alert-error">
            <i class="fa fa-triangle-exclamation"></i>
            <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="contact.php">
          <div class="form-grid-2">
            <div class="form-group">
              <label>Full Name <span style="color:var(--coral);">*</span></label>
              <input type="text" name="fullname" placeholder="e.g. Aisha Nakato" required/>
            </div>
            <div class="form-group">
              <label>Email Address <span style="color:var(--coral);">*</span></label>
              <input type="email" name="email" placeholder="your@email.com" required/>
            </div>
          </div>

          <div class="form-grid-2">
            <div class="form-group">
              <label>Phone Number <span style="color:var(--ink-xlight); font-weight:400;">(optional)</span></label>
              <input type="tel" name="phone" placeholder="+256 7XX XXX XXX"/>
            </div>
            <div class="form-group">
              <label>Subject</label>
              <select name="subject" class="subject-select">
                <option value="">— Select a topic —</option>
                <option>General Enquiry</option>
                <option>Counselling Request</option>
                <option>Crisis Support</option>
                <option>Platform Feedback</option>
                <option>Partnership / Collaboration</option>
                <option>Other</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label>Your Message</label>
            <textarea name="message" rows="5" placeholder="Tell us how we can support you today…"></textarea>
          </div>

          <div class="form-submit-row">
            <p class="form-notice"><i class="fa fa-lock fa-sm"></i> Confidential — never shared</p>
            <button type="submit" class="btn btn-primary">
              Send Message <i class="fa fa-paper-plane fa-sm"></i>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- SIDEBAR -->
  <div class="contact-sidebar">

    <!-- Counselors -->
    <div class="counselors-card">
      <div class="counselors-header">
        <h3>Our Counselors</h3>
        <p>Available Mon–Fri, 8 AM – 6 PM</p>
      </div>
      <div class="counselor-item">
        <div class="c-avatar" style="background:#2F7DB8;">DK</div>
        <div class="c-info">
          <h5>Dr. Diana Kamya</h5>
          <p>Clinical Psychologist · MUST</p>
        </div>
        <a href="tel:+256772100001" class="call-btn"><i class="fa fa-phone fa-xs"></i> Call</a>
      </div>
      <div class="counselor-item">
        <div class="c-avatar" style="background:var(--forest-lit);">MO</div>
        <div class="c-info">
          <h5>Moses Ochieng</h5>
          <p>Mental Health Counselor</p>
        </div>
        <a href="tel:+256752200002" class="call-btn"><i class="fa fa-phone fa-xs"></i> Call</a>
      </div>
      <div class="counselor-item">
        <div class="c-avatar" style="background:var(--gold);">AN</div>
        <div class="c-info">
          <h5>Aisha Nakato</h5>
          <p>Wellness Coach</p>
        </div>
        <a href="tel:+256700300003" class="call-btn"><i class="fa fa-phone fa-xs"></i> Call</a>
      </div>
      <div class="counselor-item">
        <div class="c-avatar" style="background:var(--coral);">RB</div>
        <div class="c-info">
          <h5>Robert Byamugisha</h5>
          <p>Crisis Support Specialist</p>
        </div>
        <a href="tel:+256712400004" class="call-btn"><i class="fa fa-phone fa-xs"></i> Call</a>
      </div>
    </div>

    <!-- Hours -->
    <div class="hours-card">
      <div class="hours-header">
        <i class="fa fa-clock" style="color:var(--forest-lit);"></i>
        <h3>Office Hours</h3>
        <span class="open-badge open" id="openBadge">● Open</span>
      </div>
      <div class="hours-body" id="hoursBody">
        <div class="hours-row"><span class="day">Monday</span><span class="time">8:00 AM – 6:00 PM</span></div>
        <div class="hours-row"><span class="day">Tuesday</span><span class="time">8:00 AM – 6:00 PM</span></div>
        <div class="hours-row"><span class="day">Wednesday</span><span class="time">8:00 AM – 6:00 PM</span></div>
        <div class="hours-row"><span class="day">Thursday</span><span class="time">8:00 AM – 6:00 PM</span></div>
        <div class="hours-row"><span class="day">Friday</span><span class="time">8:00 AM – 4:00 PM</span></div>
        <div class="hours-row closed"><span class="day">Saturday</span><span class="time">Closed</span></div>
        <div class="hours-row closed"><span class="day">Sunday</span><span class="time">Closed</span></div>
      </div>
    </div>

    <!-- WhatsApp -->
    <div class="wa-card">
      <div class="wa-card-top">
        <div class="wa-icon"><i class="fa-brands fa-whatsapp"></i></div>
        <h4>Join the Support Group</h4>
      </div>
      <p>A safe, moderated WhatsApp community for MUST students to share and support each other through university life.</p>
      <a href="https://wa.me/256700000000" target="_blank" rel="noopener" class="wa-btn">
        <i class="fa-brands fa-whatsapp"></i> Join on WhatsApp
      </a>
    </div>

  </div>
</div>

<!-- INFO ROW -->
<div class="info-row">
  <div style="text-align:center;">
    <span class="section-eyebrow">Find Us</span>
    <h2 class="section-title">Get in <em>touch</em></h2>
  </div>
  <div class="info-cards">
    <div class="info-card">
      <div class="info-card-icon"><i class="fa fa-location-dot"></i></div>
      <div>
        <h4>Campus Location</h4>
        <p>Faculty of Computing &amp; Informatics<br>Mbarara University of Science &amp; Technology<br>Mbarara, Uganda</p>
      </div>
    </div>
    <div class="info-card">
      <div class="info-card-icon"><i class="fa fa-envelope"></i></div>
      <div>
        <h4>Email Us</h4>
        <p><a href="mailto:mindspace@must.ac.ug">mindspace@must.ac.ug</a></p>
        <p style="margin-top:6px; color:var(--ink-xlight);">Responses within 24 hours on weekdays.</p>
      </div>
    </div>
    <div class="info-card">
      <div class="info-card-icon"><i class="fa fa-phone"></i></div>
      <div>
        <h4>Call Us</h4>
        <p><a href="tel:+256772100001">+256 772 100 001</a></p>
        <p style="margin-top:6px; color:var(--ink-xlight);">Mon–Fri, 8 AM – 6 PM EAT</p>
      </div>
    </div>
  </div>
</div>

<!-- EMERGENCY -->
<section class="emergency-section">
  <span class="section-eyebrow">🚨 Crisis Resources</span>
  <h2>Immediate Support</h2>
  <p>If you or someone you know is in crisis, please don't wait. Reach out to one of these services right now.</p>

  <div class="emer-grid">
    <div class="emer-card">
      <div class="emer-card-icon"><i class="fa fa-phone-volume"></i></div>
      <h4>National Helpline</h4>
      <a href="tel:+256800100066">0800 100 066</a>
      <span>Free · 24/7 · Uganda</span>
    </div>
    <div class="emer-card">
      <div class="emer-card-icon"><i class="fa fa-brain"></i></div>
      <h4>Mental Health Uganda</h4>
      <a href="tel:+256414340869">+256 414 340 869</a>
      <span>Professional support</span>
    </div>
    <div class="emer-card">
      <div class="emer-card-icon"><i class="fa fa-user-doctor"></i></div>
      <h4>Campus Counselor</h4>
      <a href="tel:+256772100001">+256 772 100 001</a>
      <span>Mon–Fri, 8 AM – 6 PM</span>
    </div>
    <div class="emer-card">
      <div class="emer-card-icon"><i class="fa fa-hospital"></i></div>
      <h4>Mbarara Hospital</h4>
      <a href="tel:+256485421899">+256 485 421 899</a>
      <span>24/7 emergency services</span>
    </div>
  </div>
</section>

<footer>
  <div class="footer-grid">
    <div class="footer-brand">
      <span class="logo-text">🌿 MindSpace</span>
      <p>A digital mental wellness platform built by students of Mbarara University of Science and Technology.</p>
    </div>
    <div class="footer-col">
      <h4>Pages</h4>
      <ul>
        <li><a href="index.html">Home</a></li>
        <li><a href="about.html">About Us</a></li>
        <li><a href="mood.html">Mood Tracker</a></li>
        <li><a href="journal.html">Journal</a></li>
        <li><a href="contact.php">Contact Us</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Emergency Help</h4>
      <ul>
        <li><a href="tel:+256800100066">Uganda Helpline: 0800 100 066</a></li>
        <li><a href="tel:+256414340869">Mental Health Uganda</a></li>
        <li><a href="tel:+256772100001">Campus Counselor</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© 2025 MindSpace — MUST · Faculty of Computing and Informatics</p>
    <p>Built with ❤️ by Group Two, BIT Program</p>
  </div>
</footer>

<script src="main.js"></script>
<script>
  /* Highlight today's row */
  const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
  const today = days[new Date().getDay()];
  const rows = document.querySelectorAll('.hours-row');
  rows.forEach(r => {
    if (r.querySelector('.day') && r.querySelector('.day').textContent === today) {
      r.classList.add('today');
    }
  });

  /* Open / closed badge */
  const now = new Date();
  const h = now.getHours();
  const day = now.getDay();
  const isWeekday = day >= 1 && day <= 5;
  const isFriday = day === 5;
  const isOpen = isWeekday && h >= 8 && (isFriday ? h < 16 : h < 18);
  const badge = document.getElementById('openBadge');
  if (!isOpen) {
    badge.textContent = '● Closed';
    badge.className = 'open-badge closed';
  }
</script>
</body>
</html>
