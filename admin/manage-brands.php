<?php
session_start();
include('../includes/config.php');

if (empty($_SESSION['alogin'])) {
    header('location:index.php');
    exit();
}

$msg = $error = "";

// Handle brand creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_brand'])) {
    $brand = trim($_POST['brand']);
    if ($brand === "") {
        $error = "Brand name cannot be empty.";
    } else {
        try {
            $sql = "INSERT INTO tblbrands (BrandName) VALUES (:brand)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':brand', $brand, PDO::PARAM_STR);
            $query->execute();
            $msg = "✅ Brand created successfully.";
        } catch (Exception $e) {
            $error = "⚠️ Something went wrong. Please try again.";
        }
    }
}

// Handle brand deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $dbh->prepare("DELETE FROM tblbrands WHERE id=:id")->execute([':id'=>$id]);
    $msg = "🗑️ Brand deleted.";
}

// Handle brand update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_brand'])) {
    $id = (int)$_POST['id'];
    $brand = trim($_POST['brand']);
    if ($brand !== "") {
        $sql = "UPDATE tblbrands SET BrandName=:brand WHERE id=:id";
        $query = $dbh->prepare($sql);
        $query->execute([':brand'=>$brand, ':id'=>$id]);
        $msg = "✏️ Brand updated.";
    }
}

// Fetch existing brands
$brands = $dbh->query("SELECT * FROM tblbrands ORDER BY id DESC")->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Brands | Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include('includes/header.php'); ?>
<div class="container py-5">
  <h2 class="mb-4">Manage Brands</h2>

  <?php if($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php elseif($msg): ?>
    <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <!-- Create Brand Form -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header">Add New Brand</div>
    <div class="card-body">
      <form method="post" class="row g-3">
        <div class="col-md-8">
          <input type="text" name="brand" class="form-control" placeholder="Enter brand name" required>
        </div>
        <div class="col-md-4">
          <button type="submit" name="add_brand" class="btn btn-primary w-100">Create Brand</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Existing Brands Table -->
  <div class="card shadow-sm">
    <div class="card-header">Existing Brands</div>
    <div class="card-body">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Brand Name</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($brands as $b): ?>
            <tr>
              <td><?= $b->id ?></td>
              <td><?= htmlspecialchars($b->BrandName) ?></td>
              <td>
                <!-- Edit form inline -->
                <form method="post" class="d-inline">
                  <input type="hidden" name="id" value="<?= $b->id ?>">
                  <input type="text" name="brand" value="<?= htmlspecialchars($b->BrandName) ?>" class="form-control d-inline w-auto">
                  <button type="submit" name="edit_brand" class="btn btn-sm btn-warning">Edit</button>
                </form>
                <a href="?delete=<?= $b->id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this brand?')">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

</body>
</html>
