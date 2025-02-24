<link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<header class="p-3 mb-3 border-bottom bg-dark">
  <div class="container">
    <div class="d-flex flex-wrap align-items-center justify-content-between ">

      <!-- Logo -->
      <a href="home.php" class="navbar-brand fw-bold link-light">AutoViaje</a>

      <!-- Navegación -->
      <ul class="nav col-lg-auto mb-2 mb-lg-0">
        <li><a href="home.php" class="nav-link px-3 link-light">Inicio</a></li>
        <li><a href="create_auto.php" class="nav-link px-3 link-light">Crear</a></li>
        <li><a href="exportar.php" class="nav-link px-3 link-light">Exportar</a></li>
      </ul>

      <!-- Usuario / Logout -->
      <div class="dropdown text-end">
        <a href="#" class="d-block text-light text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
          <?php
          if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
            echo htmlspecialchars($_SESSION['username']);
          } else {
            echo "Usuario no logueado";
          }
          ?>
        </a>
        <ul class="dropdown-menu text-small">
          <li><a class="dropdown-item" href="../views/auth/logout.php">Cerrar sesión</a></li>
        </ul>
      </div>
    </div>
  </div>
</header>
<script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
