<?php require_once __DIR__ . '/../../includes/config.php'; ?>
<!DOCTYPE html>
<html lang="en" class="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' — Vision Exim Admin' : 'Vision Exim Admin Panel'; ?></title>
    <meta name="description" content="Vision Exim - Spice Export Management Admin Panel">
    <meta name="robots" content="noindex, nofollow">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= htmlspecialchars(ve_url('images/favicons/favicon-96x96.png')) ?>" sizes="96x96" />
    <link rel="shortcut icon" href="<?= htmlspecialchars(ve_url('images/favicons/favicon.ico')) ?>" />

    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        spice: {
                            green: {
                                50: '#E8F5EC',
                                100: '#C8E6CF',
                                200: '#A0D4AC',
                                300: '#6BBF7E',
                                400: '#3D9B53',
                                500: '#2D7A42',
                                600: '#1F4D3A',
                                700: '#1A3F30',
                                800: '#143226',
                                900: '#0E241B',
                            },
                            turmeric: {
                                50: '#FFF8E8',
                                100: '#FFEFC6',
                                200: '#FFE29E',
                                300: '#F5CE6E',
                                400: '#E8B94E',
                                500: '#D9A441',
                                600: '#C48E2F',
                                700: '#A87523',
                                800: '#8B5E1A',
                                900: '#6E4912',
                            },
                            chili: {
                                50: '#FFF0ED',
                                100: '#FFD9D2',
                                200: '#FFBAB0',
                                300: '#E8897B',
                                400: '#D4604F',
                                500: '#B9412E',
                                600: '#9E3425',
                                700: '#82281C',
                                800: '#661D14',
                                900: '#4A130D',
                            },
                            cream: '#F7F3EB',
                            dark: '#1E1E1E',
                        }
                    },
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif'],
                    },
                    boxShadow: {
                        'card': '0 2px 12px rgba(0,0,0,0.06)',
                        'card-hover': '0 8px 30px rgba(0,0,0,0.1)',
                        'sidebar': '4px 0 24px rgba(0,0,0,0.08)',
                        'dropdown': '0 10px 40px rgba(0,0,0,0.12)',
                    },
                    borderRadius: {
                        'xl': '12px',
                        '2xl': '16px',
                        '3xl': '20px',
                    }
                }
            }
        }
    </script>

    <!-- Custom Admin Styles (minimal overrides only) -->
    <style>
        * { font-family: 'Poppins', sans-serif; }
        
        /* Scrollbar Styling */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #c4c4c4; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #999; }
        
        /* Sidebar scrollbar */
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 2px; }

        /* Smooth transitions */
        .sidebar-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        
        /* Page transition */
        .page-content { animation: fadeInUp 0.4s ease-out; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Stat card shimmer */
        .stat-shimmer {
            position: relative;
            overflow: hidden;
        }
        .stat-shimmer::after {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.08), transparent);
            animation: shimmer 3s infinite;
        }
        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        /* Chart container */
        .apexcharts-canvas { font-family: 'Poppins', sans-serif !important; }

        /* Dark mode overrides for specific elements */
        .dark .dark-card { background: #1e293b; border-color: #334155; }
        .dark .dark-input { background: #1e293b; border-color: #475569; color: #e2e8f0; }
    </style>

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="font-poppins bg-spice-cream text-spice-dark antialiased dark:bg-slate-900 dark:text-slate-200">
