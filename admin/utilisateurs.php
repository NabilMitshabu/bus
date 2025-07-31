<?php
$page_title = "Gestion des Utilisateurs - Transport Lubumbashi";

require_once(__DIR__ . '/../includes/header.php');
require_once(__DIR__ . '/../classes/Utilisateur.php');
require_once(__DIR__ . '/../classes/Auth.php');

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header('Location: ../index.php');
    exit;
}

$utilisateur = new Utilisateur();
$agence_id = $_SESSION['agence_id'];

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'ajouter':
                $data = [
                    'agence_id' => $agence_id,
                    'nom_complet' => $_POST['nom_complet'],
                    'email' => $_POST['email'],
                    'mot_de_passe' => $_POST['mot_de_passe'],
                    'role' => $_POST['role']
                ];
                if ($utilisateur->creer($data)) {
                    $success = "Utilisateur ajouté avec succès!";
                } else {
                    $error = "Erreur lors de l'ajout de l'utilisateur.";
                }
                break;
            case 'modifier':
                $data = [
                    'nom_complet' => $_POST['nom_complet'],
                    'email' => $_POST['email'],
                    'role' => $_POST['role'],
                    'statut' => $_POST['statut']
                ];
                if (!empty($_POST['mot_de_passe'])) {
                    $data['mot_de_passe'] = $_POST['mot_de_passe'];
                }
                if ($utilisateur->mettreAJour($_POST['user_id'], $data)) {
                    $success = "Utilisateur modifié avec succès!";
                } else {
                    $error = "Erreur lors de la modification.";
                }
                break;
            case 'supprimer':
                if ($utilisateur->supprimer($_POST['user_id'])) {
                    $success = "Utilisateur désactivé avec succès!";
                } else {
                    $error = "Erreur lors de la suppression.";
                }
                break;
        }
    }
}

$utilisateurs = $utilisateur->lireTousParAgence($agence_id);
$stats = $utilisateur->obtenirStatistiques($agence_id);
$activite_recente = $utilisateur->obtenirActiviteRecente($agence_id);
?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center animate-fade-in">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Gestion des Utilisateurs</h1>
                <p class="text-gray-600">Gérez les utilisateurs de votre agence</p>
            </div>
            <button onclick="toggleModal('addUserModal')" class="bg-primary hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg">
                <i class="fas fa-plus mr-2"></i>Nouvel Utilisateur
            </button>
        </div>
    </div>

    <!-- Messages -->
    <?php if (isset($success)): ?>
    <div class="px-4 sm:px-0 mb-6">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded animate-fade-in">
            <i class="fas fa-check-circle mr-2"></i><?php echo $success; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
    <div class="px-4 sm:px-0 mb-6">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded animate-fade-in">
            <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="px-4 sm:px-0 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="card-hover bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-users text-primary text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Utilisateurs</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total']; ?></p>
                    </div>
                </div>
            </div>
            <div class="card-hover bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-user-check text-secondary text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Actifs</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['actifs']; ?></p>
                    </div>
                </div>
            </div>
            <div class="card-hover bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-full">
                        <i class="fas fa-user-times text-danger text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Inactifs</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['inactifs']; ?></p>
                    </div>
                </div>
            </div>
            <div class="card-hover bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-user-shield text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Administrateurs</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['par_role']['admin'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des Utilisateurs -->
    <div class="px-4 sm:px-0">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Liste principale -->
            <div class="lg:col-span-2">
                <div class="card-hover bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-list mr-2"></i>Liste des Utilisateurs
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($utilisateurs as $user): 
                                    $statut_colors = [
                                        'actif' => 'bg-green-100 text-green-800',
                                        'inactif' => 'bg-red-100 text-red-800'
                                    ];
                                    $statut_color = $statut_colors[$user['statut']] ?? 'bg-gray-100 text-gray-800';
                                    
                                    $role_colors = [
                                        'admin' => 'bg-purple-100 text-purple-800',
                                        'manager' => 'bg-blue-100 text-blue-800',
                                        'employe' => 'bg-gray-100 text-gray-800',
                                        'caissier' => 'bg-yellow-100 text-yellow-800'
                                    ];
                                    $role_color = $role_colors[$user['role']] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center">
                                                <span class="text-white font-medium"><?php echo strtoupper(substr($user['nom_complet'], 0, 2)); ?></span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900"><?php echo $user['nom_complet']; ?></div>
                                                <div class="text-sm text-gray-500"><?php echo $user['email']; ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $role_color; ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $statut_color; ?>">
                                            <?php echo ucfirst($user['statut']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <button onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)" class="text-blue-600 hover:text-blue-900 transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <button onclick="deleteUser(<?php echo $user['id']; ?>)" class="text-red-600 hover:text-red-900 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Activité récente -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-clock mr-2 text-primary"></i>Activité Récente
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <?php foreach ($activite_recente as $activite): ?>
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-ticket-alt text-primary text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-900"><?php echo $activite['description']; ?></p>
                                    <p class="text-xs text-gray-500"><?php echo date('d/m/Y H:i', strtotime($activite['date'])); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter Utilisateur -->
<div id="addUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3">
            <h3 class="text-lg font-bold text-gray-900">Nouvel Utilisateur</h3>
            <button onclick="toggleModal('addUserModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form method="POST" action="" class="space-y-4">
            <input type="hidden" name="action" value="ajouter">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nom Complet</label>
                <input type="text" name="nom_complet" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mot de Passe</label>
                <input type="password" name="mot_de_passe" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Rôle</label>
                <select name="role" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="employe">Employé</option>
                    <option value="caissier">Caissier</option>
                    <option value="manager">Manager</option>
                    <option value="admin">Administrateur</option>
                </select>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="toggleModal('addUserModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Modifier Utilisateur -->
<div id="editUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3">
            <h3 class="text-lg font-bold text-gray-900">Modifier Utilisateur</h3>
            <button onclick="toggleModal('editUserModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form method="POST" action="" class="space-y-4" id="editUserForm">
            <input type="hidden" name="action" value="modifier">
            <input type="hidden" name="user_id" id="edit_user_id">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nom Complet</label>
                <input type="text" name="nom_complet" id="edit_nom_complet" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" name="email" id="edit_email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nouveau Mot de Passe (optionnel)</label>
                <input type="password" name="mot_de_passe" id="edit_mot_de_passe" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rôle</label>
                    <select name="role" id="edit_role" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="employe">Employé</option>
                        <option value="caissier">Caissier</option>
                        <option value="manager">Manager</option>
                        <option value="admin">Administrateur</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select name="statut" id="edit_statut" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="actif">Actif</option>
                        <option value="inactif">Inactif</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="toggleModal('editUserModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Modifier
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.toggle('hidden');
}

function editUser(user) {
    document.getElementById('edit_user_id').value = user.id;
    document.getElementById('edit_nom_complet').value = user.nom_complet;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_role').value = user.role;
    document.getElementById('edit_statut').value = user.statut;
    toggleModal('editUserModal');
}

function deleteUser(userId) {
    if (confirm('Êtes-vous sûr de vouloir désactiver cet utilisateur ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="supprimer">
            <input type="hidden" name="user_id" value="${userId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require_once(__DIR__ . '/../includes/footer.php'); ?>
