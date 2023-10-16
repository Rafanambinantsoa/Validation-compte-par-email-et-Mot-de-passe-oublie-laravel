<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome Email</title>
  <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
  <div class="w-full bg-gray-50 shadow-md p-4">
    <div class="flex justify-between items-center">
      <h1 class="text-xl font-bold">Welcome to Ready Player Me</h1>
      <img src="https://readyplayer.me/assets/img/logo.svg" alt="Ready Player Me logo" class="w-16 h-16 rounded-full">
    </div>
    <!-- text pour montrer le code envyoe  -->
    <p class="text-sm leading-4">
        Hey {{ $name }},

        <br>
        <br>
        Voici votre code de validation : {{ $token }}
        <br>
        <br>
        Merci de votre confiance.
        
    <div class="flex justify-between items-center">
      <a href="#" class="btn btn-primary">Verify email</a>
      <p class="text-sm">
        If the button is not working for you, copy the URL below into your browser.
      </p>
      <a href="https://readyplayer.me/verify/[your-email-address]" class="text-sm text-primary">[your-email-address]</a>
    </div>
  </div>
</body>
</html>
