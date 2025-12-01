# Gestión de citas médicas (Laravel + Vue)

Aplicación educativa para practicar un flujo completo de desarrollo con Laravel, Vue (Inertia) y CI en GitHub Actions. El proyecto permite:

- Solicitar citas desde un formulario público validando que la hora elegida esté libre en el centro médico seleccionado.
- Iniciar sesión para acceder a un panel de administración donde se consultan las citas registradas.
- Gestionar (crear, actualizar y eliminar) los centros médicos disponibles.
- Ejecutar pruebas unitarias y de integración, además de un pipeline de integración continua preconfigurado.

## Puesta en marcha

1. **Instalar dependencias**

   ```bash
   cp .env.example .env
   composer install
   npm ci
   php artisan key:generate
   ```

2. **Migraciones y base de datos**

   El proyecto usa SQLite por defecto en los tests, pero puedes configurar cualquier base de datos en `.env`.

   ```bash
   php artisan migrate
   ```

3. **Compilar los assets** (para entorno local)

   ```bash
   npm run dev
   ```

   Para producción o CI se usa `npm run build`.

4. **Servidor de desarrollo**

   ```bash
   php artisan serve
   ```

   El formulario público está en `/` y el panel administrativo en `/dashboard` (requiere autenticación). Los usuarios se gestionan con el flujo de autenticación que incluye Laravel Fortify.

5. **Pruebas**

   ```bash
   php artisan test
   ```

## Integración continua

El repositorio incluye workflows de GitHub Actions que instalan dependencias, generan la llave de la app, compilan los assets y ejecutan PHPUnit (`.github/workflows/tests.yml`). Esto permite a los alumnos validar sus cambios automáticamente al crear un fork y abrir un Pull Request.

## Estructura clave

- `app/Models`: Modelos de dominio (citas y centros médicos).
- `app/Http/Controllers`: Controladores para el formulario público y el panel.
- `resources/js/pages`: Vistas Inertia/Vue para el panel (`Dashboard`) y el formulario (`appointments/Request`).
- `database/migrations`: Migraciones para usuarios, centros y citas.
- `tests/Feature`: Cobertura básica de las reglas de negocio y del CRUD de centros.

¡Buen trabajo experimentando con pruebas automatizadas y despliegues continuos!
