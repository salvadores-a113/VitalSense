# VitalSense

Proyecto integrador que reúne tres módulos desarrollados en un hackatón, enfocados en monitoreo e IoT: agricultura inteligente, hogar inteligente y monitoreo de salud.

## Módulos

### 🌱 Smart Agriculture (Gemini)
Aplicación en Python que utiliza la API de Gemini para analizar el estado de plantas a partir de datos/imágenes, con una interfaz web simple (Flask + templates).

**Stack:** Python, Flask, Google Gemini API

**Archivos principales:**
- `Planta.py` — lógica principal de análisis
- `check_models.py` — verificación de modelos disponibles de Gemini
- `templates/index.html` — interfaz web

### 🏠 Smart Home
Sistema de control y monitoreo de dispositivos del hogar, con visualización de planos de casa y consumo energético por sala.

**Stack:** HTML/CSS/JS (frontend), PHP + MySQL (backend)

**Archivos principales:**
- `Home/` — interfaz de control de dispositivos con plano interactivo de la casa
- `hackaton/` — API en PHP para dispositivos y salas, y reportes de consumo
- `hackaton.sql` — esquema de base de datos

### ❤️ Health Monitor
Plataforma web para monitoreo de pacientes: registro, login, seguimiento de ritmo cardíaco y detección de anomalías.

**Stack:** PHP, MySQL, JavaScript, HTML/CSS

**Archivos principales:**
- `index.php`, `login.php`, `register.php` — autenticación de usuarios
- `dashboard.php`, `profile.php` — panel de paciente
- `simulate_heart_rate.php`, `simulate_anomaly.php` — simulación de datos vitales
- `health_monitor.sql` / `db/init.sql` — esquema de base de datos

## Requisitos

- PHP 7.4+ con extensión PDO/MySQLi
- MySQL / MariaDB
- Python 3.8+ (para el módulo de Smart Agriculture)
- Servidor local tipo XAMPP/WAMP para los módulos PHP

## Configuración

1. Importa los archivos `.sql` de cada módulo (`hackaton.sql`, `health_monitor.sql`, `db/init.sql`) a tu base de datos local.
2. Ajusta las credenciales de conexión en los archivos `conexion.php`, `db.php` y `includes/config.php` según tu entorno (por defecto usan `root` sin contraseña, típico de XAMPP).
3. Para el módulo de Smart Agriculture, instala las dependencias de Python necesarias y configura tu propia API key de Gemini (no incluida en el repositorio).

## Notas

Este proyecto fue desarrollado como parte de un hackatón, por lo que algunas configuraciones (credenciales de base de datos, rutas locales) están pensadas para un entorno de desarrollo local y deben ajustarse antes de un despliegue en producción.
