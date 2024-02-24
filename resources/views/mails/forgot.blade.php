<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialisation de votre mot de passe</title>
</head>
<body>
    <h1>Réinitialisez votre mot de passe</h1>

    <p>Bonjour,</p>

    <p>Vous recevez cet email car vous avez demandé la réinitialisation de votre mot de passe pour votre compte sur notre site.</p>

    <p>Pour réinitialiser votre mot de passe, cliquez sur le lien ci-dessous.</p>

    <a href="{{ url('api/password.reset', $token) }}">Réinitialiser mon mot de passe</a>

    <p>Ce lien expirera dans  60 minutes.</p>

    <p>Si vous n'avez pas demandé la réinitialisation de votre mot de passe, veuillez ignorer cet email.</p>

    <p>Cordialement,</p>

    <p>L'équipe de notre site</p>
</body>
</html>
