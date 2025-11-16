<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $title ?? 'CLI Dungeon' ?></title>

  <link rel="stylesheet" href="assets/css/base.css">

  <?php if (!empty($extraCss)): ?>
    <link rel="stylesheet" href="/assets/css/<?= htmlspecialchars($extraCss) ?>">
  <?php endif; ?>
  <?php if(!empty($script)):?>
    <script  src="/scripts/<?= htmlspecialchars($script)?>"></script>
    <?php endif; ?>
</head> 
<body>
</body>
</html>