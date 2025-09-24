<?php
$arquivo = 'noticias.json';
$noticias = file_exists($arquivo) ? json_decode(file_get_contents($arquivo), true) : [];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Notícias</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
      margin: 0;
      font-family: "Segoe UI", sans-serif;
      background-image: url('noticia2.png');
      padding: 30px 15px;
    }

    .container {
      max-width: 900px;
      margin: 0 auto;
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      font-size: 30px;
      background: white;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .noticia {
      background: white;
      padding: 20px;
      margin-bottom: 20px;
      border-radius: 10px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.05);
      transition: transform 0.2s ease;
    }

    .noticia:hover {
      transform: translateY(-3px);
    }

    .noticia img {
      max-width: 100%;
      height: auto;
      border-radius: 8px;
      margin-bottom: 15px;
      display: block;
    }

    .categoria {
      font-size: 14px;
      font-weight: 600;
      color: #3673c2;
      margin-bottom: 8px;
    }

    .titulo {
      font-size: 22px;
      color: #333;
      margin-bottom: 8px;
    }

    .descricao {
      font-size: 16px;
      color: #555;
      margin-bottom: 12px;
    }

    .data {
      font-size: 14px;
      color: #999;
      text-align: right;
    }

    @media (max-width: 600px) {
      .titulo {
        font-size: 20px;
      }

      .descricao {
        font-size: 15px;
      }

      .data {
        font-size: 13px;
      }
    }
  </style>
</head>
<body>
  <div class="container">

  <body>
  <div class="container">
    <center><a href="tuise/inicio.html" style="
      display: inline-block;
      margin-bottom: 20px;
      padding: 10px 20px;
      background: linear-gradient(90deg, #3f9aa3, #3b749e);
      color: white;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
      transition: background 0.3s ease; ">
      Página inicial
    </a>
    <a href="tuise/index.html" style="
      display: inline-block;
      margin-bottom: 20px;
      padding: 10px 20px;
      background: linear-gradient(90deg, #3f9aa3, #3b749e);
      color: white;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
      transition: background 0.3s ease; ">
      Home
    </a></center>
    <h2>Últimas Notícias</h2>

    <?php if (empty($noticias)): ?>
      <p style="text-align:center; color:#777;">Nenhuma notícia disponível.</p>
    <?php else: ?>
      <?php foreach ($noticias as $noticia): ?>
        <div class="noticia">
          <?php
          $imagem = isset($noticia['imagem']) ? $noticia['imagem'] : '';
          if (!empty($imagem)) {
          echo '<img src="' . htmlspecialchars($imagem) . '" alt="Imagem da notícia">';
          }
          ?>

          <div class="categoria"><?= htmlspecialchars($noticia['categoria']) ?></div>
          <div class="titulo"><?= htmlspecialchars($noticia['titulo']) ?></div>
          <div class="descricao"><?= nl2br(htmlspecialchars($noticia['descricao'])) ?></div>
          <div class="data"><?= htmlspecialchars($noticia['data']) ?></div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</body>
</html>
