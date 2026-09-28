# 🍕 Pizzería "La Napo" - Sistema Web de Punto de Venta y Gestión

Sistema de gestión administrativa y punto de venta web para la Pizzería Artesanal "La Napo", desarrollado como Proyecto Integrador para la asignatura de **Gestión y Desarrollo de Software**.

---

## 👥 Integrantes del Equipo
* **Roberto Sebastián Sánchez Martínez**
* **Diego**
* **Kevin**
* **Arath**

---

## 🛠️ Tecnologías Utilizadas
* **Servidor Local:** AppServ (Apache 2.4, PHP 8.x, MySQL / phpMyAdmin)
* **Frontend:** HTML5, CSS3, JavaScript
* **Backend:** PHP
* **Base de Datos:** MySQL
* **Control de Versiones:** Git y GitHub

---

## 🏗️ Arquitectura y Microservicios

El sistema está dividido en 4 módulos/microservicios independientes:

| Microservicio / Carpeta | Responsables | Descripción del Módulo | Archivos Asociados |
| :--- | :--- | :--- | :--- |
| `servicio-usuarios/` | Sebastian y Diego | Registro de empleados, autenticación (login) y roles (Admin, Cajero, Repartidor). | `login.php`, `logout.php`, `usuarios.php`, `navbar.php`, `conexion.example.php` |
| `servicio-productos/` | Kevin y Arath | CRUD de catálogo de pizzas 12", promociones fijas y precios. | `productos.php`, `promociones.php` |
| `servicio-inventario/` | Sebastian y Arath | Control de existencias de materia prima (queso, harina, cajas) y alertas de stock mínimo. | `inventario.php`, `alertas.php` |
| `servicio-ventas/` | Diego y Kevin | Registro de ventas (mostrador/domicilio), cambio de estatus de orden y emisión de tickets. | `ventas.php`, `ticket.php` |

---

## ⚙️ Requisitos e Instalación

1. Instalar **AppServ** (Apache, PHP, MySQL).
2. Clonar este repositorio dentro de la carpeta del servidor web:
```bash
   cd C:\AppServ\www\
   git clone https://github.com/sebastiansanz1/pizzeria_lanapo.git
```
3. Crear la base de datos en phpMyAdmin:
   * Crear una base llamada `pizzeria_lanapo`.
   * Importar el archivo `docs/base_de_datos.sql`.
4. Configurar la conexión (este archivo **no se sube al repositorio** porque contiene credenciales):
   * Copiar `servicio-usuarios/conexion.example.php` como `servicio-usuarios/conexion.php`.
   * Editar `conexion.php` y poner la contraseña de MySQL local en `$password`.

---

## ▶️ Instrucciones de Ejecución

1. Iniciar Apache y MySQL desde AppServ.
2. Abrir en el navegador: `http://localhost/pizzeria_lanapo/`
3. Iniciar sesión desde `servicio-usuarios/login.php` con un usuario registrado.

> Si clonaste el repo con otro nombre de carpeta, ajusta la URL a ese nombre.

---

## 📸 Evidencias

Las capturas de las prácticas (repositorio, estructura, commits, ramas, Pull Requests y GitHub Actions) se encuentran en la carpeta `docs/evidencias/`.

---

## 📌 Estado del Proyecto

* ✅ Módulos de usuarios, productos, inventario y ventas funcionando de forma local.
* ✅ Repositorio en GitHub con `.gitignore` y credenciales fuera del control de versiones.
* 🔄 En proceso: flujo con ramas y Pull Requests, e integración continua con GitHub Actions.