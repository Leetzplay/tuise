<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: login.html");
    exit;
}

$usuarios = json_decode(file_get_contents("usuarios.json"), true);

// Filtros
$filtroRole = $_GET['role'] ?? '';
$filtroNome = strtolower(trim($_GET['nome'] ?? ''));
$filtroContrato = strtolower(trim($_GET['contrato'] ?? ''));
$filtroPlantao = strtolower(trim($_GET['plantao'] ?? ''));

// Lista única de funções
$funcoes = array_unique(array_filter(array_map(function($u) {
    return $u['role'] ?? '';
}, $usuarios)));
sort($funcoes);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Painel Admin - Tuise</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
      background: #f9f9f9;
    }

    h2 {
      color: #5A6B9E;
      text-align: center;
    }

    .filtro-form {
      text-align: center;
      margin-bottom: 20px;
    }

    .filtro-form select,
    .filtro-form input[type="text"],
    .filtro-form button {
      padding: 8px;
      font-size: 1em;
      border-radius: 5px;
      border: 1px solid #ccc;
      margin: 5px;
    }

    .acoes {
      margin-top: 15px;
    }

    .botao-noticia,
    .botao-sair {
      display: inline-block;
      padding: 10px 20px;
      background: linear-gradient(to right, #00a1ff, #0083cc);
      color: white;
      text-decoration: none;
      border-radius: 8px;
      margin: 10px;
      border: none;
      cursor: pointer;
      font-size: 1em;
    }

    .botao-noticia:hover,
    .botao-sair:hover {
      background: linear-gradient(to right, #0083cc, #006db3);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th, td {
      border: 1px solid #ccc;
      padding: 10px;
      text-align: left;
    }

    th {
      background-color: #7AA8D5;
      color: white;
    }

    form {
      margin: 0;
    }

    input[type="file"], select {
      padding: 5px;
    }

    button {
      padding: 8px 16px;
      background-color: #5A6B9E;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 1em;
    }

    button:hover {
      background-color: #4972b7;
    }

    @media (max-width: 768px) {
      table, thead, tbody, th, td, tr {
        display: block;
        width: 100%;
      }

      thead {
        display: none;
      }

      tr {
        margin-bottom: 15px;
        background: white;
        border: 1px solid #ccc;
        border-radius: 10px;
        padding: 5px;
      }

      td {
        border: none;
        padding: 5px;
        position: relative;
      }

      td::before {
        content: attr(data-label);
        font-weight: bold;
        color: #5A6B9E;
        display: block;
        margin-bottom: 5px;
      }

      .filtro-form input,
      .filtro-form select,
      .filtro-form button {
        width: 90%;
        margin: 8px auto;
      }

      button,
      .botao-noticia,
      .botao-sair {
        width: 50%;
      }

      input[type="file"] {
        width: 90%;
        margin-top: 5px;
      }
    }
  </style>
</head>
<body>
  <h2>Painel Admin - Usuários Cadastrados</h2>

  <form class="filtro-form" method="GET">
    <label for="role">Filtrar por função:</label>
    <select name="role" id="role">
      <option value="">Todos</option>
      <?php foreach ($funcoes as $funcao): ?>
        <option value="<?= $funcao ?>" <?= $filtroRole === $funcao ? 'selected' : '' ?>>
          <?= ucfirst($funcao) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label for="nome">Buscar por nome:</label>
    <input type="text" name="nome" id="nome" placeholder="Digite o nome" value="<?= htmlspecialchars($_GET['nome'] ?? '') ?>">

    <label for="contrato">Filtrar por contrato:</label>
    <input type="text" name="contrato" id="contrato" placeholder="Digite o contrato" value="<?= htmlspecialchars($_GET['contrato'] ?? '') ?>">

    <label for="plantao">Filtrar por plantão:</label>
    <input type="text" name="plantao" id="planto" placeholder="Digite o plantão" value="<?= htmlspecialchars($_GET['plantao'] ?? '') ?>">

    <button type="submit">Aplicar Filtro</button>

    <div class="acoes">
      <a href="enviodenoticias.php" class="botao-noticia">Enviar Notícia</a>
      
      <a href="logout.php" class="botao-noticia" style="background: linear-gradient(to right, #ff5e5e, #cc0000); margin-left: 10px;">Sair</a>

    </div>
  </form>

  <table>
    <thead>
      <tr>
        <th>Nome</th>
        <th>Usuário</th>
        <th>Email</th>
        <th>Função</th>
        <th>Contrato</th>
        <th>Plantão</th>
        <th>Enviar Holerite</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($usuarios as $user): ?>
        <?php
          $condicaoRole = !$filtroRole || ($user['role'] ?? '') === $filtroRole;
          $condicaoNome = !$filtroNome || stripos($user['nome'], $filtroNome) !== false;
          $condicaoContrato = !$filtroContrato || stripos($user['contrato'] ?? '', $filtroContrato) !== false;
          $condicaoPlantao = !$filtroPlantao || strtolower($user['plantao'] ?? '') === $filtroPlantao;

          if ($condicaoRole && $condicaoNome && $condicaoContrato && $condicaoPlantao):
        ?>
          <tr>
            <td data-label="Nome"><?= htmlspecialchars($user['nome']) ?></td>
            <td data-label="Usuário"><?= htmlspecialchars($user['usuario']) ?></td>
            <td data-label="Email"><?= htmlspecialchars($user['email']) ?></td>
            <td data-label="Função"><?= htmlspecialchars($user['role']) ?></td>
            <td data-label="Contrato"><?= htmlspecialchars($user['contrato'] ?? '-') ?></td>
            <td data-label="Plantão"><?= htmlspecialchars($user['plantao'] ?? '-') ?></td>
            <td data-label="Holerite">
              <form action="upload.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="usuario" value="<?= htmlspecialchars($user['usuario']) ?>">
                <input type="file" name="holerite" accept="application/pdf" required>
                <button type="submit">Enviar</button>
              </form>
            </td>
          </tr>
        <?php endif; ?>
      <?php endforeach; ?>
    </tbody>
  </table>

  <?php
  if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    echo "<script>alert('Sessão encerrada com sucesso!'); window.location.href='login.html';</script>";
    exit;
  }
  ?>
</body>
</html>
