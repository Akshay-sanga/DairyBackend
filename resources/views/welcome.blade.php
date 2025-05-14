<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Dairy Software</title>
        <link rel="icon" type="image/png" href="favicon.jpg">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <style>
    body {
      margin: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(135deg, #f9f9f9, #e0e0e0);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .card {
      background: white;
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      text-align: center;
      max-width: 400px;
      width: 90%;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
      transform: translateY(-10px);
      box-shadow: 0 12px 30px rgba(0,0,0,0.3);
    }

    .card h1 {
      margin-bottom: 20px;
      font-size: 28px;
      color: #2e7d32;
    }

    .card p {
      font-size: 14px;
      color: #555;
      margin-top: 20px;
    }
  </style>
    </head>
    <body >
    <div class="card">
    <h1>सरस डेयरी में आपका स्वागत है</h1>
    <p>&copy; 2025 Designed & Developed by Akshay & Shivam & Sunil</p>
  </div>
    </body>
</html>
