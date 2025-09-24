<?php
// Logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    if (file_exists('usuario_logado.txt')) {
        unlink('usuario_logado.txt');
    }
    header('Location: login.html');
    exit;
}

$usuarioLogado = file_exists("usuario_logado.txt") ? file_get_contents("usuario_logado.txt") : '';
$usuariosJson = file_exists("usuarios.json") ? file_get_contents("usuarios.json") : '[]';
$usuariosArray = json_decode($usuariosJson, true);

$nomeUsuario = $usuarioLogado;
$funcaoUsuario = '';
foreach ($usuariosArray as $usuario) {
    if (($usuario['usuario'] ?? '') === $usuarioLogado) {
        $nomeUsuario = $usuario['nome'];
        $funcaoUsuario = $usuario['role'] ?? '';
        break;
    }
}

$arquivos = glob("arquivos/{$usuarioLogado}-*.pdf");

// Avatar fixo
$avatar = "usuario.png";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Área do Usuário - Tuise</title>
  <link rel="icon" href="tuiselogo.png" type="image/x-icon" />
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: Arial, sans-serif; }
    body { display: flex; min-height: 100vh; background: #f5f7fa; }

    .sidebar {
      width: 250px;
      background: #9bb8f1;
      padding: 20px;
      color: white;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      position: relative;
      transition: left 0.3s ease;
    }

    .close-sidebar {
      display: none;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
      margin-bottom: 10px;
      text-align: right;
    }

    .sidebar .logo img {
      width: 50px;
      height: 50px;
      object-fit: cover;
      border-radius: 50%;
      margin-bottom: 10px;
    }

    .sidebar h1 { font-size: 15px; margin-bottom: 5px; }
    .sidebar p { font-size: 12px; }

    .actions button {
      margin-top: 20px;
      background: #007bff;
      color: white;
      padding: 8px 12px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .actions button:hover { background: #0056b3; }

    .main-area {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .header {
      background: linear-gradient(90deg, #04a8ec, #3673c2);
      color: white;
      padding: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }

    .user-info {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .user-info img {
      width: 40px;
      height: 40px;
      object-fit: cover;
      border-radius: 50%;
    }

    .search-input {
      padding: 8px 12px;
      border-radius: 20px;
      border: 1px solid #ccc;
      outline: none;
      transition: all 0.3s ease;
      font-size: 14px;
    }

    .search-input:focus {
      border-color: #007bff;
      box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }

    .search-container {
  position: relative;
  display: inline-block;
}

.search-icon {
  position: absolute;
  right: -1px;
  top: 50%;
  transform: translateY(-50%);
  width: 20px;
  height: 20px;
  pointer-events: none;
}

    .content {
      padding: 30px;
      flex: 1;
      background: #e9efff;
    }

    .holerite-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.12);
      padding: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
      flex-wrap: wrap;
    }

    .holerite-buttons {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .holerite-buttons button,
    .holerite-buttons a {
      background: linear-gradient(90deg, #04a8ec, #3673c2);
      color: white;
      border: none;
      padding: 8px 12px;
      border-radius: 5px;
      cursor: pointer;
      text-decoration: none;
      font-weight: 600;
    }

    .holerite-buttons button:hover,
    .holerite-buttons a:hover {
      background: linear-gradient(90deg, #04a8ec, #3673c2);
    }

    .hamburguer {
      display: none;
      cursor: pointer;
      padding: 10px;
    }

    .hamburguer span {
      display: block;
      height: 3px;
      background: white;
      margin: 6px 0;
      width: 25px;
      border-radius: 2px;
    }

    @media (max-width: 768px) {
      body {
        flex-direction: column;
      }

        .search-container {
    width: 80%;
    margin-bottom: 10px;
  }
  .search-input {
    width: 80%;
    padding-right: 0px; /* espaço para o ícone */
  }
  
      .sidebar {
        position: absolute;
        left: -100%;
        top: 0;
        height: 100vh;
        z-index: 10;
        background: #9bb8f1;
      }

      .sidebar.active {
        left: 0;
        width: 70%;
        max-width: 250px;
      }

      .close-sidebar {
        display: block;
      }

      .hamburguer {
        display: block;
      }
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
<aside class="sidebar" id="sidebar">
  <div class="close-sidebar" onclick="toggleSidebar()">&times;</div>
  <div class="logo">
    <img src="tuiselogo2.png" alt="Tuise">
    <h1 style="color:#2d2c82;">Gestão Integrada</h1>
    <p>Holerite Digital</p>
  </div>

  <!-- Menu de navegação -->
  <nav class="menu">
    <ul style="list-style: none; padding: 0; margin-top: 30px;">
      <li style="margin: 15px 0;">
        <a href="conta.php" style="display: flex; align-items: center; gap: 10px; color: #0056b3; text-decoration: none;">
          <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#007bff" class="bi bi-person-circle" viewBox="0 0 16 16">
            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
          </svg>
          Conta
        </a>
      </li>
<br><br>
      <li style="margin: 15px 0;">
        <a href="https://wa.me/552422639135" style="display: flex; align-items: center; gap: 10px; color: #0056b3; text-decoration: none;">
          <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#007bff" class="bi bi-headset" viewBox="0 0 16 16">
            <path d="M8 1a5 5 0 0 0-5 5v1h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a6 6 0 1 1 12 0v6a2.5 2.5 0 0 1-2.5 2.5H9.366a1 1 0 0 1-.866.5h-1a1 1 0 1 1 0-2h1a1 1 0 0 1 .866.5H11.5A1.5 1.5 0 0 0 13 12h-1a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h1V6a5 5 0 0 0-5-5"/>
          </svg>
          Suporte
        </a>
      </li>
<br><br>
      <li style="margin: 15px 0;">
        <a href="https://wa.me/552422639135" style="display: flex; align-items: center; gap: 10px; color: #0056b3; text-decoration: none;">
          <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#007bff" class="bi bi-question-circle-fill" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.496 6.033h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286a.237.237 0 0 0 .241.247m2.325 6.443c.61 0 1.029-.394 1.029-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94 0 .533.425.927 1.01.927z"/>
          </svg>
          Dúvidas
        </a>
      </li>
    </ul>
  </nav>

  <div class="actions">
    <form method="POST">
      <button type="submit" name="logout">Sair</button>
    </form>
  </div>
</aside>


  <!-- Main -->
  <div class="main-area">
    <header class="header">
      <div class="hamburguer" onclick="toggleSidebar()">
        <span></span><span></span><span></span>
      </div>
      <div>
        <h1>Bem vindo, <?= htmlspecialchars($nomeUsuario) ?></h1>
        <p>Consulte seu Holerite e faça o download</p>
      </div>
<div class="user-info">
  <div class="search-container">
    <input type="text" placeholder="Pesquisar" class="search-input" onkeyup="filtrarCards(this.value)">
    <img src="pesquisa.png" alt="Pesquisar" class="search-icon">
  </div>
  <?php
  $fotoPerfil = "fotos_perfil/{$usuarioLogado}.jpg";
  $avatar = file_exists($fotoPerfil) ? $fotoPerfil : "usuario.png";
  ?>
  <img src="<?= $avatar ?>?t=<?= time() ?>" alt="Avatar">

  <div>
    <h3><?= htmlspecialchars($nomeUsuario) ?></h3>
    <p><?= htmlspecialchars(ucfirst($funcaoUsuario)) ?></p>
  </div>
</div>

    </header>

    <main class="content">
      <?php if ($arquivos): ?>
        <?php foreach ($arquivos as $arquivo):
          $mesAnoRaw = preg_replace("/^{$usuarioLogado}-|\.pdf$/i", '', basename($arquivo));
          $dataFormatada = date("d/m/Y", filemtime($arquivo));
        ?>
          <article class="holerite-card" data-nome="<?= strtolower($mesAnoRaw) ?>">
            Holerite <?= htmlspecialchars($dataFormatada) ?>
            <div class="holerite-buttons">
              <button onclick="window.open('<?= htmlspecialchars($arquivo) ?>','_blank')">Abrir</button>
              <a href="<?= htmlspecialchars($arquivo) ?>" download>Baixar</a>
              <button onclick="compartilhar('<?= htmlspecialchars($arquivo) ?>')">Compartilhar</button>
            </div>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Nenhum holerite disponível ainda.</p>
      <?php endif; ?>
    </main>
  </div>

<script>
function compartilhar(path) {
  const url = location.origin + '/' + path;
  if (navigator.share) {
    navigator.share({ title: 'Holerite', text: 'Confira seu holerite:', url })
      .catch(err => alert('Erro ao compartilhar: ' + err));
  } else {
    prompt("Copie o link abaixo para compartilhar:", url);
  }
}

function filtrarCards(filtro) {
  const termos = filtro.toLowerCase();
  document.querySelectorAll('.holerite-card').forEach(card => {
    const nome = card.getAttribute('data-nome');
    card.style.display = nome.includes(termos) ? '' : 'none';
  });
}

function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('active');
}
</script>

</body>
</html>
