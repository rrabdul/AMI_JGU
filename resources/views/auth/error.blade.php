
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
body {
   
font-family: Arial, sans-serif;
background-color: #f7f7f7;
display: flex;
flex-direction: column;
align-items: center;
justify-content: center;
height: 100vh;
margin: 0;
}

.forgot-password-page {
    text-align: center;
}

.logo {
    margin-bottom: 21px; /* Jarak antara logo dan form */
    animation: float 3s ease-in-out infinite;
}

.logo img {
    width: 335px;
    height: 110px;
    margin-left: -20px; /* Geser ke kiri dengan nilai negatif */
}
/* Animasi naik turun */
@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

.forgot-password-container {
    background-color: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    width: 400px;
    text-align: center;
}

h2 {
    margin-bottom: 10px;
    font-size: 24px;
}

p {
    color: #888;
    margin-bottom: 20px;
}

.input-group {
    margin-bottom: 15px;
    text-align: left;
}

.input-group label {
    display: block;
    margin-bottom: 5px;
    font-size: 14px;
}

.input-group input {
    width: 100%;
    padding: 10px;
    box-sizing: border-box;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.error {
    color: red;
    font-size: 12px;
    margin-top: 5px;
    display: block;
}

button {
    width: 100%;
    padding: 10px;
    background-color: #e50914;
    color: #fff;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    margin-top: 10px;
}

button:hover {
    background-color: #d40813;
}

    </style>
</head>
<body>
    <div class="forgot-password-page">
        <div class="logo">
            <a href="{{ route('home') }}"><img src="{{ asset('../assets-landing/img/ami-jgu.png') }}" alt="Logo" /></a>
        </div>
        <div class="forgot-password-container">
            <div class="login-main text-danger text-center">
                @if(isset($msg))
                {!! $msg !!}
                @else
                <h1>ERROR!</h1>
                @endif
            </div>
        </div>

    </div>
</body>
</html>