<?php
session_start();

// Si l'utilisateur est déjà connecté, rediriger vers le tableau de bord
if (isset($_SESSION['user']) && $_SESSION['user']['logged_in']) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediTimmy – Gestion médicale simplifiée</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;700;800&family=DM+Sans:ital,wght@0,400;0,500;1,400&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:       #1a6bff;
            --primary-dark:  #0047d4;
            --primary-light: #e8f0ff;
            --accent:        #00d4aa;
            --danger:        #ff5c5c;
            --warning:       #ffb347;
            --dark:          #0d1117;
            --dark-2:        #161b27;
            --muted:         #8899aa;
            --white:         #ffffff;
            --surface:       #f4f7ff;
            --border:        rgba(26,107,255,0.12);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--surface);
            color: var(--dark);
            overflow-x: hidden;
        }
        /* ── HEADER ─────────────────────────────── */
        .header {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 1px 0 var(--border);
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
        }
        .navbar-brand {
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 1.35rem;
            color: var(--primary) !important;
            letter-spacing: -0.5px;
        }
        .nav-link {
            color: var(--dark) !important;
            font-weight: 500;
            margin: 0 6px;
            font-size: .95rem;
            transition: color .2s;
        }
        .nav-link:hover { color: var(--primary) !important; }
        .btn-connexion {
            background: var(--primary);
            border: none;
            color: var(--white);
            font-weight: 600;
            border-radius: 8px;
            padding: 8px 22px;
            font-size: .92rem;
            transition: background .2s, transform .15s;
        }
        .btn-connexion:hover { background: var(--primary-dark); transform: translateY(-1px); }

        /* ── HERO ────────────────────────────────── */
        .hero {
            padding: 140px 0 90px;
            background: linear-gradient(145deg, #0d1117 0%, #001f6e 55%, #1a6bff 100%);
            color: var(--white);
            text-align: center;
            border-radius: 0 0 30px 30px;
            margin-top: 68px;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(ellipse 70% 60% at 50% 120%, rgba(0,212,170,.18), transparent);
            pointer-events: none;
        }
        /* floating blobs */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: .25;
            animation: float 8s ease-in-out infinite;
        }
        .blob-1 { width:320px;height:320px;background:var(--accent);  top:-80px;  left:-80px; animation-delay:0s; }
        .blob-2 { width:200px;height:200px;background:var(--primary); bottom:-60px;right:5%;  animation-delay:3s; }
        @keyframes float {
            0%,100%{ transform:translateY(0); }
            50%    { transform:translateY(-22px); }
        }

        .hero-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 20px;
            padding: 5px 14px;
            font-size: .82rem;
            letter-spacing: .5px;
            margin-bottom: 22px;
            color: rgba(255,255,255,.85);
        }
        .hero-badge .dot { width:7px;height:7px;background:var(--accent);border-radius:50%;animation:pulse 1.8s infinite; }
        @keyframes pulse {
            0%,100%{ opacity:1; transform:scale(1); }
            50%     { opacity:.5; transform:scale(1.4); }
        }

        .hero h1 {
            font-family: 'Sora', sans-serif;
            font-size: clamp(2.2rem, 5vw, 3.6rem);
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 18px;
            letter-spacing: -1px;
        }
        .hero h1 span { color: var(--accent); }
        .hero p {
            font-size: 1.1rem;
            max-width: 620px;
            margin: 0 auto 34px;
            color: rgba(255,255,255,.75);
            line-height: 1.7;
        }
        .hero-buttons { display:flex;justify-content:center;gap:14px;flex-wrap:wrap; }
        .btn-hero-primary {
            background: var(--white); color: var(--primary);
            padding: 13px 32px; border-radius: 10px;
            font-weight: 700; font-size: 1rem;
            text-decoration: none; transition: all .25s;
            border: none; box-shadow: 0 4px 20px rgba(0,0,0,.25);
        }
        .btn-hero-primary:hover { transform:translateY(-3px); box-shadow:0 8px 30px rgba(0,0,0,.3); color:var(--primary-dark); }
        .btn-hero-outline {
            background: transparent;
            border: 2px solid rgba(255,255,255,.4);
            color: var(--white);
            padding: 13px 32px; border-radius: 10px;
            font-weight: 600; font-size: 1rem;
            text-decoration: none; transition: all .25s;
        }
        .btn-hero-outline:hover { border-color:var(--white); background:rgba(255,255,255,.1); color:var(--white); }

        /* ── STATS ───────────────────────────────── */
        .stats-section {
            padding: 70px 0;
            background: var(--white);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2px;
            background: var(--border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 40px rgba(26,107,255,.1);
        }
        .stat-item {
            background: var(--white);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            transition: background .25s;
        }
        .stat-item:hover { background: var(--primary-light); }
        .stat-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
            font-size: 1.4rem;
        }
        .stat-icon.blue   { background: #e8f0ff; color: var(--primary); }
        .stat-icon.green  { background: #e0faf4; color: var(--accent); }
        .stat-icon.orange { background: #fff3e0; color: var(--warning); }
        .stat-icon.red    { background: #ffe8e8; color: var(--danger); }

        .stat-number {
            font-family: 'Sora', sans-serif;
            font-size: 2.6rem;
            font-weight: 800;
            color: var(--dark);
            line-height: 1;
            letter-spacing: -1px;
        }
        .stat-suffix {
            font-size: 1.5rem;
            color: var(--primary);
            font-weight: 700;
        }
        .stat-label {
            margin-top: 8px;
            font-size: .88rem;
            color: var(--muted);
            font-weight: 500;
            letter-spacing: .3px;
        }
        .stat-trend {
            display: inline-flex; align-items: center; gap: 4px;
            margin-top: 10px;
            font-size: .78rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
        }
        .trend-up   { background:#e0faf4; color:#00a88a; }
        .trend-down { background:#ffe8e8; color:#d94040; }

        /* progress bar inside stat */
        .stat-bar-track {
            height: 4px; background: #eef2ff;
            border-radius: 2px; margin-top: 14px; overflow: hidden;
        }
        .stat-bar-fill {
            height: 100%; border-radius: 2px;
            background: var(--primary);
            width: 0;
            transition: width 1.5s cubic-bezier(.4,0,.2,1);
        }
        .stat-bar-fill.green  { background: var(--accent); }
        .stat-bar-fill.orange { background: var(--warning); }
        .stat-bar-fill.red    { background: var(--danger); }

        /* ── FEATURES ────────────────────────────── */
        .features { padding: 80px 0; }
        .section-title { text-align:center; margin-bottom:52px; }
        .section-title .eyebrow {
            display: inline-block;
            font-size: .78rem; font-weight: 700;
            letter-spacing: 2px; text-transform: uppercase;
            color: var(--primary); margin-bottom: 10px;
        }
        .section-title h2 {
            font-family: 'Sora', sans-serif;
            font-size: clamp(1.8rem, 3vw, 2.6rem);
            font-weight: 800; color: var(--dark);
            margin-bottom: 12px; letter-spacing: -0.5px;
        }
        .section-title p { color: var(--muted); font-size: 1.05rem; }

        .feature-card {
            background: var(--white);
            border-radius: 16px;
            padding: 32px;
            border: 1px solid var(--border);
            transition: all .3s; text-align: center; height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(26,107,255,.12);
            border-color: rgba(26,107,255,.25);
        }
        .feature-icon {
            width: 64px; height: 64px;
            background: var(--primary-light);
            border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.7rem; color: var(--primary);
            margin: 0 auto 20px;
        }
        .feature-card h3 {
            font-family: 'Sora', sans-serif;
            font-size: 1.2rem; font-weight: 700;
            margin-bottom: 10px; letter-spacing: -0.3px;
        }
        .feature-card p { color: var(--muted); font-size: .95rem; line-height: 1.65; }

        /* ── TESTIMONIALS ────────────────────────── */
        .testimonials { background: var(--dark); padding: 80px 0; }
        .testimonials .section-title h2 { color: var(--white); }
        .testimonials .section-title p  { color: rgba(255,255,255,.5); }
        .testimonials .eyebrow          { color: var(--accent); }

        .testimonial-card {
            background: var(--dark-2);
            border: 1px solid rgba(255,255,255,.07);
            padding: 30px; border-radius: 16px;
            transition: border-color .25s;
        }
        .testimonial-card:hover { border-color: rgba(26,107,255,.4); }
        .testimonial-text {
            font-style: italic;
            color: rgba(255,255,255,.8);
            font-size: 1rem; line-height: 1.7;
            margin-bottom: 22px;
        }
        .testimonial-author { display:flex; align-items:center; }
        .author-avatar {
            width: 48px; height: 48px;
            border-radius: 50%; margin-right: 14px; object-fit: cover;
            border: 2px solid rgba(255,255,255,.15);
        }
        .author-name { color: var(--white); font-weight: 600; font-size: .95rem; }
        .author-role { color: var(--muted); font-size: .82rem; margin-top: 1px; }
        .stars { color: #ffb347; font-size: .85rem; margin-bottom: 14px; }

        /* ── CTA ─────────────────────────────────── */
        .cta {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white); padding: 90px 0; text-align: center;
        }
        .cta h2 {
            font-family: 'Sora', sans-serif;
            font-size: clamp(1.8rem, 3vw, 2.6rem);
            font-weight: 800; margin-bottom: 16px; letter-spacing: -0.5px;
        }
        .cta p { color: rgba(255,255,255,.75); font-size: 1.05rem; margin-bottom: 32px; }

        /* ── FOOTER ──────────────────────────────── */
        .footer { background: var(--dark); color: rgba(255,255,255,.6); padding: 40px 0 20px; }
        .footer-links { display:flex;justify-content:center;gap:28px;margin-bottom:28px;flex-wrap:wrap; }
        .footer-links a { color:rgba(255,255,255,.5); text-decoration:none; font-size:.9rem; transition:color .2s; }
        .footer-links a:hover { color:var(--white); }
        .copyright {
            text-align:center; padding-top:18px;
            border-top:1px solid rgba(255,255,255,.08);
            font-size: .85rem;
        }

        /* ── RESPONSIVE ──────────────────────────── */
        @media (max-width: 768px) {
            .hero { padding: 110px 0 70px; }
            .hero-buttons { flex-direction:column; align-items:center; }
            .btn-hero-primary, .btn-hero-outline { width:100%; max-width:300px; text-align:center; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .footer-links { flex-direction:column; gap:12px; }
        }
        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- ── HEADER ─────────────────────────────────── -->
    <header class="header">
        <nav class="navbar navbar-expand-lg navbar-light bg-transparent">
            <div class="container">
                <a class="navbar-brand" href="#">
                    <i class="fas fa-hospital-alt me-2"></i>MediTimmy
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="#stats">Statistiques</a></li>
                        <li class="nav-item"><a class="nav-link" href="#features">Fonctionnalités</a></li>
                        <li class="nav-item"><a class="nav-link" href="#testimonials">Témoignages</a></li>
                        <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    </ul>
                    <a href="login.php" class="btn btn-connexion ms-3">Connexion</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- ── HERO ───────────────────────────────────── -->
    <section class="hero">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="container position-relative">
            <div class="hero-badge mb-3">
                <span class="dot"></span> Nouvelle version disponible — v2.4
            </div>
            <h1>La gestion médicale<br><span>réinventée</span></h1>
            <p>MediTimmy est la solution tout-en-un pour gérer vos patients, vos rendez-vous et vos dossiers médicaux en toute simplicité.</p>
            <div class="hero-buttons">
                <a href="login.php" class="btn-hero-primary">Se connecter</a>
                <a href="Ncompte.php" class="btn-hero-outline">S'inscrire gratuitement</a>
            </div>
        </div>
    </section>

    <!-- ── STATS ──────────────────────────────────── -->
    <section class="stats-section" id="stats">
        <div class="container">
            <div class="section-title mb-4">
                <span class="eyebrow">Chiffres clés</span>
                <h2>MediTimmy en quelques chiffres</h2>
                <p>Des résultats concrets pour les professionnels de santé qui nous font confiance</p>
            </div>
            <div class="stats-grid">

                <div class="stat-item">
                    <div class="stat-icon blue"><i class="fas fa-user-injured"></i></div>
                    <div>
                        <span class="stat-number" data-target="48500" data-suffix="+">0</span><span class="stat-suffix">+</span>
                    </div>
                    <div class="stat-label">Patients gérés</div>
                    <span class="stat-trend trend-up"><i class="fas fa-arrow-up"></i> +12% ce mois</span>
                    <div class="stat-bar-track"><div class="stat-bar-fill" data-width="82"></div></div>
                </div>

                <div class="stat-item">
                    <div class="stat-icon green"><i class="fas fa-calendar-check"></i></div>
                    <div>
                        <span class="stat-number" data-target="320" data-suffix="k">0</span><span class="stat-suffix">k</span>
                    </div>
                    <div class="stat-label">Rendez-vous planifiés</div>
                    <span class="stat-trend trend-up"><i class="fas fa-arrow-up"></i> +8% ce mois</span>
                    <div class="stat-bar-track"><div class="stat-bar-fill green" data-width="68"></div></div>
                </div>

                <div class="stat-item">
                    <div class="stat-icon orange"><i class="fas fa-star"></i></div>
                    <div>
                        <span class="stat-number" data-target="98" data-suffix="%">0</span><span class="stat-suffix">%</span>
                    </div>
                    <div class="stat-label">Satisfaction utilisateurs</div>
                    <span class="stat-trend trend-up"><i class="fas fa-arrow-up"></i> +2% vs. 2023</span>
                    <div class="stat-bar-track"><div class="stat-bar-fill orange" data-width="98"></div></div>
                </div>

                <div class="stat-item">
                    <div class="stat-icon red"><i class="fas fa-hospital"></i></div>
                    <div>
                        <span class="stat-number" data-target="1200" data-suffix="+">0</span><span class="stat-suffix">+</span>
                    </div>
                    <div class="stat-label">Cabinets partenaires</div>
                    <span class="stat-trend trend-up"><i class="fas fa-arrow-up"></i> +35 cette semaine</span>
                    <div class="stat-bar-track"><div class="stat-bar-fill red" data-width="55"></div></div>
                </div>

            </div>
        </div>
    </section>

    <!-- ── FEATURES ───────────────────────────────── -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-title">
                <span class="eyebrow">Fonctionnalités</span>
                <h2>Tout ce dont vous avez besoin</h2>
                <p>Découvrez comment MediTimmy peut transformer votre gestion médicale</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-calendar-check"></i></div>
                        <h3>Gestion des rendez-vous</h3>
                        <p>Planifiez, modifiez et suivez tous vos rendez-vous en un clin d'œil avec notre calendrier interactif.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-users"></i></div>
                        <h3>Dossiers patients</h3>
                        <p>Accédez rapidement aux historiques médicaux complets de vos patients, sécurisés et organisés.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                        <h3>Statistiques avancées</h3>
                        <p>Analysez votre activité avec des graphiques et rapports détaillés pour une meilleure prise de décision.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── TESTIMONIALS ────────────────────────────── -->
    <section class="testimonials" id="testimonials">
        <div class="container">
            <div class="section-title">
                <span class="eyebrow">Témoignages</span>
                <h2>Ce que disent nos utilisateurs</h2>
                <p>Des professionnels de santé satisfaits par notre solution</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="stars">★★★★★</div>
                        <div class="testimonial-text">"MediTimmy a révolutionné notre façon de gérer les rendez-vous. Plus de doubles réservations et une visibilité parfaite sur notre planning."</div>
                        <div class="testimonial-author">
                            <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Dr. Martin" class="author-avatar">
                            <div>
                                <div class="author-name">Dr. Sophie Martin</div>
                                <div class="author-role">Médecin généraliste</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="stars">★★★★★</div>
                        <div class="testimonial-text">"L'interface est intuitive et les dossiers patients sont enfin bien organisés. Un gain de temps considérable au quotidien."</div>
                        <div class="testimonial-author">
                            <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Dr. Dupont" class="author-avatar">
                            <div>
                                <div class="author-name">Dr. Thomas Dupont</div>
                                <div class="author-role">Pédiatre</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="stars">★★★★★</div>
                        <div class="testimonial-text">"Les statistiques m'aident à mieux comprendre l'activité de mon cabinet. Je recommande MediTimmy à tous mes confrères."</div>
                        <div class="testimonial-author">
                            <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Dr. Leroy" class="author-avatar">
                            <div>
                                <div class="author-name">Dr. Claire Leroy</div>
                                <div class="author-role">Dermatologue</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── CTA ────────────────────────────────────── -->
    <section class="cta">
        <div class="container">
            <h2>Prêt à simplifier votre gestion médicale ?</h2>
            <p>Rejoignez des centaines de professionnels de santé qui font déjà confiance à MediTimmy.</p>
            <div class="hero-buttons">
                <a href="login.php" class="btn-hero-primary">Se connecter</a>
                <a href="register.php" class="btn-hero-outline">Essayer gratuitement</a>
            </div>
        </div>
    </section>

    <!-- ── FOOTER ─────────────────────────────────── -->
    <footer class="footer" id="contact">
        <div class="container">
            <div class="footer-links">
                <a href="#stats">Statistiques</a>
                <a href="#features">Fonctionnalités</a>
                <a href="#testimonials">Témoignages</a>
                <a href="login.php">Connexion</a>
                <a href="register.php">Inscription</a>
                <a href="contact.php">Contact</a>
            </div>
            <div class="copyright">
                <p>&copy; <?= date('Y') ?> MediTimmy. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        /* ── Compteurs animés au scroll ── */
        function animateCounter(el) {
            const target   = parseInt(el.dataset.target, 10);
            const duration = 1800;
            const steps    = 60;
            const stepTime = duration / steps;
            let current    = 0;

            const timer = setInterval(() => {
                current += target / steps;
                if (current >= target) { current = target; clearInterval(timer); }
                el.textContent = Math.floor(current).toLocaleString('fr-FR');
            }, stepTime);
        }

        /* ── Barres de progression ── */
        function animateBars(section) {
            section.querySelectorAll('.stat-bar-fill').forEach(bar => {
                bar.style.width = bar.dataset.width + '%';
            });
        }

        /* ── IntersectionObserver ── */
        const statsSection = document.getElementById('stats');
        let animated = false;

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !animated) {
                    animated = true;
                    document.querySelectorAll('.stat-number').forEach(animateCounter);
                    animateBars(statsSection);
                }
            });
        }, { threshold: 0.25 });

        observer.observe(statsSection);
    </script>
</body>
</html>
