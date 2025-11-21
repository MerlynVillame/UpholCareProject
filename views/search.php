<?php require_once "views/header.php"; ?>
<div class="container-fluid px-3">
  <div class="card">
    <div class="card-header "><div class="card-title">Search Results</div></div>
    <div class="card-body">
      <?php 
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        if ($q === '') {
          echo '<p class="text-muted">Type in the search bar to find services, bookings, or materials.</p>';
        } else {
          echo '<p class="mb-3">Showing results for <strong>'.htmlspecialchars($q).'</strong></p>';
          echo '<div class="alert alert-info">This is a placeholder. Hook this up to your database to return real results.</div>';
        }
      ?>
    </div>
  </div>
</div>
<?php require_once "views/footer.php"; ?>



