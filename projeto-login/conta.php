<?php
session_start();

$usuarioLogado = file_exists("usuario_logado.txt") ? file_get_contents("usuario_logado.txt") : '';
if (!$usuarioLogado) {
    header("Location: login.html");
    exit;
}

$usuariosJson = file_exists("usuarios.json") ? file_get_contents("usuarios.json") : '[]';
$usuariosArray = json_decode($usuariosJson, true);

$indiceUsuario = -1;
foreach ($usuariosArray as $i => $usuario) {
    if ($usuario['usuario'] === $usuarioLogado) {
        $indiceUsuario = $i;
        break;
    }
}

if ($indiceUsuario === -1) {
    echo "Usuário não encontrado.";
    exit;
}

$usuarioAtual = $usuariosArray[$indiceUsuario];
$fotoPath = "fotos_perfil/{$usuarioLogado}.jpg";

// Upload da foto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto'])) {
    $diretorio = "fotos_perfil";
    if (!file_exists($diretorio)) {
        mkdir($diretorio, 0777, true);
    }
    $arquivoTemp = $_FILES['foto']['tmp_name'];
    move_uploaded_file($arquivoTemp, $fotoPath);
    header("Location: conta.php");
    exit;
}

// Atualização de dados
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar'])) {
    $usuariosArray[$indiceUsuario]['nome'] = $_POST['nome'];
    $usuariosArray[$indiceUsuario]['email'] = $_POST['email'];
    $usuariosArray[$indiceUsuario]['usuario'] = $_POST['usuario'];
    $usuariosArray[$indiceUsuario]['matricula'] = $_POST['matricula'];
    $usuariosArray[$indiceUsuario]['role'] = $_POST['role'];
    $usuariosArray[$indiceUsuario]['contrato'] = $_POST['contrato'];
    $usuariosArray[$indiceUsuario]['plantao'] = $_POST['plantao'];

    file_put_contents("usuarios.json", json_encode($usuariosArray, JSON_PRETTY_PRINT));

    if ($_POST['usuario'] !== $usuarioLogado) {
        file_put_contents("usuario_logado.txt", $_POST['usuario']);
        header("Location: conta.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Minha Conta</title>
  <style>
* {
  box-sizing: border-box;
}

body {
  font-family: Arial, sans-serif;
  background: #f4f4f4;
  padding: 20px;
  margin: 0;
}

h1 {
  text-align: center;
  margin-bottom: 10px;
  font-weight: bold;
  background: linear-gradient(90deg, #04a8ec, #3673c2);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

p {
  text-align: center;
  color: #555;
  margin-bottom: 30px;
}

.container {
  display: flex;
  justify-content: center;
  gap: 40px;
  flex-wrap: wrap;
  max-width: 1000px;
  margin: 0 auto;
}

.greeting {
  text-align: center;
  margin-bottom: 30px;
  font-size: 18px;
  font-weight: bold;
  color: black;
}

.username-gradient {
  background: linear-gradient(90deg, #04a8ec, #3673c2);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.left, .right {
  flex: 1 1 45%;
  background: #fff;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  min-width: 300px;
}

.left {
  text-align: center;
}

.left img {
  width: 180px;
  height: 180px;
  border-radius: 50%;
  object-fit: cover;
  border: none;
  margin-bottom: 20px;
}

.left label {
  font-weight: bold;
  margin-bottom: 10px;
  display: block;
}

.left input[type="file"] {
  margin-bottom: 10px;
}

.left button {
  background: linear-gradient(90deg, #04a8ec, #3673c2);
  color: white;
  padding: 8px 15px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.left button:hover {
  opacity: 0.9;
}

.right form {
  display: flex;
  flex-direction: column;
}

.right label {
  font-weight: bold;
  margin-top: 10px;
}

.right input[type="text"],
.right input[type="email"] {
  padding: 8px;
  border-radius: 5px;
  border: 1px solid #ccc;
  margin-top: 5px;
}

.right button {
  margin-top: 20px;
  padding: 10px;
  background: linear-gradient(90deg, #04a8ec, #3673c2);
  color: white;
  border: none;
  border-radius: 5px;
  font-weight: bold;
  cursor: pointer;
}

.right button:hover {
  opacity: 0.9;
}

.voltar {
  margin-top: 30px;
  text-align: center;
}

.voltar a {
  padding: 10px 20px;
  background: linear-gradient(90deg, #04a8ec, #3673c2);
  color: white;
  border-radius: 5px;
  font-weight: bold;
  text-decoration: none;
  display: inline-block;
}

.voltar a:hover {
  opacity: 0.9;
}

@media (max-width: 768px) {
  .container {
    flex-direction: column;
    align-items: center;
  }

  .left, .right {
    width: 100%;
    max-width: 500px;
  }

  /* Ajustes específicos para a versão mobile */
  .left img {
    width: 50px;
    height: 50px;
  }

  .left input[type="file"] {
    width: 50%;
    font-size: 14px;
  }

  .left button {
    padding: 6px 12px;
    font-size: 14px;
  }
}

  </style>
</head>
<body>

  <h1>Minha Conta</h1>
  <p class="greeting">
  Olá, <span class="username-gradient"><?= htmlspecialchars($usuarioAtual['nome']) ?></span>! Atualize sua foto de perfil ou seus dados abaixo:
</p>

  <div class="container">
    <!-- Foto de Perfil -->
    <div class="left">
      <?php if (file_exists($fotoPath)): ?>
        <img src="<?= $fotoPath ?>?t=<?= time() ?>" alt="Foto de Perfil">
      <?php else: ?>
        <img src="usuario.png" alt="Foto de Perfil Padrão">
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data">
        <label>Atualizar Foto de Perfil:</label>
        <input type="file" name="foto" accept="image/*" required>
        <button type="submit">Enviar Foto</button>
      </form>
    </div>

    <!-- Formulário de Dados -->
    <div class="right">
      <form method="POST">
        <input type="hidden" name="atualizar" value="1">
        
        <label>Nome:</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($usuarioAtual['nome']) ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($usuarioAtual['email']) ?>" required>

        <label>Usuário:</label>
        <input type="text" name="usuario" value="<?= htmlspecialchars($usuarioAtual['usuario']) ?>" required>

        <label>Matrícula:</label>
        <input type="text" name="matricula" value="<?= htmlspecialchars($usuarioAtual['matricula']) ?>">

        <label>Função:</label>
        <input type="text" name="role" value="<?= htmlspecialchars($usuarioAtual['role']) ?>">

        <label>Contrato:</label>
        <input type="text" name="contrato" value="<?= htmlspecialchars($usuarioAtual['contrato'] ?? '') ?>">

        <label>Plantão:</label>
        <input type="text" name="plantao" value="<?= htmlspecialchars($usuarioAtual['plantao'] ?? '') ?>">

        <button type="submit">Salvar Alterações</button>
      </form>
    </div>
  </div>

  <div class="voltar">
    <a href="usuario.php">← Voltar</a>
  </div>

</body>
</html>
