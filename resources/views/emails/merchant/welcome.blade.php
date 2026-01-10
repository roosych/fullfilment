<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добро пожаловать!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .info-box {
            background-color: white;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .credentials {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
        }
        .credentials strong {
            color: #92400e;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Добро пожаловать в систему!</h1>
    </div>
    
    <div class="content">
        <p>Здравствуйте, <strong>{{ $user->name }}</strong>!</p>
        
        <p>Ваш аккаунт мерчанта был успешно создан. Ниже указаны ваши данные для входа в систему:</p>
        
        <div class="credentials">
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Пароль:</strong> {{ $password }}</p>
        </div>
        
        <div class="info-box">
            <h3>Информация о вашей компании:</h3>
            <p><strong>Название компании:</strong> {{ $merchant->company }}</p>
            @if($merchant->address)
                <p><strong>Адрес:</strong> {{ $merchant->address }}</p>
            @endif
            @if($user->phone)
                <p><strong>Телефон:</strong> {{ $user->phone }}</p>
            @endif
        </div>
        
        <p><strong>Важно:</strong> Сохраните эти данные в безопасном месте. Рекомендуем изменить пароль после первого входа в систему.</p>
        
        <p>Если у вас возникнут вопросы, пожалуйста, свяжитесь с нашей службой поддержки.</p>
        
        <p>С уважением,<br>Команда поддержки</p>
    </div>
    
    <div class="footer">
        <p>Это автоматическое письмо, пожалуйста, не отвечайте на него.</p>
    </div>
</body>
</html>

