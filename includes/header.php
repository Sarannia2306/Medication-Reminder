<?php
// Shared header and navigation include
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MediTrack</title>
  <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  
  <script src="script.js?v=<?php echo time(); ?>"></script>
</head>
<body>
  <a class="skip-link" href="#main">Skip to content</a>
  <header class="app-header">
    <div class="brand">
      <span class="brand-icon" aria-hidden="true">💊</span>
      <span class="brand-text">MediTrack</span>
    </div>
    <div class="header-actions">
      <a href="caregiver_profile.php" class="icon-btn" aria-label="Profile" title="Profile">👤</a>
    </div>
  </header>
  <nav class="top-nav" aria-label="Main">
    <a href="index.php" class="nav-link" data-nav>
      <span aria-hidden="true">🏠</span>
      <span>Home</span>
    </a>
    <a href="patients.php" class="nav-link" data-nav>
      <span aria-hidden="true">🧑‍🤝‍🧑</span>
      <span>Patients</span>
    </a>
    <a href="add_medication.php" class="nav-link" data-nav>
      <span aria-hidden="true">➕</span>
      <span>Add</span>
    </a>
    <a href="medication_list.php" class="nav-link" data-nav>
      <span aria-hidden="true">📋</span>
      <span>List</span>
    </a>
    <a href="caregiver_dashboard.php" class="nav-link" data-nav>
      <span aria-hidden="true">👨‍⚕️</span>
      <span>Caregiver</span>
    </a>
    <a href="settings.php" class="nav-link" data-nav>
      <span aria-hidden="true">⚙️</span>
      <span>Settings</span>
    </a>
  </nav>
  <main id="main" class="container">
