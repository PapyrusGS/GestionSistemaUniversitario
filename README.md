# Sistema de Gestión Universitaria Patito (SGU)

## 🚀 Prerrequisitos

Antes de configurar y ejecutar el proyecto, asegúrate de tener instalado en tu equipo:

*   **PHP** (Versión 8.2 o superior recomendada)
*   **Composer** (Gestor de dependencias de PHP)
*   **Node.js** (Versión 20 o superior recomendada) y **NPM**
*   **Servidor de Base de Datos MySQL** (e.g., XAMPP, Laragon o MySQL Server local)
*   **xampp**

---

## 🔧 Configuración del Backend (Laravel)

Sigue estos pasos para levantar el entorno de la API:

1.  **Navega al directorio del backend:**
    ```bash
    cd Backend
    ```

2.  **Instalar dependencias de PHP:**
    Ejecuta el siguiente comando para instalar todas las dependencias del proyecto (incluyendo las librerías para la generación de reportes en PDF y Excel):
    ```bash
    composer install
    ```
    *Nota: Si por alguna razón necesitas instalar o reinstalar de forma manual las dependencias específicas de reportes, puedes ejecutar:*
    ```bash
    composer require barryvdh/laravel-dompdf
    composer require maatwebsite/excel
    ```

3.  **Configurar las variables de entorno:**
    *   Copia el archivo de configuración de ejemplo:
        ```bash
        cp .env.example .env
        ```
    *   Abre el archivo `.env` recién creado y ajusta la configuración de tu base de datos local. Por defecto, el sistema está configurado para conectarse a una base de datos llamada `universidad`:
        ```env
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=universidad
        DB_USERNAME=root
        DB_PASSWORD=
        ```

4.  **Generar la clave de cifrado de la aplicación:**
    ```bash
    php artisan key:generate
    ```

5.  **Crear y Sembrar la Base de Datos:**
    *   Crea una base de datos vacía en tu servidor local con el nombre especificado en tu `.env` (e.g. `universidad`).
    *   Ejecuta las migraciones y los seeders. Este comando estructurará las tablas, cargará los **Procedimientos Almacenados** académicos y registrará los datos de prueba iniciales:
        ```bash
        php artisan migrate:fresh --seed
        ```
    *   *(Opcional)* En la raíz del repositorio se incluye un archivo de respaldo `universidadV2.sql` por si deseas realizar una importación directa de la estructura SQL. Sin embargo, se recomienda el uso de migraciones de Laravel para asegurar el correcto despliegue de los Stored Procedures.

6.  **Iniciar el servidor local de desarrollo:**
    ```bash
    php artisan serve
    ```
    *La API estará disponible en [http://127.0.0.1:8000](http://127.0.0.1:8000).*

---

## 💻 Configuración del Frontend (Vue 3 + Vite)

Sigue estos pasos para arrancar la interfaz de usuario:

1.  **Navega al directorio del frontend desde la raíz del proyecto:**
    ```bash
    cd FrontEnd
    ```

2.  **Instalar dependencias de JavaScript:**
    ```bash
    npm install
    ```

3.  **Configurar variables de entorno:**
    *   Copia el archivo de configuración de ejemplo:
        ```bash
        cp .env.example .env
        ```
    *   Verifica que la variable `VITE_API_BASE_URL` apunte a la dirección correcta de tu backend (por defecto, `http://localhost:8000/api`):
        ```env
        VITE_API_BASE_URL=http://localhost:8000/api
        ```

4.  **Iniciar el servidor de desarrollo de Vite:**
    ```bash
    npm run dev
    ```
    *La interfaz de usuario estará disponible en [http://localhost:5173](http://localhost:5173) (o el puerto que te asigne Vite en consola).*

---

## 👥 Credenciales de Acceso para Pruebas

Los seeders de base de datos registran tres usuarios de prueba correspondientes a cada rol operativo del sistema. La contraseña para todos es **`123456`**:

| Rol | Correo Electrónico (Login) | Contraseña | Funciones Clave |
| :--- | :--- | :--- | :--- |
| **Administrador** | `admin@sistema.com` | `123456` | Gestión de usuarios, roles, carreras, materias, asignación de docentes en cursos, y visualización de reportes globales de auditoría/desempeño. |
| **Docente** | `docente@sistema.com` | `123456` | Consulta de cursos asignados, registro/edición de calificaciones de estudiantes, rendimiento académico del curso y reportes descargables en PDF/Excel. |
| **Estudiante** | `estudiante@sistema.com` | `123456` | Visualización de dashboard, consulta de materias disponibles para inscripción, inscripción en línea de cursos, consulta de calificaciones, historial académico y malla curricular. |

---

## ✨ Características Especiales del Sistema

*   **Lógica de Negocio mediante Stored Procedures:** La mayor parte del procesamiento crítico (procesar rendimiento, verificar inscripciones, kárdex, y generar reportes específicos) se realiza de forma directa en el servidor MySQL mediante Procedimientos Almacenados, optimizando la velocidad de respuesta y la integridad referencial.
*   **Auditoría Interna de Base de Datos:** Registra y traza de forma automática cada cambio en notas u operaciones académicas críticas en la tabla de auditoría, identificando qué usuario realizó la modificación, cuándo, desde qué IP y los valores previos/posteriores.
*   **Reportes Multiformato:** Generación y descarga nativa de reportes tanto en formato PDF (listos para imprimir) como Excel/CSV (para análisis administrativo).