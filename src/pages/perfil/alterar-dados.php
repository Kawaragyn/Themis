<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Dados | THEMIS</title>
</head>
<body>
    <form action="/upload" method="POST" enctype="multipart/form-data">
        <label for="documento">Envie seu documento:</label>
        <input type="file" id="documento" name="documento">
        
        <button type="submit">Enviar</button>
    </form>
</body>
</html>