<?php
include 'db.php';

if (isset($_POST['add_list'])) {
    $name = trim($_POST['name']);
    if ($name !== '') {
        mysqli_query($conn, "INSERT INTO lists (name) VALUES ('$name')");
        header("Location: index.php");
        exit;
    }
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM lists WHERE id=$id");
    header("Location: index.php");
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM lists ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>To-Do List - Daftar</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">📋 Daftar To-Do List</h4>
                </div>
                <div class="card-body">
                    <form method="post" class="input-group mb-3">
                        <input type="text" name="name" class="form-control" placeholder="Buat list baru..." required>
                        <button type="submit" name="add_list" class="btn btn-success">Tambah</button>
                    </form>
                    <?php if (mysqli_num_rows($result) == 0): ?>
                        <div class="alert alert-info">Belum ada list dibuat.</div>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="tasks.php?list_id=<?= $row['id'] ?>" class="text-decoration-none"><?= htmlspecialchars($row['name']) ?></a>
                                    <div>
                                        <a href="tasks.php?list_id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">Lihat</a>
                                        <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus list ini?')">Hapus</a>
                                    </div>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
