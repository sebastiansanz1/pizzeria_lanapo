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
| `servicio-usuarios/` | Sebastian y Diego | Registro de empleados, autenticación (login) y roles (Admin, Cajero, Repartidor). | `login.php`, `usuarios.php`, `conexion.php` |
| `servicio-productos/` | Kevin y Arath | CRUD de catálogo de pizzas 12", promociones fijas y precios. | `productos.php`, `promociones.php` |
| `servicio-inventario/` | Sebastian y Arath | Control de existencias de materia prima (queso, harina, cajas) y alertas de stock mínimo. | `inventario.php`, `alertas.php` |
| `servicio-ventas/` | Diego y Kevin | Registro de ventas (mostrador/domicilio), cambio de estatus de orden y emisión de tickets. | `ventas.php`, `ticket.php` |

---

## ⚙️ Requisitos e Instalación

1. Descargar e instalar **AppServ** (Apache, PHP, MySQL).
2. Clonar este repositorio en la carpeta del servidor web:
   ```bash
   cd C:\AppServ\www\
   git clone [https://github.com/Sebastiansanz1/pizzeria_lanapo.git](https://github.com/Sebastiansanz1/pizzeria_lanapo.git)