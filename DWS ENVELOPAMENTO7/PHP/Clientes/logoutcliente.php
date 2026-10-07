<?php
// =============================================
// logoutcliente.php - Logout do Cliente
// =============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Saindo - DWS</title>
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'Space Grotesk', sans-serif;
    background: #050505;
    background-image:
        radial-gradient(ellipse at top left, rgba(242, 53, 53, 0.1) 0%, transparent 50%),
        radial-gradient(ellipse at bottom right, rgba(242, 53, 53, 0.08) 0%, transparent 50%);
    background-attachment: fixed;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 20px;
    color: #fff;
}
.box {
    background: linear-gradient(145deg, rgba(20,20,20,0.95), rgba(10,10,10,0.98));
    padding: 50px 40px;
    border-radius: 28px;
    text-align: center;
    max-width: 420px;
    width: 100%;
    position: relative;
    animation: fadeIn 0.5s ease;
}
.box::before {
    content: '';
    position: absolute;
    inset: -2px;
    border-radius: 28px;
    padding: 2px;
    background: linear-gradient(var(--angle, 0deg), transparent, #f23535, #ff6b6b, #f23535, transparent);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    animation: ledRotate 4s linear infinite;
    pointer-events: none;
}
@keyframes ledRotate { 0% { --angle: 0deg; } 100% { --angle: 360deg; } }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
.icon {
    width: 90px;
    height: 90px;
    background: linear-gradient(135deg, #f23535, #c91f2c);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 25px;
    font-size: 2.5rem;
    color: #fff;
    box-shadow: 0 15px 40px rgba(242,53,53,0.4);
}
h1 {
    font-family: 'Orbitron', sans-serif;
    color: #fff;
    font-size: 1.5rem;
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: -0.5px;
}
p { color: #888; font-size: 0.95rem; margin-bottom: 8px; }
.loader {
    width: 100%;
    height: 4px;
    background: rgba(255,255,255,0.05);
    border-radius: 10px;
    margin: 25px auto 0;
    overflow: hidden;
}
.loader span {
    display: block;
    height: 100%;
    width: 100%;
    background: linear-gradient(90deg, #f23535, #ff6b6b);
    animation: loading 2s forwards;
}
@keyframes loading { 0% { width: 0; } 100% { width: 100%; } }
.btn-voltar {
    display: inline-block;
    margin-top: 25px;
    padding: 12px 30px;
    background: rgba(242,53,53,0.1);
    border: 1px solid rgba(242,53,53,0.3);
    color: #f23535;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    transition: 0.3s;
    font-size: 0.9rem;
}
.btn-voltar:hover { background: rgba(242,53,53,0.2); transform: translateY(-2px); }
</style>
</head>
<body>
    <div class="box">
        <div class="icon">👋</div>
        <h1>ATÉ LOGO!</h1>
        <p>Você saiu da sua conta com sucesso.</p>
        <p style="font-size: 0.8rem; color: #555;">Redirecionando...</p>
        <div class="loader"><span></span></div>
        <a href="../../telas/Cliente/principal.html" class="btn-voltar">Voltar agora</a>
    </div>

    <script>
        try {
            sessionStorage.removeItem('dws_sessao_cliente');
            localStorage.removeItem('fotoPerfilDWS');
        } catch (e) {}

        const box = document.querySelector('.box');
        let angle = 0;
        function animar() {
            angle = (angle + 1.5) % 360;
            box.style.setProperty('--angle', angle + 'deg');
            requestAnimationFrame(animar);
        }
        animar();

        setTimeout(() => {
            window.location.href = '../../telas/Cliente/principal.html';
        }, 1800);
    </script>
</body>
</html>
<?php exit; ?>