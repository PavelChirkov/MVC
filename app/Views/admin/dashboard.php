<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ панель</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f2f5; margin: 0; }
        .header { background: #1877f2; color: white; padding: 15px; }
        .container { max-width: 1200px; margin: 20px auto; padding: 0 20px; }
        .dashboard { background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .welcome { font-size: 24px; margin-bottom: 20px; }
        .nav { margin-top: 20px; }
        .nav a { display: inline-block; margin-right: 10px; padding: 10px 20px; background: #1877f2; color: white; text-decoration: none; border-radius: 4px; }
        .nav a:hover { background: #166fe5; }
    </style>
</head>
<body>
<div class="header">
    <h1>Административная панель</h1>
</div>

<div class="container">
    <div class="dashboard">
        <div class="welcome">
            Добро пожаловать, <?= htmlspecialchars($user_name) ?>!
        </div>

        <div class="content">
            <p>Это защищенная страница администратора. Доступ только для авторизованных пользователей.</p>
            <p>Здесь вы можете разместить любую информацию для администраторов.</p>
        </div>

        <div class="nav">
            <a href="/logout">Выйти</a>
        </div>
    </div>
</div>
</body>
</html>