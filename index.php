<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
</head>
<body>
    <h2>Lista de productos</h2>
    <p>
        <a href="agregar.php">Agregar producto</a>
        <button id="btnRefrescar" type="button">Refrescar</button>
    </p>

    <p id="mensaje"></p>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tablaProductos"></tbody>
    </table>

    <script>
        const API_URL = 'api/productos.php';
        const tabla = document.getElementById('tablaProductos');
        const mensaje = document.getElementById('mensaje');
        const btnRefrescar = document.getElementById('btnRefrescar');

        function mostrarMensaje(texto, esError = false) {
            mensaje.textContent = texto;
            mensaje.style.color = esError ? 'red' : 'green';
        }

        function filaProducto(producto) {
            return `
                <tr>
                    <td>${producto.id}</td>
                    <td>${producto.nombre}</td>
                    <td>${producto.cantidad}</td>
                    <td>${producto.precio}</td>
                    <td>
                        <button type="button" data-accion="editar" data-id="${producto.id}">Editar</button>
                        <button type="button" data-accion="eliminar" data-id="${producto.id}">Eliminar</button>
                    </td>
                </tr>
            `;
        }

        async function cargarProductos() {
            mostrarMensaje('Cargando productos...');
            tabla.innerHTML = '';

            try {
                const response = await fetch(API_URL);
                const result = await response.json();

                if (!response.ok || !result.ok) {
                    throw new Error(result.error || 'No se pudieron cargar los productos');
                }

                if (!result.data.length) {
                    tabla.innerHTML = '<tr><td colspan="5">No hay productos registrados</td></tr>';
                    mostrarMensaje('Sin productos cargados');
                    return;
                }

                tabla.innerHTML = result.data.map(filaProducto).join('');
                mostrarMensaje('Productos cargados correctamente');
            } catch (error) {
                tabla.innerHTML = '<tr><td colspan="5">Error al cargar</td></tr>';
                mostrarMensaje(error.message, true);
            }
        }

        async function eliminarProducto(id) {
            const confirmado = confirm('Deseas eliminar este producto?');
            if (!confirmado) {
                return;
            }

            try {
                const response = await fetch(`${API_URL}?id=${id}`, {
                    method: 'DELETE'
                });
                const result = await response.json();

                if (!response.ok || !result.ok) {
                    throw new Error(result.error || 'No se pudo eliminar');
                }

                mostrarMensaje('Producto eliminado');
                await cargarProductos();
            } catch (error) {
                mostrarMensaje(error.message, true);
            }
        }

        async function editarProducto(id) {
            try {
                const detalleResponse = await fetch(`${API_URL}?id=${id}`);
                const detalle = await detalleResponse.json();

                if (!detalleResponse.ok || !detalle.ok) {
                    throw new Error(detalle.error || 'No se pudo obtener el producto');
                }

                const actual = detalle.data;
                const nombre = prompt('Nombre:', actual.nombre);
                if (nombre === null) {
                    return;
                }

                const cantidad = prompt('Cantidad:', actual.cantidad);
                if (cantidad === null) {
                    return;
                }

                const precio = prompt('Precio:', actual.precio);
                if (precio === null) {
                    return;
                }

                const payload = {
                    nombre: nombre.trim(),
                    cantidad: Number(cantidad),
                    precio: Number(precio)
                };

                const updateResponse = await fetch(`${API_URL}?id=${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                const updated = await updateResponse.json();

                if (!updateResponse.ok || !updated.ok) {
                    throw new Error(updated.error || 'No se pudo actualizar');
                }

                mostrarMensaje('Producto actualizado');
                await cargarProductos();
            } catch (error) {
                mostrarMensaje(error.message, true);
            }
        }

        tabla.addEventListener('click', async (event) => {
            const button = event.target.closest('button');
            if (!button) {
                return;
            }

            const id = Number(button.dataset.id);
            const accion = button.dataset.accion;

            if (!id || !accion) {
                return;
            }

            if (accion === 'eliminar') {
                await eliminarProducto(id);
                return;
            }

            if (accion === 'editar') {
                await editarProducto(id);
            }
        });

        btnRefrescar.addEventListener('click', cargarProductos);

        cargarProductos();
    </script>
</body>
</html>