</main>

    <!-- Notifications -->
    <div id="notifications" class="fixed bottom-4 right-4 z-50 space-y-2"></div>

    <script>
        // Fonction pour afficher les notifications
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            const colors = {
                'success': 'bg-green-500',
                'error': 'bg-red-500',
                'warning': 'bg-yellow-500',
                'info': 'bg-blue-500'
            };
            
            notification.className = `notification ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg max-w-sm transform translate-x-full`;
            notification.innerHTML = `
                <div class="flex items-center justify-between">
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            document.getElementById('notifications').appendChild(notification);
            
            // Animation d'entrée
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            // Auto-suppression après 5 secondes
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }

        // Fonction pour vérifier les programmes complets
        function checkFullPrograms() {
            // Cette fonction serait appelée périodiquement pour vérifier
            // si des programmes sont devenus complets
            <?php
            require_once 'classes/Programme.php';
            $programme = new Programme();
            $programmes = $programme->lireTous();
            foreach ($programmes as $prog) {
                if ($prog['statut'] == 'complet' && $prog['places_reservees'] == $prog['capacite']) {
                    echo "showNotification('Bus {$prog['itineraire']} du {$prog['date_depart']} à {$prog['heure_depart']} est maintenant COMPLET!', 'warning');";
                }
            }
            ?>
        }

        // Vérifier les programmes complets au chargement
        document.addEventListener('DOMContentLoaded', function() {
            checkFullPrograms();
        });

        // Confirmation pour les actions de suppression
        function confirmDelete(message = 'Êtes-vous sûr de vouloir supprimer cet élément ?') {
            return confirm(message);
        }

        // Animation pour les cartes
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card-hover');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>