@extends('layouts.maquette')

@section('content')
        <!-- DASHBOARD -->
        <section id="dashboard" class="tab-content active">
            <div class="header">
                <h1>Tableau de bord</h1>
                <div class="header-actions">
                    <input type="text" class="search-box" placeholder="Rechercher un équipement...">
                    <button class="btn btn-primary" onclick="openModal('addEquipmentModal')">+ Ajouter équipement</button>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total équipements</div>
                    <div class="stat-value">400+</div>
                </div>
                <div class="stat-card blue">
                    <div class="stat-label">Fonctionnels</div>
                    <div class="stat-value">360+</div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-label">En maintenance</div>
                    <div class="stat-value">25</div>
                </div>
                <div class="stat-card red">
                    <div class="stat-label">Hors service</div>
                    <div class="stat-value">15</div>
                </div>
            </div>

            <div class="card">
                <div class="card-title">
                    Équipements critiques
                    <a href="#">Voir tous →</a>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Nom</th>
                                <th>Catégorie</th>
                                <th>État</th>
                                <th>Responsable</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#MBOT2-001</td>
                                <td>MBot2</td>
                                <td>Robots</td>
                                <td><span class="badge badge-danger">Usé</span></td>
                                <td>Fréderic SOUBEIGA</td>
                            </tr>
                            <tr>
                                <td>#SPIDER-001</td>
                                <td>Hiwonder SpiderPi</td>
                                <td>Robots</td>
                                <td><span class="badge badge-danger">Usé</span></td>
                                <td>Fréderic SOUBEIGA</td>
                            </tr>
                            <tr>
                                <td>#TELLO-004</td>
                                <td>Drone Tello</td>
                                <td>Drones</td>
                                <td><span class="badge badge-warning">Bug</span></td>
                                <td>Roch SAWADOGO</td>
                            </tr>
                            <tr>
                                <td>#PC-ACER-001</td>
                                <td>Ordinateur Acer</td>
                                <td>Informatique</td>
                                <td><span class="badge badge-warning">Bug</span></td>
                                <td>Général</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-title">
                    Maintenances prévues cette semaine
                    <a href="#">Voir plus →</a>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Équipement</th>
                                <th>Type</th>
                                <th>Fréquence</th>
                                <th>Date prévue</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Drone FPV</td>
                                <td>Hebdomadaire</td>
                                <td>Contrôle visuel, batteries, hélices</td>
                                <td>18/10/2025</td>
                                <td><span class="badge badge-info">Planifiée</span></td>
                            </tr>
                            <tr>
                                <td>Drone Parrot</td>
                                <td>Hebdomadaire</td>
                                <td>Capteurs, moteurs, calibrations</td>
                                <td>19/10/2025</td>
                                <td><span class="badge badge-info">Planifiée</span></td>
                            </tr>
                            <tr>
                                <td>Ordinateurs portables</td>
                                <td>Trimestrielle</td>
                                <td>Nettoyage, mise à jour, antivirus</td>
                                <td>25/10/2025</td>
                                <td><span class="badge badge-secondary">À planifier</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- INVENTORY -->
        <section id="inventory" class="tab-content">
            <div class="header">
                <h1>Inventaire du matériel</h1>
                <div class="header-actions">
                    <input type="text" class="search-box" placeholder="Rechercher...">
                    <button class="btn btn-primary" onclick="openModal('addEquipmentModal')">+ Ajouter</button>
                </div>
            </div>

            <div class="tabs">
                <button class="tab-btn active" onclick="filterCategory('tous')">Tous (400+)</button>
                <button class="tab-btn" onclick="filterCategory('drones')">Drones (43)</button>
                <button class="tab-btn" onclick="filterCategory('robots')">Robots (36)</button>
                <button class="tab-btn" onclick="filterCategory('informatique')">Informatique (46)</button>
                <button class="tab-btn" onclick="filterCategory('electronique')">Électronique (110+)</button>
            </div>

            <div class="card">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Nom</th>
                                <th>Marque</th>
                                <th>Catégorie</th>
                                <th>Quantité</th>
                                <th>État</th>
                                <th>Emplacement</th>
                                <th>Responsable</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>DRN-FPV-001</td>
                                <td>Drone FPV</td>
                                <td>Divers</td>
                                <td>Drones</td>
                                <td>14</td>
                                <td><span class="badge badge-success">Fonctionnels</span></td>
                                <td>Armoire plastique - Salle Simulateur</td>
                                <td>Roch SAWADOGO</td>
                                <td><button class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.8em;">Détails</button></td>
                            </tr>
                            <tr>
                                <td>DRN-PARROT-001</td>
                                <td>Drone Parrot (Mongo Fly, AR)</td>
                                <td>Parrot</td>
                                <td>Drones</td>
                                <td>12</td>
                                <td><span class="badge badge-warning">10 OK / 2 Usés</span></td>
                                <td>Armoire plastique - Salle Simulateur</td>
                                <td>Roch SAWADOGO</td>
                                <td><button class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.8em;">Détails</button></td>
                            </tr>
                            <tr>
                                <td>ROB-MBOT2-001</td>
                                <td>MBot2</td>
                                <td>MakeBlock</td>
                                <td>Robots</td>
                                <td>5</td>
                                <td><span class="badge badge-danger">Usé</span></td>
                                <td>Placard - Salle Formation</td>
                                <td>Fréderic SOUBEIGA</td>
                                <td><button class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.8em;">Détails</button></td>
                            </tr>
                            <tr>
                                <td>PC-HP-001</td>
                                <td>Unité centrale HP</td>
                                <td>HP</td>
                                <td>Informatique</td>
                                <td>7</td>
                                <td><span class="badge badge-success">Fonctionnels</span></td>
                                <td>Salle Formation & Bureaux</td>
                                <td>Général</td>
                                <td><button class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.8em;">Détails</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- MOVEMENTS -->
        <section id="tracking" class="tab-content">
            <div class="header">
                <h1>Suivi des mouvements</h1>
                <button class="btn btn-primary" onclick="openModal('addMovementModal')">+ Enregistrer mouvement</button>
            </div>

            <div class="card">
                <div class="card-title">Enregistrer un mouvement</div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Équipement</label>
                        <input type="text" placeholder="Rechercher un équipement...">
                    </div>
                    <div class="form-group">
                        <label>Type de mouvement</label>
                        <select>
                            <option>Sortie</option>
                            <option>Retour</option>
                            <option>Transfert</option>
                            <option>Maintenance</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Utilisateur</label>
                        <input type="text" placeholder="Nom de l'utilisateur">
                    </div>
                </div>
                <div class="form-group">
                    <label>Remarques</label>
                    <textarea rows="3" placeholder="Détails du mouvement..."></textarea>
                </div>
                <button class="btn btn-primary">Valider le mouvement</button>
            </div>

            <div class="card">
                <div class="card-title">
                    Historique des mouvements
                    <a href="#">Voir tous →</a>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Équipement</th>
                                <th>Type</th>
                                <th>Utilisateur</th>
                                <th>Zone</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>15/10/2025 14:30</td>
                                <td>Drone FPV</td>
                                <td><span class="badge badge-info">Sortie</span></td>
                                <td>Roch SAWADOGO</td>
                                <td>Armoire plastique → Salle Démo</td>
                                <td><span class="badge badge-success">Terminé</span></td>
                            </tr>
                            <tr>
                                <td>15/10/2025 10:15</td>
                                <td>Robot NAO V6</td>
                                <td><span class="badge badge-success">Retour</span></td>
                                <td>Fréderic SOUBEIGA</td>
                                <td>Salle Formation → Placard</td>
                                <td><span class="badge badge-success">Terminé</span></td>
                            </tr>
                            <tr>
                                <td>14/10/2025 16:45</td>
                                <td>Ordinateur Portable</td>
                                <td><span class="badge badge-warning">Maintenance</span></td>
                                <td>Général</td>
                                <td>Bureau → Atelier technique</td>
                                <td><span class="badge badge-warning">En cours</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- MAINTENANCE -->
        <section id="maintenance" class="tab-content">
            <div class="header">
                <h1>Planification maintenance</h1>
                <button class="btn btn-primary" onclick="openModal('addMaintenanceModal')">+ Planifier</button>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Planifiées</div>
                    <div class="stat-value">12</div>
                </div>
                <div class="stat-card blue">
                    <div class="stat-label">En cours</div>
                    <div class="stat-value">3</div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-label">Urgentes</div>
                    <div class="stat-value">2</div>
                </div>
                <div class="stat-card red">
                    <div class="stat-label">Retard</div>
                    <div class="stat-value">1</div>
                </div>
            </div>

            <div class="card">
                <div class="card-title">
                    Maintenances planifiées
                    <a href="#">Voir toutes →</a>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Équipement</th>
                                <th>Type</th>
                                <th>Fréquence</th>
                                <th>Date prévue</th>
                                <th>Technicien</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Drone FPV</td>
                                <td>Hebdomadaire</td>
                                <td>Contrôle visuel, batteries, hélices</td>
                                <td>18/10/2025</td>
                                <td>Roch SAWADOGO</td>
                                <td><span class="badge badge-info">Planifiée</span></td>
                            </tr>
                            <tr>
                                <td>Robot NAO V6</td>
                                <td>Batterie faible</td>
                                <td>Urgente</td>
                                <td>16/10/2025</td>
                                <td>Fréderic SOUBEIGA</td>
                                <td><span class="badge badge-danger">Urgente</span></td>
                            </tr>
                            <tr>
                                <td>Ordinateurs portables</td>
                                <td>Trimestrielle</td>
                                <td>Nettoyage, mise à jour, antivirus</td>
                                <td>25/10/2025</td>
                                <td>Général</td>
                                <td><span class="badge badge-secondary">À planifier</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ALERTS -->
        <section id="alerts" class="tab-content">
            <div class="header">
                <h1>Alertes actives</h1>
            </div>

            <div class="stats-grid">
                <div class="stat-card red">
                    <div class="stat-label">Critiques</div>
                    <div class="stat-value">3</div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-label">Hautes</div>
                    <div class="stat-value">5</div>
                </div>
                <div class="stat-card blue">
                    <div class="stat-label">Moyennes</div>
                    <div class="stat-value">8</div>
                </div>
            </div>

            <div class="card">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Équipement</th>
                                <th>Type d'alerte</th>
                                <th>Description</th>
                                <th>Urgence</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>MBot2</td>
                                <td>État critique</td>
                                <td>Équipement usé, nécessite réparation</td>
                                <td><span class="badge badge-danger">Critique</span></td>
                                <td>12/10/2025</td>
                            </tr>
                            <tr>
                                <td>Robot NAO V6</td>
                                <td>Batterie faible</td>
                                <td>Autonomie réduite, recharge recommandée</td>
                                <td><span class="badge badge-danger">Critique</span></td>
                                <td>15/10/2025</td>
                            </tr>
                            <tr>
                                <td>Drone Parrot</td>
                                <td>Usure détectée</td>
                                <td>2 drones présentent des signes d'usure</td>
                                <td><span class="badge badge-warning">Haute</span></td>
                                <td>14/10/2025</td>
                            </tr>
                            <tr>
                                <td>Ordinateur Acer</td>
                                <td>Bug logiciel</td>
                                <td>Diagnostique requis</td>
                                <td><span class="badge badge-warning">Haute</span></td>
                                <td>13/10/2025</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- 5S ZONES -->
        <section id="5s" class="tab-content">
            <div class="header">
                <h1>Zone 5S - Conformité</h1>
            </div>

            <div class="zones-grid">
                <div class="zone-card conforme">
                    <div class="zone-icon">✅</div>
                    <div class="zone-name">Zone A</div>
                    <div class="zone-status">Armoires Drones<br><strong>Conforme</strong></div>
                </div>

                <div class="zone-card conforme">
                    <div class="zone-icon">✅</div>
                    <div class="zone-name">Zone B</div>
                    <div class="zone-status">Placard Robots<br><strong>Conforme</strong></div>
                </div>

                <div class="zone-card conforme">
                    <div class="zone-icon">✅</div>
                    <div class="zone-name">Zone C</div>
                    <div class="zone-status">Salle Formation<br><strong>Conforme</strong></div>
                </div>

                <div class="zone-card attention">
                    <div class="zone-icon">⚠️</div>
                    <div class="zone-name">Zone D</div>
                    <div class="zone-status">Outils Maintenance<br><strong>Attention</strong></div>
                </div>

                <div class="zone-card conforme">
                    <div class="zone-icon">✅</div>
                    <div class="zone-name">Zone E</div>
                    <div class="zone-status">Électronique<br><strong>Conforme</strong></div>
                </div>

                <div class="zone-card attention">
                    <div class="zone-icon">⚠️</div>
                    <div class="zone-name">Zone F</div>
                    <div class="zone-status">Stockage Général<br><strong>Réorganisation</strong></div>
                </div>
            </div>

            <div class="card" style="margin-top: 25px;">
                <div class="card-title">Principes 5S appliqués</div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #27ae60;">
                        <strong>Seiri (Trier)</strong><br>
                        <small>Élimination des éléments inutiles</small>
                    </div>
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #27ae60;">
                        <strong>Seiton (Ranger)</strong><br>
                        <small>Organisation optimale des équipements</small>
                    </div>
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #27ae60;">
                        <strong>Seiso (Nettoyer)</strong><br>
                        <small>Maintien de la propreté et l'ordre</small>
                    </div>
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #27ae60;">
                        <strong>Seiketsu (Standardiser)</strong><br>
                        <small>Établissement de normes et procédures</small>
                    </div>
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #27ae60;">
                        <strong>Shitsuke (Suivre)</strong><br>
                        <small>Discipline et amélioration continue</small>
                    </div>
                </div>
            </div>
        </section>

        <!-- REPORTS -->
        <section id="reports" class="tab-content">
            <div class="header">
                <h1>Rapports et statistiques</h1>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Équipements inventoriés</div>
                    <div class="stat-value">400+</div>
                </div>
                <div class="stat-card blue">
                    <div class="stat-label">Catégories</div>
                    <div class="stat-value">8</div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-label">Maintenances ce mois</div>
                    <div class="stat-value">28</div>
                </div>
                <div class="stat-card red">
                    <div class="stat-label">Équipements critiques</div>
                    <div class="stat-value">4</div>
                </div>
            </div>

            <div class="card">
                <div class="card-title">Résumé par catégorie</div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Catégorie</th>
                                <th>Total</th>
                                <th>Fonctionnels</th>
                                <th>En maintenance</th>
                                <th>Hors service</th>
                                <th>% Disponibilité</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Drones</strong></td>
                                <td>43</td>
                                <td>38</td>
                                <td>3</td>
                                <td>2</td>
                                <td><span class="badge badge-success">88%</span></td>
                            </tr>
                            <tr>
                                <td><strong>Robots</strong></td>
                                <td>36</td>
                                <td>28</td>
                                <td>5</td>
                                <td>3</td>
                                <td><span class="badge badge-success">78%</span></td>
                            </tr>
                            <tr>
                                <td><strong>Informatique</strong></td>
                                <td>46</td>
                                <td>39</td>
                                <td>4</td>
                                <td>3</td>
                                <td><span class="badge badge-success">85%</span></td>
                            </tr>
                            <tr>
                                <td><strong>Électronique</strong></td>
                                <td>110+</td>
                                <td>105</td>
                                <td>3</td>
                                <td>2</td>
                                <td><span class="badge badge-success">95%</span></td>
                            </tr>
                            <tr>
                                <td><strong>Outils & Accessoires</strong></td>
                                <td>170+</td>
                                <td>165</td>
                                <td>3</td>
                                <td>2</td>
                                <td><span class="badge badge-success">97%</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    <!-- MODALS -->
    <div id="addEquipmentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Ajouter un équipement</h2>
                <button class="close-btn" onclick="closeModal('addEquipmentModal')">×</button>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Nom de l'équipement</label>
                    <input type="text" placeholder="Ex: Drone FPV">
                </div>
                <div class="form-group">
                    <label>Catégorie</label>
                    <select>
                        <option>Sélectionner...</option>
                        <option>Drones</option>
                        <option>Robots</option>
                        <option>Informatique</option>
                        <option>Électronique</option>
                        <option>Outils</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Marque/Modèle</label>
                    <input type="text" placeholder="Ex: DJI Phantom">
                </div>
                <div class="form-group">
                    <label>État</label>
                    <select>
                        <option>Fonctionnel</option>
                        <option>Usé</option>
                        <option>Bug</option>
                        <option>Hors service</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Emplacement</label>
                <input type="text" placeholder="Ex: Armoire plastique - Salle Simulateur">
            </div>
            <button class="btn btn-primary" style="width: 100%;">Enregistrer</button>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function showSection(section, e) {
            e.preventDefault();
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.nav-link').forEach(el => el.classList.remove('active'));
            document.getElementById(section).classList.add('active');
            e.target.closest('a').classList.add('active');
        }

        function openModal(id) {
            document.getElementById(id).classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        function filterCategory(cat) {
            console.log('Filtering by:', cat);
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        }
    </script>
@endpush