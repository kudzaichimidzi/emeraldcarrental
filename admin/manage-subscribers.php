<?php
session_start();
include('../includes/config.php');

if (empty($_SESSION['alogin'])) {
    header('location:index.php');
    exit();
}

$msg = $error = "";

// Handle delete subscriber
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    try {
        $sql = "DELETE FROM tblsubscribers WHERE id=:id";
        $query = $dbh->prepare($sql);
        $query->execute([':id'=>$id]);
        $msg = "🗑️ Subscriber deleted successfully.";
    } catch (Exception $e) {
        $error = "⚠️ Error deleting subscriber.";
    }
}

// Fetch subscribers
$subscribers = $dbh->query("SELECT * FROM tblsubscribers ORDER BY id DESC")->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Subscribers | Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include('includes/header.php'); ?>
<div class="container py-5">
  <h2 class="mb-4">Manage Subscribers</h2>

  <?php if($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php elseif($msg): ?>
    <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-header">Subscribers List</div>
    <div class="card-body">
      <table id="subTable" class="table table-striped table-bordered align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>Email</th>
            <th>Subscription Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php $cnt=1; foreach($subscribers as $s): ?>
            <tr>
              <td><?= $cnt++ ?></td>
              <td><?= htmlspecialchars($s->SubscriberEmail) ?></td>
              <td><?= htmlspecialchars($s->PostingDate) ?></td>
              <td>
                <form method="post" class="d-inline" onsubmit="return confirm('Delete this subscriber?');">
                  <input type="hidden" name="delete_id" value="<?= $s->id ?>">
                  <button type="submit" class="btn btn-sm btn-danger">
                    <i class="bi bi-trash"></i> Delete
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
  $('#subTable').DataTable({
    pageLength: 10,
    lengthChange: false,
    searching: true,
    ordering: true
  });
});
</script>
</body>
</html>
