<?php
$arquivo = 'noticias.json';
$noticias = file_exists($arquivo) ? json_decode(file_get_contents($arquivo), true) : [];

// Enviar nova notícia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titulo'])) {
    $categoria = $_POST['categoria'];
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $data = date('d/m/Y');

    // Upload da imagem
    $imagemNome = '';
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
        $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $imagemNome = uniqid() . '.' . $extensao;
        move_uploaded_file($_FILES['imagem']['tmp_name'], 'imagens/' . $imagemNome);
    }

    $noticia = [
        'categoria' => $categoria,
        'titulo' => $titulo,
        'descricao' => $descricao,
        'data' => $data,
        'imagem' => $imagemNome
    ];

    array_unshift($noticias, $noticia);
    file_put_contents($arquivo, json_encode($noticias, JSON_PRETTY_PRINT));
    header("Location: enviodenoticias.php");
    exit();
}

// Editar notícia
if (isset($_POST['editar']) && isset($_POST['indice'])) {
    $i = $_POST['indice'];
    $noticias[$i]['titulo'] = $_POST['titulo_edit'];
    $noticias[$i]['descricao'] = $_POST['descricao_edit'];
    file_put_contents($arquivo, json_encode($noticias, JSON_PRETTY_PRINT));
    header("Location: enviodenoticias.php");
    exit();
}

// Excluir notícia
if (isset($_GET['excluir'])) {
    $i = $_GET['excluir'];
    if (!empty($noticias[$i]['imagem'])) {
        @unlink('imagens/' . $noticias[$i]['imagem']);
    }
    array_splice($noticias, $i, 1);
    file_put_contents($arquivo, json_encode($noticias, JSON_PRETTY_PRINT));
    header("Location: enviodenoticias.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Admin - Notícias</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: "Segoe UI", sans-serif;
      background: #f0f4f8;
    }

    header {
      background: linear-gradient(90deg, #04a8ec, #3673c2);
      color: white;
      padding: 15px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .menu-toggle {
      display: none;
      cursor: pointer;
      font-size: 26px;
    }

    .painel {
      display: flex;
      padding: 20px;
      gap: 20px;
    }

    .formulario, .noticias {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      flex: 1;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      background: linear-gradient(90deg, #04a8ec, #3673c2);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    label {
      display: block;
      margin: 10px 0 5px;
      font-weight: 600;
    }

    input[type="text"],
    textarea,
    select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
    }

    textarea {
      resize: vertical;
    }

    input[type="submit"] {
      margin-top: 20px;
      width: 100%;
      padding: 12px;
      background: linear-gradient(90deg, #04a8ec, #3673c2);
      color: white;
      font-size: 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    .noticia {
      border-top: 1px solid #ddd;
      padding: 15px 0;
    }

    .noticia img {
      max-width: 100%;
      border-radius: 6px;
      margin-bottom: 10px;
    }

    .noticia form {
      margin-top: 10px;
    }

    .noticia button {
      margin-right: 10px;
      padding: 6px 12px;
      border: none;
      background: #04a8ec;
      color: white;
      border-radius: 4px;
      cursor: pointer;
    }

    .noticia button.excluir {
      background: #e63946;
    }

    .voltar {
      color: white;
      text-decoration: none;
      font-weight: bold;
    }

    @media (max-width: 768px) {
      .painel {
        flex-direction: column;
      }

      .menu-toggle {
        display: block;
      }

      .voltar {
        font-size: 14px;
      }
    }
  </style>
</head>
<body>
  <header>
    <div><strong>Painel Administrativo</strong></div>
    <a href="admin.php" class="voltar">← Voltar</a>
  </header>

  <div class="painel">
    <div class="formulario">
      <h2>Enviar Notícia</h2>
      <form method="POST" enctype="multipart/form-data">
        <label>Categoria:</label>
        <select name="categoria" required>
          <option value="Oxigenoterapia">Oxigenoterapia</option>
          <option value="Materiais Hospitalares">Materiais Hospitalares</option>
          <option value="Medicina do Trabalho">Medicina do Trabalho</option>
          <option value="Artigos">Artigos</option>
        </select>

        <label>Título:</label>
        <input type="text" name="titulo" required>

        <label>Descrição:</label>
        <textarea name="descricao" rows="5" required></textarea>

        <label>Imagem (opcional):</label>
        <input type="file" name="imagem" accept="image/*">

        <input type="submit" value="Publicar Notícia">
      </form>
    </div>

    <div class="noticias">
      <h2>Notícias Publicadas</h2>
      <?php foreach ($noticias as $i => $n): ?>
        <div class="noticia">
          <?php if (!empty($n['imagem'])): ?>
            <img src="imagens/<?= htmlspecialchars($n['imagem']) ?>" alt="Imagem da Notícia">
          <?php endif; ?>
          <form method="POST">
            <input type="hidden" name="indice" value="<?= $i ?>">
            <label>Título:</label>
            <input type="text" name="titulo_edit" value="<?= htmlspecialchars($n['titulo']) ?>" required>

            <label>Descrição:</label>
            <textarea name="descricao_edit" rows="4" required><?= htmlspecialchars($n['descricao']) ?></textarea>

            <p><strong>Categoria:</strong> <?= htmlspecialchars($n['categoria']) ?> | <strong>Data:</strong> <?= $n['data'] ?></p>
            <button type="submit" name="editar">Salvar Edição</button>
            <a href="?excluir=<?= $i ?>"><button type="button" class="excluir">Excluir</button></a>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>
