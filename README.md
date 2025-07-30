# API REST de Inscripciones - Evaluación Técnica

Realizado por: **Leandro Fabian Martinez**

---

## 1. Contexto

Este repositorio contiene la solución a la evaluación técnica para el puesto de Desarrollador PHP. El proyecto consiste en una API REST construida con Laravel 11 para gestionar la inscripción de participantes a un evento, siguiendo los requisitos funcionales y de arquitectura solicitados.

---

## 2. Stack Tecnológico

* **Framework**: Laravel 11
* **Lenguaje**: PHP 8.4.10
* **Base de Datos**: MySQL
* **Caché y Colas**: Redis

---

## 3. Instalación y Ejecución

Sigue estos pasos para configurar el proyecto en un entorno de desarrollo local.

### Prerrequisitos

* PHP (v8.4.10 o compatible)
* Composer
* Un servidor de base de datos (MySQL / MariaDB)
* Un servidor de Redis

### Pasos

1.  **Clonar el repositorio y acceder a la carpeta**:
    ```bash
    git clone [https://github.com/LeaFMtz/prueba-tecnica-php-laravel.git](https://github.com/LeaFMtz/prueba-tecnica-php-laravel.git)
    cd prueba-tecnica-php-laravel
    ```

2.  **Instalar dependencias de PHP**:
    ```bash
    composer install
    ```

3.  **Configurar el archivo de entorno**:
    * Copia el archivo de ejemplo `.env.example` a un nuevo archivo llamado `.env`.
        ```bash
        cp .env.example .env
        ```
    * **Importante**: Antes de continuar, crea una base de datos vacía en tu gestor de base de datos.
    * Abre el archivo `.env` y edítalo para que coincida con tu configuración local. Asegúrate de revisar estas líneas:
        ```dotenv
        # Credenciales de la Base de Datos
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=el_nombre_de_tu_db_vacia
        DB_USERNAME=tu_usuario_db
        DB_PASSWORD=tu_contraseña_db

        # Configuración de Redis
        SESSION_DRIVER=redis
        QUEUE_CONNECTION=redis
        CACHE_STORE=redis
        ```

4.  **Generar la clave de la aplicación**:
    ```bash
    php artisan key:generate
    ```

5.  **Crear las tablas de la base de datos**:
    ```bash
    php artisan migrate
    ```

### Ejecución

Para el correcto funcionamiento de la API, necesitas tener dos procesos corriendo en dos terminales separadas:

* **Terminal 1 (Servidor Web)**:
    ```bash
    php artisan serve
    ```
* **Terminal 2 (Procesador de Colas)**:
    ```bash
    php artisan queue:work
    ```

---

## 4. Endpoints de la API

La API expone los siguientes endpoints para interactuar con el sistema.

| Método | URL                          | Descripción                               |
| :----- | :--------------------------- | :---------------------------------------- |
| `POST` | `/api/participants`          | Registra un nuevo participante.           |
| `GET`  | `/api/participants/{id}`     | Obtiene un participante específico por su ID. |
| `GET`  | `/api/stats`                 | Obtiene el contador total de participantes registrados. |

### Ejemplo de Petición `POST /api/participants`

Para registrar un nuevo participante, se debe enviar un cuerpo (body) en formato JSON con la siguiente estructura:

```json
{
    "nombre": "Ana",
    "apellido": "López",
    "dni": "35123456",
    "email": "ana.lopez@example.com"
}
```

Para ver ejemplos completos y ejecutables de todas las peticiones, consulta el archivo `api_requests.http` incluido en la raíz de este repositorio. Dicho archivo puede ser utilizado con la extensión **REST Client** para Visual Studio Code.

---

## 5. Decisiones de Arquitectura

Para el desarrollo de esta API, se tomó un enfoque de arquitectura por capas, priorizando la separación de responsabilidades, la mantenibilidad y la eficiencia, en línea con los principios **SOLID**.

* **Arquitectura en Capas (Controller-Service-Repository)**
    Se implementó este patrón para delimitar claramente las responsabilidades:
    * **Controller**: Su única función es gestionar la comunicación HTTP (peticiones y respuestas), delegando toda la lógica de negocio.
    * **Service**: Actúa como el orquestador de la lógica de negocio. Mantiene el código de la aplicación puro, sin dependencias del framework, y coordina las interacciones entre los diferentes componentes.
    * **Repository**: Abstrae y centraliza toda la interacción con la capa de persistencia de datos (base de datos y caché). Esto permite que la lógica de negocio sea agnóstica a la fuente de datos.

* **Desacoplamiento con Eventos y Listeners (Patrón Observer)**
    Para cumplir con el requisito de ejecutar acciones secundarias de forma desacoplada, se utilizó el sistema de Eventos de Laravel. Al registrar un participante, se dispara un evento `ParticipantRegistered`. Dos `Listeners` independientes (`SendWelcomeNotification` y `UpdateRegistrationStats`) reaccionan a este evento. De esta forma, el servicio de registro no conoce los detalles de las acciones secundarias, y se pueden añadir más acciones en el futuro sin modificar el flujo principal.

* **Eficiencia mediante Colas (Queues)**
    El listener que simula el envío del email (`SendWelcomeNotification`) implementa la interfaz `ShouldQueue`. Esto hace que la tarea se procese en segundo plano a través de un trabajador de colas, usando Redis. El resultado es una respuesta inmediata y una mejor experiencia para el cliente de la API, ya que no tiene que esperar a que se completen tareas potencialmente lentas.

* **Validación Robusta y Aislada (Form Requests)**
    La validación de las peticiones se extrajo de los controladores y se encapsuló en clases `Form Request` dedicadas. Esto limpia el controlador de responsabilidades que no le corresponden, centraliza las reglas de validación y hace el código más reutilizable y fácil de leer.

* **Estrategia de Caché para Rendimiento**
    Se implementó una estrategia de caché en el método `findById` del repositorio utilizando `Cache::remember`. Esto asegura que las consultas repetidas por un mismo participante se sirvan directamente desde Redis, evitando accesos innecesarios a la base de datos y garantizando el rendimiento "casi instantáneo" solicitado.

* **Contador Global Atómico con Redis**
    Para la actualización de las estadísticas, se utilizó directamente el comando `INCR` de Redis. Esta decisión se basa en que es una operación atómica y extremadamente rápida, lo que garantiza la consistencia del contador sin impactar el rendimiento. A diferencia del envío de email, esta acción se ejecuta de forma síncrona, demostrando un uso selectivo de las colas solo cuando es estrictamente necesario.

* **Integridad de Datos en la Base de Datos**
    Además de la validación en la aplicación, se añadieron restricciones `UNIQUE` a nivel de base de datos en las columnas `dni` y `email` a través de las migraciones. Esto proporciona una capa final y robusta de seguridad para garantizar la integridad de los datos.