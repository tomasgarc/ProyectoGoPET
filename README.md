# GoPET: Plataforma de Cuidado de Perros de Confianza
**Proyecto Intermodular PIDAWE - 2º DAW (Curso 2025/26)**

GoPET es una aplicación web responsiva diseñada para conectar a dueños de perros con cuidadores locales calificados de forma segura. La plataforma integra mensajería en tiempo real, reseñas bilaterales, una cartera digital integrada y un sistema de pagos protegidos en depósito de garantía (*Escrow*), complementado con un pipeline de análisis de datos desarrollado en Python.

---

## 🗺️ Mapa del Proyecto: Alineación con la Rúbrica PIDAWE

Este repositorio está estructurado para facilitar la auditoría técnica por parte del tribunal docente. A continuación se detallan los archivos del proyecto que corresponden a los criterios de evaluación de cada módulo profesional:

### 1. Bases de Datos (1º DAW)
* **Modelado e Integridad**: Las restricciones relacionales (PK, FK), deletes en cascada y checks lógicos se definen en las migraciones de [database/migrations/](file:///d:/ProyectoGoPET/database/migrations).
* **Script SQL Completo**: En [database/schema.sql](file:///d:/ProyectoGoPET/database/schema.sql) se encuentra el código DDL nativo del modelo.
* **Disparadores (Triggers)**: Contiene el disparador `tg_validate_care_request_dates` para la validación lógica de fechas y `tg_audit_payment_status` con su correspondiente tabla de auditoría para registrar transacciones de pagos.
* **Consultas Complejas y Vistas**: Implementación de las vistas `view_platform_financial_summary` y `view_caretaker_rankings`, junto con índices optimizados y consultas avanzadas agrupadas con `HAVING` y `ORDER BY`.

### 2. Programación (1º DAW)
* **Orientación a Objetos y Estructura**: Código PHP estructurado bajo el patrón MVC usando clases, herencia y encapsulación en los modelos de [app/Models/](file:///d:/ProyectoGoPET/app/Models).
* **Docstrings y Comentarios**: Toda la lógica crítica de controladores en [app/Http/Controllers/](file:///d:/ProyectoGoPET/app/Http/Controllers) cuenta con bloques de comentarios estructurados (PHPDoc).
* **Tratamiento de Excepciones**: Validación robusta de datos de entrada mediante `$request->validate()` en peticiones, pagos y reviews, capturando fallos de integridad antes de interactuar con el almacenamiento.

### 3. Sostenibilidad aplicada al sistema productivo (1º DAW)
* **Green Code (Eficiencia)**: Optimización de consultas a base de datos mediante la precarga de relaciones (*Eager Loading*) utilizando `with()` en todos los controladores para evitar el problema de consultas $N+1$.
* **Gestión de Residuos Digitales**: Comando Artisan programable en [CleanDigitalResidues.php](file:///d:/ProyectoGoPET/app/Console/Commands/CleanDigitalResidues.php). Se ejecuta mediante `php artisan gopet:clean-residues`, eliminando peticiones obsoletas y archivos de fotos huérfanas en disco, estimando los gramos de CO2eq ahorrados anualmente.

### 4. Entornos de Desarrollo (1º DAW)
* **Control de Versiones**: Flujo de trabajo basado en ramas Git y commits cronológicos que reflejan el ciclo de vida del software, documentado en [CHANGELOG.md](file:///d:/ProyectoGoPET/CHANGELOG.md).
* **Plan de Pruebas**: Suite de pruebas unitarias y de integración en el directorio [tests/](file:///d:/ProyectoGoPET/tests) que cubren flujos de cobros, registro y gestión de perfiles.
* **Calidad y Automatización**: Integración de Laravel Pint para la limpieza y homogeneidad de sintaxis de código.

### 5. Lenguajes de Marcas y Sistemas de Gestión de Información (1º DAW)
* **Diseño Adaptativo**: Hojas de estilos en [resources/css/app.css](file:///d:/ProyectoGoPET/resources/css/app.css) estructuradas en base a Tailwind CSS con diseño responsivo móvil-primero.
* **Estrategia SEO y Semántica HTML5**: Uso estricto de etiquetas estructuradas HTML5 (`<header>`, `<main>`, `<section>`). Inyección de cabeceras de metadatos SEO descriptivos y protocolo OpenGraph en el archivo base [layouts/app.blade.php](file:///d:/ProyectoGoPET/resources/views/layouts/app.blade.php) y [welcome.blade.php](file:///d:/ProyectoGoPET/resources/views/welcome.blade.php).
* **Sindicación y Almacenamiento (XML/JSON)**: 
  - Archivos JSON (`storage/app/analytics.json`) para comunicación estructurada de datos.
  - Sindicación XML mediante el canal RSS implementado en la plantilla [feeds/care_requests_rss.blade.php](file:///d:/ProyectoGoPET/resources/views/feeds/care_requests_rss.blade.php), accesible en la ruta `/feed/care-requests.xml`.

### 6. Desarrollo Web en Entorno Cliente (2º DAW)
* **Interactividad y DOM**: Código JavaScript / Alpine.js integrado para el comportamiento dinámico del chat en tiempo real en la vista de conversaciones y la interactividad en la selección de cuidadores.
* **Conexión Asíncrona**: Consumo de endpoints locales mediante peticiones asíncronas con Axios, configuradas globalmente en [bootstrap.js](file:///d:/ProyectoGoPET/resources/js/bootstrap.js).

### 7. Desarrollo Web en Entorno Servidor (2º DAW)
* **Arquitectura de Servidor**: Enrutamiento web estructurado y protegido mediante middlewares de sesión en [routes/web.php](file:///d:/ProyectoGoPET/routes/web.php).
* **Seguridad y Autenticación**: Control de acceso a recursos basado en roles (*owner*, *caretaker*, *admin*) y protección de rutas. Gestión de variables de entorno sensibles (como la base de datos o variables del sistema) mediante el archivo `.env`.
* **API REST**: Exposición de endpoints de datos estructurados JSON documentados en [routes/api.php](file:///d:/ProyectoGoPET/routes/api.php) controlados por [CareRequestApiController.php](file:///d:/ProyectoGoPET/app/Http/Controllers/Api/CareRequestApiController.php).

### 8. Despliegue de Aplicaciones Web (2º DAW)
* **Entorno de Producción**: Virtualización de servicios lista para desplegar en local o en VPS mediante [Dockerfile](file:///d:/ProyectoGoPET/Dockerfile) y [docker-compose.yml](file:///d:/ProyectoGoPET/docker-compose.yml).
* **Integración Continua (CI/CD)**: Pipeline automático con GitHub Actions configurado en [.github/workflows/laravel.yml](file:///d:/ProyectoGoPET/.github/workflows/laravel.yml), que valida la compilación, lints y tests unitarios en cada integración de código.

### 9. Diseño de Interfaces Web (2º DAW)
* **UX/UI y Responsive**: Interfaz moderna de alta calidad visual utilizando una paleta de colores armónica de tonos índigo y rosa, con bordes redondeados orgánicos (*rounded-3xl*), fuentes de Google Fonts (*Nunito*) y transiciones interactivas fluidas.

### 10. Programación en Python y Análisis de Datos (2º DAW)
* **Procesamiento ETL**: El script de Python [analyze_data.py](file:///d:/ProyectoGoPET/python/analyze_data.py) extrae la información de la base de datos SQLite de Laravel, la procesa y limpia (cálculos de agregados, volúmenes de depósitos y distribuciones) y genera ficheros legibles JSON y gráficos analíticos.
* **Gráficos e Integración**: Generación de diagramas PNG (`dog_sizes.png`, `request_statuses.png`, `revenue_stats.png`) usando `matplotlib` que el panel de control de Laravel (`DashboardController`) incrusta dinámicamente en la vista de usuario.

---

## 🚀 Instalación y Puesta en Marcha

### Prerrequisitos
* PHP >= 8.3
* Composer
* Node.js y npm
* Python 3 (con pip)

### Pasos de Instalación
1. **Clonar el repositorio** y acceder al directorio:
   ```bash
   cd ProyectoGoPET
   ```

2. **Instalar dependencias de PHP (Backend)**:
   ```bash
   composer install
   ```

3. **Instalar dependencias de Node.js (Frontend)**:
   ```bash
   npm install
   ```

4. **Configurar el entorno**:
   Copiar el archivo de variables y generar la clave única de encriptación de Laravel:
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```

5. **Preparar la Base de Datos SQLite**:
   Crear la base de datos sqlite y ejecutar las migraciones y seeders para poblar datos de prueba:
   ```bash
   # En Windows Powershell/CMD crear base de datos vacía si no existe:
   type NUL > database/database.sqlite
   
   # Ejecutar migraciones y semilla
   php artisan migrate:fresh --seed
   ```

6. **Instalar Dependencias de Python (ETL)**:
   Para que la actualización de estadísticas con Python y la renderización de gráficos funcionen, instala las librerías necesarias:
   ```bash
   pip install -r python/requirements.txt
   ```
   *(Nota: Si no se instalan, la plataforma activará de forma automática el modo de compatibilidad de estadísticas en PHP puro, ocultando los gráficos).*

7. **Ejecutar el servidor local**:
   Puedes levantar el servidor de desarrollo utilizando el script automatizado:
   ```bash
   composer dev
   ```
   *(Esto levantará de forma simultánea el servidor de Laravel en `http://127.0.0.1:8000` y el compilador de assets Vite).*

---

## 🐳 Despliegue con Docker

Para levantar la aplicación en un contenedor de producción simulado:
```bash
docker compose up --build
```
La aplicación estará disponible en `http://localhost:8080`.

---

## 🛠️ Comandos de Mantenimiento y Utilidades

### Limpieza de Residuos Digitales (Sostenibilidad)
Para limpiar peticiones obsoletas y liberar espacio eliminando imágenes de perros huérfanas en el disco:
```bash
# Simular la purga (sin borrar nada físicamente)
php artisan gopet:clean-residues --dry-run

# Ejecutar limpieza real
php artisan gopet:clean-residues
```

### Ejecutar Suite de Tests
Para verificar el correcto funcionamiento del software:
```bash
php artisan test
```

---

## 📄 Especificaciones de la API REST y Sindicación RSS

### Endpoints de API (Retornan JSON)
* **Listar peticiones de cuidado activas**:
  `GET /api/care-requests`
* **Listar perros registrados en la plataforma**:
  `GET /api/dogs`

### Canal de Sindicación XML
* **Canal RSS 2.0 de peticiones activas**:
  `GET /feed/care-requests.xml`
  *(Ideal para lectores de noticias o automatizaciones de correos electrónicos externos).*
