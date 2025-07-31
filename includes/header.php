<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Dashboard Transport Lubumbashi'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#1E40AF',
                        'secondary': '#059669',
                        'warning': '#EA580C',
                        'danger': '#DC2626'
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .notification {
            transition: all 0.3s ease;
        }
        
        .card-hover {
            transition: all 0.2s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg border-b border-gray-200 fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <i class="fas fa-bus text-2xl text-primary mr-3"></i>
                        <h1 class="text-xl font-bold text-gray-900">TransportLub</h1>
                    </div>
                    <div class="hidden md:ml-10 md:flex md:space-x-8">
                        <a href="../admin/index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-primary border-primary' : 'text-gray-500 hover:text-gray-700'; ?> border-b-2 px-1 pt-1 pb-1 text-sm font-medium transition-colors">
                            <i class="fas fa-tachometer-alt mr-2"></i>Tableau de Bord
                        </a>
                        <a href="../admin/programmes.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'programmes.php' ? 'text-primary border-primary' : 'text-gray-500 hover:text-gray-700'; ?> border-b-2 border-transparent px-1 pt-1 pb-1 text-sm font-medium transition-colors">
                            <i class="fas fa-route mr-2"></i>Programmes
                        </a>
                        <a href="../admin/reservations.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'reservations.php' ? 'text-primary border-primary' : 'text-gray-500 hover:text-gray-700'; ?> border-b-2 border-transparent px-1 pt-1 pb-1 text-sm font-medium transition-colors">
                            <i class="fas fa-ticket-alt mr-2"></i>Réservations
                        </a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="relative p-2 text-gray-400 hover:text-gray-500 transition-colors">
                        <i class="fas fa-bell text-lg"></i>
                        <span class="absolute -top-1 -right-1 block h-4 w-4 rounded-full bg-danger text-xs text-white flex items-center justify-center">3</span>
                    </button>
                    <div class="relative">
                        <button class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 transition-colors">
                            <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <span class="text-sm font-medium">Admin</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile menu -->
    <div class="md:hidden">
        <div class="pt-16 pb-3 space-y-1 bg-white shadow">
            <a href="../admin/index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-50'; ?> block px-3 py-2 text-base font-medium">
                <i class="fas fa-tachometer-alt mr-2"></i>Tableau de Bord
            </a>
            <a href="../admin/programmes.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'programmes.php' ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-50'; ?> block px-3 py-2 text-base font-medium">
                <i class="fas fa-route mr-2"></i>Programmes
            </a>
            <a href="../admin/reservations.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'reservations.php' ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-50'; ?> block px-3 py-2 text-base font-medium">
                <i class="fas fa-ticket-alt mr-2"></i>Réservations
            </a>
        </div>
    </div>

    <!-- Content -->
    <main class="pt-16 min-h-screen">