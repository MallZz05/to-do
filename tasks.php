<?php
include 'db.php';

$list_id = $_GET['list_id'] ?? 0;
$list_id = intval($list_id);

$list = mysqli_query($conn, "SELECT * FROM lists WHERE id=$list_id");
if (mysqli_num_rows($list) == 0) {
    die("List tidak ditemukan.");
}

if (isset($_POST['add_task'])) {
    $title = trim($_POST['title']);
    $priority = $_POST['priority'];
    $due = $_POST['due'] ?? null;
    if ($title !== '') {
        $query = "INSERT INTO tasks (list_id, title, priority, due_date) VALUES ($list_id, '$title', '$priority', '$due')";
        mysqli_query($conn, $query);
        header("Location: tasks.php?list_id=$list_id");
        exit;
    }
}

if (isset($_GET['toggle'])) {
    $task_id = intval($_GET['toggle']);
    mysqli_query($conn, "UPDATE tasks SET status = IF(status='done','pending','done') WHERE id=$task_id");
    header("Location: tasks.php?list_id=$list_id");
    exit;
}

if (isset($_GET['delete'])) {
    $task_id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM tasks WHERE id=$task_id");
    header("Location: tasks.php?list_id=$list_id");
    exit;
}

$list_data = mysqli_fetch_assoc($list);
$tasks = mysqli_query($conn, "SELECT * FROM tasks WHERE list_id=$list_id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>To-Do List - <?= htmlspecialchars($list_data['name']) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><?= htmlspecialchars($list_data['name']) ?></h4>
                    <a href="index.php" class="btn btn-sm btn-light">Kembali</a>
                </div>
                <div class="card-body">
                    <form method="post" class="row g-2 mb-4">
                        <div class="col-md-5">
                            <input type="text" name="title" class="form-control" placeholder="Nama tugas..." required>
                        </div>
                        <div class="col-md-3">
                            <select name="priority" class="form-select">
                                <option value="Normal">Normal</option>
                                <option value="High">High</option>
                                <option value="Low">Low</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="due" class="form-control">
                        </div>
                        <div class="col-md-1">
                            <button type="submit" name="add_task" class="btn btn-success w-100">+</button>
                        </div>
                    </form>
                    <?php if (mysqli_num_rows($tasks) == 0): ?>
                        <div class="alert alert-info">Belum ada tugas di list ini.</div>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php while ($t = mysqli_fetch_assoc($tasks)): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="?list_id=<?= $list_id ?>&toggle=<?= $t['id'] ?>" class="me-2">
                                            <?= $t['status'] == 'done' ? '✅' : '⬜' ?>
                                        </a>
                                        <strong class="<?= $t['status']=='done'?'text-decoration-line-through text-muted':'' ?>">
                                            <?= htmlspecialchars($t['title']) ?>
                                        </strong>
                                        <small class="text-muted d-block">
                                            Prioritas: <?= $t['priority'] ?> 
                                            <?= $t['due_date'] ? '• Jatuh tempo: '.$t['due_date'] : '' ?>
                                        </small>
                                    </div>
                                    <a href="?list_id=<?= $list_id ?>&delete=<?= $t['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus tugas ini?')">🗑️</a>
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
