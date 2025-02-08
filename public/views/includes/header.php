<link rel="stylesheet" href="../views/includes/css/header.css">

<div id="header">
  <div class="logo">
    <a href="home.php">
      AutoViaje</a>
  </div>
  <nav>
    <ul>
      <li>
        <a href="home.php">Buscar</a>
      </li>
      <li>
        <a href="create_auto.php">Crear</a>
      </li>
      <li>
        <a href="#">Exportar</a>
      </li>
      <li class="dropdown">
        <a href="">
          <?php
          if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
            echo htmlspecialchars($_SESSION['username']);
          } else {
            echo "Usuario no logueado";
          }
          ?>
        </a>
        <ul>
          <li><a href="../views/auth/logout.php">Cerrar sesión</a></li>
        </ul>
      </li>
    </ul>
  </nav>
</div>