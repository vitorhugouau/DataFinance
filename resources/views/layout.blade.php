<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Painel Financeiro</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        :root {
            --bg-primary: #0a0e27;
            --bg-secondary: #141b2d;
            --bg-card: #1a2332;
            --accent-primary: #00d4ff;
            --accent-secondary: #7c3aed;
            --text-primary: #e2e8f0;
            --text-secondary: #94a3b8;
            --border-color: #1e293b;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--bg-primary) 0%, #1a1f3a 100%);
            color: var(--text-primary);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 50%, rgba(124, 58, 237, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(0, 212, 255, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        .navbar {
            background: rgba(26, 35, 50, 0.8) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
        }

        .navbar-brand {
            color: var(--accent-primary) !important;
            font-weight: 700;
            font-size: 1.5rem;
            text-shadow: 0 0 20px rgba(0, 212, 255, 0.5);
            letter-spacing: 1px;
        }

        .navbar-nav .nav-link {
            color: var(--text-secondary) !important;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .navbar-nav .nav-link:hover {
            color: var(--accent-primary) !important;
        }

        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary));
            transition: width 0.3s ease;
        }

        .navbar-nav .nav-link:hover::after {
            width: 100%;
        }

        .sidebar {
            width: 280px;
            position: fixed;
            height: 100vh;
            background: rgba(26, 35, 50, 0.95);
            backdrop-filter: blur(10px);
            border-right: 1px solid var(--border-color);
            padding: 2rem 0;
            z-index: 100;
            overflow-y: auto;
        }

        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 3px;
            height: 100%;
            background: linear-gradient(180deg, var(--accent-primary), var(--accent-secondary));
        }

        .sidebar h5 {
            color: var(--accent-primary);
            padding: 0 2rem;
            margin-bottom: 2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.85rem;
        }

        .sidebar .nav-link {
            color: var(--text-secondary);
            padding: 1rem 2rem;
            margin: 0.25rem 1rem;
            border-radius: 10px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar .nav-link i {
            font-size: 1.2rem;
            width: 24px;
        }

        .sidebar .nav-link:hover {
            background: rgba(0, 212, 255, 0.1);
            color: var(--accent-primary);
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            background: linear-gradient(90deg, rgba(0, 212, 255, 0.2), rgba(124, 58, 237, 0.2));
            color: var(--accent-primary);
            border-left: 3px solid var(--accent-primary);
            box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        }

        .content {
            margin-left: 280px;
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 212, 255, 0.2);
            border-color: var(--accent-primary);
        }

        .card:hover::before {
            opacity: 1;
        }

        .card h2, .card h3, .card h4, .card h5 {
            color: var(--text-primary);
            margin-bottom: 1rem;
        }

        .btn {
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            color: white;
            box-shadow: 0 4px 15px rgba(0, 212, 255, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 212, 255, 0.6);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger), #dc2626);
            color: white;
        }

        .btn-outline-secondary {
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            background: transparent;
        }

        .btn-outline-secondary:hover {
            background: var(--bg-secondary);
            border-color: var(--accent-primary);
            color: var(--accent-primary);
        }

        .form-control, .form-select {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            border-radius: 10px;
            padding: 0.75rem 1rem;
        }

        .form-control:focus, .form-select:focus {
            background: var(--bg-secondary);
            border-color: var(--accent-primary);
            color: var(--text-primary);
            box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.1);
        }

        .form-label {
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .table {
            color: var(--text-primary);
        }

        .table thead th {
            background: var(--bg-secondary);
            border-bottom: 2px solid var(--border-color);
            color: var(--text-primary);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
        }

        .table tbody tr {
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: rgba(0, 212, 255, 0.05);
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        .text-success {
            color: var(--success) !important;
        }

        .text-danger {
            color: var(--danger) !important;
        }

        .text-muted {
            color: var(--text-secondary) !important;
        }

        .modal-content {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
        }

        .modal-header {
            border-bottom: 1px solid var(--border-color);
        }

        .modal-title {
            color: var(--text-primary);
        }

        .btn-close {
            filter: invert(1);
        }

        .modal-footer {
            border-top: 1px solid var(--border-color);
        }

        /* Remove modal backdrop completely */
        .modal-backdrop {
            display: none !important;
        }

        .modal-backdrop.fade {
            display: none !important;
        }

        .modal-backdrop.show {
            display: none !important;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .navbar {
                padding: 0.75rem 1rem;
            }

            .navbar-brand {
                font-size: 1.2rem;
            }

        .sidebar {
            width: 280px;
            max-width: 80%;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            z-index: 1050;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.5);
        }

        .sidebar.show {
            transform: translateX(0);
        }
        
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1049;
        }
        
        .sidebar.show ~ .sidebar-overlay,
        .sidebar-overlay.show {
            display: block;
        }

            .content {
                margin-left: 0;
                padding: 1rem;
            }

            .card {
                padding: 1rem;
                margin-bottom: 1rem;
            }

            .table {
                font-size: 0.85rem;
            }

            .table thead th {
                font-size: 0.75rem;
                padding: 0.5rem;
            }

            .table tbody td {
                padding: 0.5rem;
            }

            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }

            .modal-dialog {
                margin: 0.5rem;
            }

            .modal-content {
                border-radius: 12px;
            }
        }

        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1rem;
            }

            .content {
                padding: 0.75rem;
            }

            .card {
                padding: 0.75rem;
            }

            h2 {
                font-size: 1.5rem;
            }

            h3 {
                font-size: 1.25rem;
            }

            .table {
                font-size: 0.8rem;
            }

            .table thead th {
                font-size: 0.7rem;
                padding: 0.4rem;
            }

            .table tbody td {
                padding: 0.4rem;
            }
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-primary);
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, var(--accent-primary), var(--accent-secondary));
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--accent-primary);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/dashboard">
            <i class="bi bi-graph-up-arrow"></i> Finanças Pro
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <button class="btn btn-link d-md-none ms-2" type="button" id="sidebarToggle" style="color: var(--accent-primary);">
            <i class="bi bi-list"></i>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="/dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="/accounts"><i class="bi bi-wallet2"></i> Contas</a></li>
                <li class="nav-item"><a class="nav-link" href="/categories"><i class="bi bi-tags"></i> Categorias</a></li>
                <li class="nav-item"><a class="nav-link" href="/transactions"><i class="bi bi-arrow-left-right"></i> Transações</a></li>
                <li class="nav-item"><a class="nav-link" href="/investments"><i class="bi bi-graph-up-arrow"></i> Investimentos</a></li>
                <li class="nav-item"><a class="nav-link" href="/quotes"><i class="bi bi-currency-exchange"></i> Cotações</a></li>
                <li class="nav-item"><a class="nav-link" href="/projection"><i class="bi bi-calculator"></i> Projeção</a></li>
                <li class="nav-item"><a class="nav-link" href="/credit-cards"><i class="bi bi-credit-card"></i> Cartões</a></li>
                <li class="nav-item"><a class="nav-link" href="/fixed-expenses"><i class="bi bi-receipt-cutoff"></i> Gastos Fixos</a></li>
                <li class="nav-item"><a class="nav-link" href="/goals"><i class="bi bi-bullseye"></i> Metas</a></li>
                <li class="nav-item"><a class="nav-link" href="/receivables"><i class="bi bi-cash-coin"></i> A Receber</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="d-flex">
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="sidebar">
        <h5><i class="bi bi-menu-button-wide"></i> Menu</h5>
        <ul class="nav nav-pills flex-column">
            <li class="nav-item">
                <a href="/dashboard" class="nav-link">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/accounts" class="nav-link">
                    <i class="bi bi-wallet2"></i>
                    <span>Contas</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/categories" class="nav-link">
                    <i class="bi bi-tags"></i>
                    <span>Categorias</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/transactions" class="nav-link">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Transações</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/investments" class="nav-link">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Investimentos</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/quotes" class="nav-link">
                    <i class="bi bi-currency-exchange"></i>
                    <span>Cotações</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/projection" class="nav-link">
                    <i class="bi bi-calculator"></i>
                    <span>Projeção</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/credit-cards" class="nav-link">
                    <i class="bi bi-credit-card"></i>
                    <span>Cartões</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/fixed-expenses" class="nav-link">
                    <i class="bi bi-receipt-cutoff"></i>
                    <span>Gastos Fixos</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/goals" class="nav-link">
                    <i class="bi bi-bullseye"></i>
                    <span>Metas</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/receivables" class="nav-link">
                    <i class="bi bi-cash-coin"></i>
                    <span>A Receber</span>
                </a>
            </li>
        </ul>
    </div>

    <main class="content w-100">
        <div class="container-fluid">
            @yield('content')
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Highlight active menu item
    document.querySelectorAll('.sidebar .nav-link, .navbar-nav .nav-link').forEach(link => {
        if (link.getAttribute('href') === window.location.pathname) {
            link.classList.add('active');
        }
    });

    // Mobile sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    function toggleSidebar() {
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('show');
            if (sidebarOverlay) {
                sidebarOverlay.classList.toggle('show');
            }
        }
    }
    
    function closeSidebar() {
        if (window.innerWidth <= 768) {
            sidebar.classList.remove('show');
            if (sidebarOverlay) {
                sidebarOverlay.classList.remove('show');
            }
        }
    }
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleSidebar();
        });

        // Close sidebar when clicking on overlay
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebar);
        }

        // Close sidebar when clicking on a link
        const sidebarLinks = sidebar.querySelectorAll('.nav-link');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', closeSidebar);
        });
    }

    // Remove modal-open class when modals are hidden
    document.addEventListener('hidden.bs.modal', function(e) {
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    });
</script>
@yield('scripts')
</body>
</html>