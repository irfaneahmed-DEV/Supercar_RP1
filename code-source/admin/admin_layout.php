<?php
// admin_layout.php

function get_sidebar(string $activePage = ''): string
{
    $menuItems = [
        'dashboard' => ['label' => '📊 Dashboard', 'url' => 'dashboard.php'],
        'accueil'  => ['label' => '🏠 Accueil',  'url' => 'Accueil.php'],
        'voitures'  => ['label' => '🚗 Voitures',  'url' => 'voitures.php'],
        'services'  => ['label' => '🔧 Services',  'url' => 'services.php'],
        'demandes'  => ['label' => '📋 Demande d\'Essai', 'url' => 'demandes.php'],
        'clients'   => ['label' => '👥 Clients',   'url' => 'clients.php'],
        'contacts'  => ['label' => '✉️ Messages',  'url' => 'contacts.php'],
        'setup_admin'  => ['label' => '⚙️ Configuration',  'url' => 'setup_admin.php'],
    ];

    ob_start();
    ?>
    <!-- Barre de navigation mobile / Menu Burger (affichée uniquement sur mobile & tablette) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark d-lg-none border-bottom border-secondary mb-3">
      <div class="container-fluid">
        <a class="navbar-brand fw-bold text-danger" href="dashboard.php"> SUPERCAR ADMIN</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminMobileSidebar" aria-controls="adminMobileSidebar" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>
    </nav>

    <!-- Offcanvas pour écrans mobiles & tablettes -->
    <div class="offcanvas offcanvas-start bg-dark text-light d-lg-none" tabindex="-1" id="adminMobileSidebar">
      <div class="offcanvas-header border-bottom border-secondary">
        <h5 class="offcanvas-title text-danger fw-bold"> SUPERCAR ADMIN</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body d-flex flex-column justify-content-between">
        <ul class="nav nav-pills flex-column mb-auto">
          <?php foreach ($menuItems as $key => $item): ?>
            <?php $isActive = (strtolower($activePage) === strtolower($key) || strtolower($activePage) === strtolower($item['label'])); ?>
            <li class="nav-item mb-1">
              <a href="<?= $item['url'] ?>" class="nav-link <?= $isActive ? 'active bg-danger text-white' : 'text-light' ?>">
                <?= $item['label'] ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
        <div class="pt-3 border-top border-secondary">
          <a href="deconnexion_admin.php" class="btn btn-outline-danger w-100">Déconnexion</a>
        </div>
      </div>
    </div>

    <!-- Sidebar fixe pour grand écran (Desktop >= lg) -->
    <aside class="admin-sidebar bg-dark text-light d-none d-lg-flex flex-column p-3 border-end border-secondary" style="min-width: 250px; min-height: 100vh;">
      <div class="mb-4 text-center">
        <h2 class="h4 text-danger fw-bold">🏎️ SUPERCAR</h2>
        <span class="small text-secondary">Back-Office</span>
      </div>
      <ul class="nav nav-pills flex-column mb-auto">
        <?php foreach ($menuItems as $key => $item): ?>
          <?php $isActive = (strtolower($activePage) === strtolower($key) || strtolower($activePage) === strtolower($item['label'])); ?>
          <li class="nav-item mb-1">
            <a href="<?= $item['url'] ?>" class="nav-link <?= $isActive ? 'active bg-danger text-white' : 'text-light' ?>">
              <?= $item['label'] ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
      <div class="pt-3 border-top border-secondary">
        <a href="deconnexion_admin.php" class="btn btn-outline-danger w-100">Déconnexion</a>
      </div>
    </aside>
    <?php
    return ob_get_clean();
}