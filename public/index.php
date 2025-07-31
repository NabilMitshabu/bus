<?php
$page_title = "TransportLub - Réservez votre voyage à Lubumbashi";
require_once '../includes/public_header.php';
require_once '../classes/Programme.php';

$programme = new Programme();
// Récupérer tous les programmes avec les noms des agences
$db = new Database();
$query = "SELECT p.*, a.nom as agence_nom, a.telephone as agence_telephone 
          FROM programmes p 
          JOIN agences a ON p.agence_id = a.id 
          WHERE p.statut = 'actif' AND p.places_reservees < p.capacite 
          ORDER BY p.date_depart ASC, p.heure_depart ASC";
$stmt = $db->query($query);
$programmes_disponibles = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

// Grouper par itinéraire
$itineraires = [];
foreach ($programmes_disponibles as $prog) {
    $itineraires[$prog['itineraire']][] = $prog;
}
?>

<!-- Hero Section -->
<section class="bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6 animate-fade-in">
                Voyagez en toute sécurité
                <span class="block text-blue-200">à travers le Katanga</span>
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-blue-100 max-w-3xl mx-auto">
                Réservez votre place dans nos bus confortables pour tous vos déplacements 
                depuis Lubumbashi vers les principales villes du Katanga
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#programmes" class="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-blue-50 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-search mr-2"></i>Voir les programmes
                </a>
                <a href="#contact" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white hover:text-blue-600 transition-all duration-300">
                    <i class="fas fa-phone mr-2"></i>Nous contacter
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Pourquoi choisir TransportLub ?</h2>
            <p class="text-gray-600 text-lg">Des services de qualité pour vos déplacements</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-2xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Sécurité garantie</h3>
                <p class="text-gray-600">Nos chauffeurs expérimentés et nos véhicules régulièrement entretenus assurent votre sécurité</p>
            </div>
            <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clock text-2xl text-green-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Ponctualité</h3>
                <p class="text-gray-600">Respectez vos rendez-vous grâce à nos horaires stricts et notre ponctualité légendaire</p>
            </div>
            <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-dollar-sign text-2xl text-orange-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Prix abordables</h3>
                <p class="text-gray-600">Des tarifs compétitifs pour tous les budgets sans compromis sur la qualité</p>
            </div>
        </div>
    </div>
</section>

<!-- Programmes Section -->
<section id="programmes" class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Nos destinations</h2>
            <p class="text-gray-600 text-lg">Choisissez votre destination et réservez votre place</p>
        </div>

        <?php if (empty($itineraires)): ?>
        <div class="text-center py-12">
            <i class="fas fa-bus text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">Aucun programme disponible</h3>
            <p class="text-gray-500">Revenez plus tard pour voir nos prochains départs</p>
        </div>
        <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <?php foreach ($itineraires as $itineraire => $programmes): ?>
            <div class="bg-white border border-gray-200 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold mb-2"><?php echo $itineraire; ?></h3>
                            <p class="text-blue-100">
                                <i class="fas fa-users mr-2"></i>
                                <?php echo count($programmes); ?> départ(s) disponible(s)
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <i class="fas fa-bus text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        <?php foreach (array_slice($programmes, 0, 3) as $prog): 
                            $places_disponibles = $prog['capacite'] - $prog['places_reservees'];
                        ?>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <i class="fas fa-calendar text-blue-600"></i>
                                    <span class="font-medium text-gray-900">
                                        <?php echo date('d/m/Y', strtotime($prog['date_depart'])); ?>
                                    </span>
                                    <span class="text-gray-500">•</span>
                                    <i class="fas fa-clock text-green-600"></i>
                                    <span class="text-gray-700">
                                        <?php echo $prog['heure_depart']; ?>
                                    </span>
                                </div>
                                <div class="flex items-center space-x-4 text-sm text-gray-600 mb-1">
                                    <span>
                                        <i class="fas fa-chair mr-1"></i>
                                        <?php echo $places_disponibles; ?> places disponibles
                                    </span>
                                    <span class="text-green-600 font-semibold">
                                        <?php echo number_format($prog['prix']); ?> FC
                                    </span>
                                </div>
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-building mr-1"></i>
                                    Agence: <?php echo htmlspecialchars($prog['agence_nom']); ?>
                                </div>
                            </div>
                            <button onclick="openReservationModal(<?php echo $prog['id']; ?>, '<?php echo $prog['itineraire']; ?>', '<?php echo date('d/m/Y', strtotime($prog['date_depart'])); ?>', '<?php echo $prog['heure_depart']; ?>', <?php echo $prog['prix']; ?>, <?php echo $places_disponibles; ?>)" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-ticket-alt mr-2"></i>Réserver
                            </button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (count($programmes) > 3): ?>
                    <div class="mt-4 text-center">
                        <button class="text-blue-600 hover:text-blue-800 font-medium">
                            Voir tous les départs (<?php echo count($programmes); ?>)
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-16 bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">Contactez-nous</h2>
            <p class="text-gray-300 text-lg">Nous sommes là pour vous aider</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-phone text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Téléphone</h3>
                <p class="text-gray-300">+243 990 123 456</p>
                <p class="text-gray-300">+243 990 654 321</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-envelope text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Email</h3>
                <p class="text-gray-300">info@transportlub.cd</p>
                <p class="text-gray-300">reservations@transportlub.cd</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-map-marker-alt text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Adresse</h3>
                <p class="text-gray-300">Avenue Mobutu</p>
                <p class="text-gray-300">Centre-ville, Lubumbashi</p>
            </div>
        </div>
    </div>
</section>

<!-- Modal de Réservation -->
<div id="reservationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-900">Réserver votre place</h3>
                    <button onclick="closeReservationModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div id="programmeInfo" class="mt-3 text-sm text-gray-600"></div>
            </div>
            
            <form id="reservationForm" class="p-6 space-y-4">
                <input type="hidden" id="programme_id" name="programme_id">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nom complet *</label>
                    <input type="text" name="nom_client" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone *</label>
                    <input type="tel" name="telephone" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de places *</label>
                    <select name="nombre_places" id="nombre_places" required onchange="updateTotal()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Sélectionner</option>
                    </select>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-gray-700">Total à payer:</span>
                        <span id="totalPrice" class="text-xl font-bold text-blue-600">0 FC</span>
                    </div>
                </div>
                
                <div class="flex space-x-3 pt-4">
                    <button type="button" onclick="closeReservationModal()" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Annuler
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-check mr-2"></i>Confirmer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentProgramme = {};

function openReservationModal(id, itineraire, date, heure, prix, placesDisponibles) {
    currentProgramme = { id, itineraire, date, heure, prix, placesDisponibles };
    
    document.getElementById('programme_id').value = id;
    document.getElementById('programmeInfo').innerHTML = `
        <i class="fas fa-route mr-2"></i>${itineraire} • 
        <i class="fas fa-calendar mr-2"></i>${date} • 
        <i class="fas fa-clock mr-2"></i>${heure}
    `;
    
    // Remplir les options de places
    const placesSelect = document.getElementById('nombre_places');
    placesSelect.innerHTML = '<option value="">Sélectionner</option>';
    for (let i = 1; i <= Math.min(placesDisponibles, 5); i++) {
        placesSelect.innerHTML += `<option value="${i}">${i} place${i > 1 ? 's' : ''}</option>`;
    }
    
    document.getElementById('reservationModal').classList.remove('hidden');
}

function closeReservationModal() {
    document.getElementById('reservationModal').classList.add('hidden');
    document.getElementById('reservationForm').reset();
}

function updateTotal() {
    const places = parseInt(document.getElementById('nombre_places').value) || 0;
    const total = places * currentProgramme.prix;
    document.getElementById('totalPrice').textContent = total.toLocaleString() + ' FC';
}

document.getElementById('reservationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'nouvelle_reservation');
    formData.append('prix_total', parseInt(document.getElementById('nombre_places').value) * currentProgramme.prix);
    
    fetch('process_reservation.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Réservation effectuée avec succès! Un email de confirmation vous a été envoyé.');
            closeReservationModal();
            location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        alert('Erreur lors de la réservation. Veuillez réessayer.');
    });
});

// Smooth scrolling
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    dropdown.classList.toggle('hidden');
    
    // Fermer les autres dropdowns
    const allDropdowns = document.querySelectorAll('[id$="Dropdown"]');
    allDropdowns.forEach(item => {
        if (item.id !== dropdownId) {
            item.classList.add('hidden');
        }
    });
}

// Fermer les dropdowns quand on clique ailleurs
document.addEventListener('click', function(event) {
    if (!event.target.closest('.relative')) {
        const allDropdowns = document.querySelectorAll('[id$="Dropdown"]');
        allDropdowns.forEach(item => {
            item.classList.add('hidden');
        });
    }
});

function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    menu.classList.toggle('hidden');
}
</script>

<?php require_once '../includes/public_footer.php'; ?>