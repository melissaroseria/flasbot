<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Bot Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="static/style.css">
</head>
<body class="bg-dark text-light">
  <div class="container p-4">
    <h3 class="mb-4">Bot Yükleme</h3>

    <!-- GitHub Import -->
    <form action="http://127.0.0.1:5000/clone_repo" method="post">
      <input type="text" name="repo_url" class="form-control mb-2" placeholder="GitHub Linki">
      <button type="submit" class="btn btn-success w-100">Klonla</button>
    </form>

    <hr class="text-light">

    <!-- Zip Upload -->
    <form action="http://127.0.0.1:5000/upload_zip" method="post" enctype="multipart/form-data">
      <input type="file" name="zip_file" class="form-control mb-2">
      <button type="submit" class="btn btn-info w-100">Zip Yükle</button>
    </form>
  </div>
</body>
</html>
