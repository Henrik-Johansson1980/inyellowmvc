<?php require APP_ROOT . '/views/inc/header.php'; ?>
<div class="jumbotron jumbotron-fluid text-center">
  <div class="container">
    <h1 class="display-3">
      <?php echo $data['title']; ?>
      <p class="lead"><?php echo $data['description'];?></p>
    </h1>
  </div>
</div>

<?php require APP_ROOT . '/views/inc/footer.php'; ?>