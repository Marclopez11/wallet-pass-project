<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Event Pass Generator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --dark-gradient: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --text-primary: #2d3748;
            --text-secondary: #718096;
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --shadow-xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--primary-gradient);
            min-height: 100vh;
            color: var(--text-primary);
            overflow-x: hidden;
        }

        /* Animated background */
        .bg-animated {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: var(--primary-gradient);
        }

        .bg-animated::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.05"><circle cx="30" cy="30" r="4"/></g></svg>');
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        /* Glass morphism containers */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: var(--shadow-xl);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 32px 64px -12px rgba(0, 0, 0, 0.35);
        }

        /* Header section */
        .hero-section {
            padding: 60px 0 40px;
            text-align: center;
            color: white;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .hero-subtitle {
            font-size: 1.25rem;
            font-weight: 400;
            opacity: 0.9;
            margin-bottom: 2rem;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 500;
            color: white;
            margin-bottom: 2rem;
        }

        /* Form styling */
        .form-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-xl);
        }

        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: white;
            transform: translateY(-1px);
        }

        .form-control::placeholder {
            color: #a0aec0;
            font-weight: 400;
        }

        /* Button styling */
        .btn-premium {
            background: var(--primary-gradient);
            border: none;
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            color: white;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-premium::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-premium:hover::before {
            left: 100%;
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-outline-premium {
            border: 2px solid #667eea;
            color: #667eea;
            background: transparent;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-premium:hover {
            background: var(--primary-gradient);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        /* Pass list styling */
        .passes-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 2rem;
            box-shadow: var(--shadow-xl);
        }

        .pass-item {
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .pass-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--accent-gradient);
        }

        .pass-item:hover {
            transform: translateX(8px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .pass-title {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .pass-meta {
            color: var(--text-secondary);
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn-download {
            background: var(--accent-gradient);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-download:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(79, 172, 254, 0.4);
        }

        /* Loading states */
        .loading {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .loading::after {
            content: '';
            width: 16px;
            height: 16px;
            border: 2px solid #e2e8f0;
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-secondary);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .form-container,
            .passes-container {
                padding: 1.5rem;
                margin: 1rem;
            }

            .hero-section {
                padding: 40px 0 20px;
            }
        }

        /* Floating elements */
        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float-shapes 15s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 5s;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 10s;
        }

        @keyframes float-shapes {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.7; }
            50% { transform: translateY(-30px) rotate(180deg); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="bg-animated"></div>

    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="hero-badge">
                <i class="bi bi-stars"></i>
                <span>Premium Event Pass Generator</span>
            </div>
            <h1 class="hero-title">Create Stunning<br>Apple Wallet Passes</h1>
            <p class="hero-subtitle">Generate professional event passes with modern design and premium features</p>
        </div>
    </div>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <!-- Event Form -->
                <div class="form-container">
                    <div class="d-flex align-items-center mb-4">
                        <div class="me-3">
                            <div style="width: 48px; height: 48px; background: var(--accent-gradient); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-ticket-perforated text-white fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold">Event Details</h5>
                            <p class="text-muted mb-0">Fill in your event information to generate a premium pass</p>
                        </div>
                    </div>

                    <form id="event-form" action="{{ route('wallet-pass.create') }}" method="POST">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Event Name</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required
                                       value="Tech Innovation Summit 2024"
                                       placeholder="Enter event name">
                            </div>
                            <div class="col-md-6">
                                <label for="lugar" class="form-label">Venue</label>
                                <input type="text" class="form-control" id="lugar" name="lugar" required
                                       value="Barcelona Convention Center"
                                       placeholder="Event location">
                            </div>
                            <div class="col-md-6">
                                <label for="fecha" class="form-label">Date</label>
                                <input type="date" class="form-control" id="fecha" name="fecha" required>
                            </div>
                            <div class="col-md-6">
                                <label for="hora" class="form-label">Time</label>
                                <input type="time" class="form-control" id="hora" name="hora" required value="09:00">
                            </div>
                            <div class="col-12">
                                <label for="descripcion" class="form-label">Description</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required
                                          placeholder="Event description">Join industry leaders for an exclusive summit featuring cutting-edge technology presentations, interactive workshops, and premium networking opportunities with innovators shaping the future.</textarea>
                            </div>
                        </div>

                        <div class="row g-3 mt-4">
                            <div class="col-md-4">
                                <button type="button" class="btn btn-outline-premium w-100" onclick="limpiarFormulario()">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Clear Form
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-outline-premium w-100" onclick="rellenarEjemplo()">
                                    <i class="bi bi-lightbulb me-2"></i>Random Example
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-premium w-100">
                                    <i class="bi bi-download me-2"></i>Generate Pass
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Generated Passes -->
                <div class="passes-container">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div style="width: 48px; height: 48px; background: var(--secondary-gradient); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-collection text-white fs-4"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="mb-1 fw-bold">Generated Passes</h5>
                                <p class="text-muted mb-0">Your recently created event passes</p>
                            </div>
                        </div>
                        <button class="btn btn-outline-premium btn-sm" onclick="loadPasses()">
                            <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                        </button>
                    </div>

                    <div id="passes-list">
                        <div class="text-center py-4">
                            <div class="loading">Loading passes...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set default date
        document.addEventListener('DOMContentLoaded', function() {
            const fechaInput = document.getElementById('fecha');
            const today = new Date();
            const futureDate = new Date(today.getTime() + (15 * 24 * 60 * 60 * 1000));

            fechaInput.min = today.toISOString().split('T')[0];
            fechaInput.value = futureDate.toISOString().split('T')[0];

            loadPasses();
        });

        function limpiarFormulario() {
            document.getElementById('nombre').value = '';
            document.getElementById('lugar').value = '';
            document.getElementById('descripcion').value = '';
            document.getElementById('hora').value = '';

            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            document.getElementById('fecha').value = tomorrow.toISOString().split('T')[0];
        }

        function rellenarEjemplo() {
            const ejemplos = [
                {
                    nombre: "AI & Machine Learning Summit",
                    lugar: "Silicon Valley Convention Center",
                    descripcion: "Premier artificial intelligence conference featuring breakthrough research, industry case studies, and hands-on workshops with leading AI experts and innovators.",
                    hora: "08:30"
                },
                {
                    nombre: "Digital Art & NFT Expo",
                    lugar: "Modern Art Museum Barcelona",
                    descripcion: "Exclusive exhibition showcasing digital art revolution, NFT marketplace insights, and interactive experiences with renowned digital artists and collectors.",
                    hora: "10:00"
                },
                {
                    nombre: "Blockchain Developer Conference",
                    lugar: "Tech Hub Madrid",
                    descripcion: "Technical deep-dive into blockchain development, smart contracts, DeFi protocols, and Web3 innovations with industry-leading developers and architects.",
                    hora: "09:15"
                },
                {
                    nombre: "Sustainable Tech Innovation",
                    lugar: "Green Technology Center Valencia",
                    descripcion: "Groundbreaking conference on sustainable technology solutions, clean energy innovations, and environmental impact of emerging technologies.",
                    hora: "11:30"
                },
                {
                    nombre: "Cybersecurity Excellence Summit",
                    lugar: "Security Institute Bilbao",
                    descripcion: "Elite cybersecurity summit featuring threat intelligence, zero-trust architecture, and advanced security frameworks with top security professionals.",
                    hora: "13:00"
                }
            ];

            const ejemplo = ejemplos[Math.floor(Math.random() * ejemplos.length)];

            document.getElementById('nombre').value = ejemplo.nombre;
            document.getElementById('lugar').value = ejemplo.lugar;
            document.getElementById('descripcion').value = ejemplo.descripcion;
            document.getElementById('hora').value = ejemplo.hora;

            const today = new Date();
            const randomDays = Math.floor(Math.random() * 60) + 7;
            const randomDate = new Date(today.getTime() + (randomDays * 24 * 60 * 60 * 1000));
            document.getElementById('fecha').value = randomDate.toISOString().split('T')[0];
        }

        async function loadPasses() {
            const passesList = document.getElementById('passes-list');

            try {
                const response = await fetch('{{ route("wallet-pass.index") }}');
                const data = await response.json();

                if (data.passes.length === 0) {
                    passesList.innerHTML = `
                        <div class="empty-state">
                            <i class="bi bi-ticket-perforated"></i>
                            <h6>No passes generated yet</h6>
                            <p>Create your first premium event pass using the form above</p>
                        </div>
                    `;
                } else {
                    passesList.innerHTML = data.passes.map(pass => `
                        <div class="pass-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="pass-title">${pass.description}</div>
                                    <div class="pass-meta">
                                        <span><i class="bi bi-calendar-event me-1"></i>${pass.created_at}</span>
                                        <span><i class="bi bi-shield-check me-1"></i>Premium Pass</span>
                                    </div>
                                </div>
                                <a href="${pass.download_url}" class="btn btn-download">
                                    <i class="bi bi-download me-1"></i>Download
                                </a>
                            </div>
                        </div>
                    `).join('');
                }
            } catch (error) {
                console.error('Error:', error);
                passesList.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-exclamation-triangle text-warning"></i>
                        <h6>Error loading passes</h6>
                        <p>Please try refreshing the page</p>
                    </div>
                `;
            }
        }

        // Form submission enhancement
        document.getElementById('event-form').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<div class="loading"></div>Generating...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>
