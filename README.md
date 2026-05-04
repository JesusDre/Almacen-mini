# Sistema de Almacén con API REST

Sistema básico de gestión de inventario desarrollado en PHP, que permite administrar productos mediante operaciones CRUD y exposición de una API REST.

---

##  Descripción

Este proyecto consiste en un sistema sencillo de almacén que permite:

* Registrar productos
* Consultar inventario
* Actualizar información
* Eliminar registros

Además, incluye una API REST que expone estos datos para su consumo desde aplicaciones externas.

<img src="assets/demo.png" width="600">
![Demo](assets/demo.png)

---

##  Objetivo del proyecto

El objetivo principal fue comprender e implementar:

* Arquitectura básica en PHP sin frameworks
* Comunicación con base de datos MySQL
* Diseño de endpoints REST
* Operaciones CRUD completas

---

##  Tecnologías utilizadas

* PHP (servidor backend)
* MySQL (base de datos)
* HTML (interfaz básica)

---

##  Estructura del proyecto

```
almacen/
│
├── conexion.php        # Conexión a la base de datos
├── index.php           # Vista principal (listado de productos)
├── agregar.php         # Formulario de registro
│
├── api/
│   ├── productos.php   # Endpoint principal (GET, POST, PUT, DELETE)
```

---

##  API REST

###  Endpoint base

```
http://localhost:8000/api/productos.php
```

---

### Obtener productos (GET)

```
GET /api/productos.php
```

---

###  Crear producto (POST)

```
POST /api/productos.php
```

Body (JSON):

```json
{
  "nombre": "Laptop",
  "cantidad": 10,
  "precio": 15000
}
```

---

###  Actualizar producto (PUT)

```
PUT /api/productos.php?id=1
```

---

###  Eliminar producto (DELETE)

```
DELETE /api/productos.php?id=1
```

---

## Cómo ejecutar el proyecto

1. Clonar el repositorio:

```bash
git clone https://github.com/JesusDre/Almacen-mini.git
cd almacen
```

2. Crear la base de datos en MySQL:

```sql
CREATE DATABASE almacen;

USE almacen;

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    cantidad INT,
    precio DECIMAL(10,2)
);
```

3. Iniciar el servidor:

```bash
php -S localhost:8000
```

4. Abrir en navegador:

```
http://localhost:8000
```

## Notas

Este proyecto forma parte de mi aprendizaje en desarrollo backend y diseño de APIs REST en PHP
