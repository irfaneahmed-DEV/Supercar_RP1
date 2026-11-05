══════════════════════════════════════════════════
  SUPERCAR — PARTIE ADMIN  |  Guide d'installation
══════════════════════════════════════════════════

STRUCTURE DES FICHIERS
──────────────────────
SuperCar2_fixed/
├── admin/                    ← NOUVEAU dossier admin
│   ├── login_admin.php       ← Page de connexion admin
│   ├── dashboard.php         ← Tableau de bord (stats)
│   ├── clients.php           ← Gestion clients
│   ├── voitures.php          ← Gestion voitures (CRUD complet)
│   ├── services.php          ← Gestion services (CRUD complet)
│   ├── demandes.php          ← Gestion demandes d'essai + statuts
│   ├── contacts.php          ← Gestion messages de contact
│   ├── deconnexion_admin.php ← Déconnexion
│   ├── admin_guard.php       ← Protection des pages (inclus auto)
│   ├── admin_layout.php      ← CSS + sidebar partagés
│   └── setup_admin.php       ← Script de création du compte admin
└── supercarv3_patch_admin.sql ← Patch SQL à importer

════════════════════════════════════
  ÉTAPE 1 : IMPORTER LE PATCH SQL
════════════════════════════════════

1. Ouvrir phpMyAdmin → base `supercarv3`
2. Onglet "Importer"
3. Sélectionner : supercarv3_patch_admin.sql
4. Cliquer "Exécuter"

════════════════════════════════════
  ÉTAPE 2 : CRÉER LE COMPTE ADMIN
════════════════════════════════════

Ouvrir dans le navigateur :
  http://localhost/SuperCar2_fixed/admin/setup_admin.php

Remplir le formulaire :
  - Identifiant : admin (ou ce que vous voulez)
  - Nom affiché : Administrateur
  - Mot de passe : choisissez un mot de passe fort (min. 8 car.)

⚠ SUPPRIMER setup_admin.php après création du compte !

════════════════════════════════════
  ÉTAPE 3 : SE CONNECTER
════════════════════════════════════

URL de connexion admin :
  http://localhost/SuperCar2_fixed/admin/login_admin.php

Les sessions admin et client sont SÉPARÉES.
Un client connecté ne peut pas accéder à l'admin.
L'admin ne touche pas à la session client.

════════════════════════════════════
  FONCTIONNALITÉS ADMIN
════════════════════════════════════

📊 Dashboard     → Compteurs en temps réel (clients, voitures,
                    services, demandes, messages, demandes en attente)
                  → Tableau des 10 dernières demandes d'essai

👥 Clients       → Liste tous les clients inscrits
                  → Affiche nb de demandes et messages par client
                  → Suppression avec confirmation (supprime aussi
                    ses demandes, messages et login associés)

🚗 Voitures      → Liste toutes les voitures avec miniature
                  → Ajouter une voiture (modale)
                  → Modifier une voiture (modale préremplie)
                  → Supprimer avec confirmation

🔧 Services      → Liste les services avec image
                  → Ajouter / Modifier / Supprimer

📋 Demandes      → Liste toutes les demandes d'essai
                  → Changer le statut inline :
                      • en attente → acceptée → refusée
                  → Supprimer une demande

✉ Messages       → Liste tous les messages de contact
                  → Supprimer un message

════════════════════════════════════
  SÉCURITÉ
════════════════════════════════════

• Toutes les pages admin vérifient admin_guard.php
• Session admin séparée de la session client
• Mots de passe hashés bcrypt (via PHP password_hash)
• Toutes les requêtes SQL utilisent des requêtes préparées
• Confirmation JS avant chaque suppression
• Pas d'accès direct aux fichiers sans session active

══════════════════════════════════════════════════
