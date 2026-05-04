<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar producto</title>
</head>
<body>
    <h2>Agregar producto</h2>
    <p><a href="index.php">Volver a la lista</a></p>

    <p id="mensaje"></p>

    <form id="formProducto">
        Nombre: <input type="text" name="nombre" required><br><br>
        Cantidad: <input type="number" name="cantidad" required><br><br>
        Precio: <input type="number" step="0.01" name="precio" required><br><br>

        <button type="submit">Guardar</button>
    </form>

    <script>
        const API_URL = 'api/productos.php';
        const form = document.getElementById('formProducto');
        const mensaje = document.getElementById('mensaje');

        function mostrarMensaje(texto, esError = false) {
            mensaje.textContent = texto;
            mensaje.style.color = esError ? 'red' : 'green';
        }

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(form);
            const payload = {
                nombre: String(formData.get('nombre') || '').trim(),
                cantidad: Number(formData.get('cantidad')),
                precio: Number(formData.get('precio'))
            };

            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (!response.ok || !result.ok) {
                    throw new Error(result.error || 'No se pudo guardar el producto');
                }

                mostrarMensaje('Producto guardado correctamente');
                form.reset();

                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 700);
            } catch (error) {
                mostrarMensaje(error.message, true);
            }
        });
    </script>
</body>
</html>