<?php
session_start();
include('../includes/config.php');

//if (empty($_SESSION['alogin'])) {
  //  header('location:index.php');
    //exit();
//}

$msg = $error = "";

// Handle approve/reject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id'])) {
    $id = (int)$_POST['id'];
    $action = $_POST['action'];

    try {
        if ($action === 'approve') {
            $sql = "UPDATE tbltestimonial SET status=1 WHERE id=:id";
        } elseif ($action === 'reject') {
            $sql = "UPDATE tbltestimonial SET status=0 WHERE id=:id";
        }
        $query = $dbh->prepare($sql);
        $query->execute([':id'=>$id]);
        $msg = "✅ Testimonial updated successfully.";
    } catch (Exception $e) {
        $error = "⚠️ Error updating testimonial.";
    }
}

// Fetch testimonials
$testimonials = $dbh->query("SELECT * FROM tbltestimonial ORDER BY PostingDate DESC")->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Testimonials | Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include('../includes/header.php'); ?>
<div class="container py-5">
  <h2 class="mb-4">Manage Testimonials</h2>

  <?php if($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php elseif($msg): ?>
    <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-header">Testimonials List</div>
    <div class="card-body">
      <table class="table table-striped table-bordered align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>User Email</th>
            <th>Testimonial</th>
            <th>Posting Date</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php $cnt=1; foreach($testimonials as $t): ?>
            <tr>
              <td><?= $cnt++ ?></td>
              <td><?= htmlspecialchars($t->UserEmail) ?></td>
              <td><?= htmlspecialchars($t->Testimonial) ?></td>
              <td><?= htmlspecialchars($t->PostingDate) ?></td>
              <td>
                <?php if ($t->status == 1): ?>
                  <span class="badge bg-success">Approved</span>
                <?php else: ?>
                  <span class="badge bg-warning text-dark">Pending</span>
                <?php endif; ?>
              </td>
              <td>
                <form method="post" class="d-inline">
                  <input type="hidden" name="id" value="<?= $t->id ?>">
                  <button type="submit" name="action" value="approve" class="btn btn-sm btn-success">Approve</button>
                  <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger">Reject</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
