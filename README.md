## 🔗 Shorto - Acortador de URLs con IA
Shorto es una aplicación moderna de acortamiento de URLs con funcionalidades avanzadas, incluyendo un asistente de IA, gestión de grupos y un sistema completo de usuario.

### ✨ Características Principales
#### 🎯 Acortamiento de URLs
- Creación de enlaces cortos personalizados
- Alias personalizados para URLs 
- Protección con contraseña opcional 
- Descripción y metadatos para cada URL 
#### 🤖 Asistente de IA
- Chatbot inteligente powered by Gemini AI ShortoAgent.php
- Gestión de URLs a través de comandos naturales ShortoAgent.php
- Historial de conversaciones persistente ShortoAgent.php
#### 📁 Gestión de Grupos
- Organización de URLs en grupos Url.php
- Creación, edición y eliminación de grupos 
- Asignación y desasignación de URLs a grupos 
#### 👤 Sistema de Usuarios
- Registro y autenticación JWT 
- Sistema de roles y permisos 
- Gestión de perfil de usuario
#### 💳 Sistema de Suscripciones
- Integración con Laravel Cashier 
- Gestión de suscripciones y pagos
#### 🛠️ Tecnologías Utilizadas
- `Backend`: Laravel 12
- `Base de Datos`: MySQL
- `Cache`: Redis 
- `IA`: Gemini AI 2.0 Flash 
- `Autenticación`: JWT
- `Monitoreo`: Inspector APM 
#### 📋 Requisitos
- PHP 8.2+ 
- MySQL/MariaDB
- Redis
- Composer
#### 🚀 Instalación
- Clonar el repositorio
    - git clone https://github.com/petersonsenadevs/shorto.git  
    - cd shorto
- Instalar dependencias
    - composer install  
- Configurar el entorno
    - cp .env.example .env  
    - php artisan key:generate
- Configurar la base de datos
    - Edita el archivo .env con tus credenciales de base de datos
- Ejecuta las migraciones:
    - php artisan migrate
- Ejecuta los Seeders
    - php artisan db:seed

Iniciar el servidor
php artisan serve

### 📖 API Endpoints
#### Autenticación
- POST /api/register - Registro de usuario 
- POST /api/login - Inicio de sesión 
#### URLs
- POST /api/url/shorten - Acortar URL 
- GET /api/url/list - Listar URLs del usuario 
- PUT /api/url/update/{url} - Actualizar URL 
- DELETE /api/url/delete/{shortUrl} - Eliminar URL 
#### Grupos
- POST /api/group - Crear grupo 
- GET /api/group/list - Listar grupos con URLs 
- PUT /api/group/{groupId} - Actualizar grupo 
#### Chat IA
- POST /api/chat/send - Enviar mensaje al asistente 
- POST /api/conversation - Crear conversación 
- GET /api/conversation/list - Listar conversaciones 
  

### 🤖 Funcionalidades del Asistente IA
El asistente IA de Shorto puede ayudarte con:

- Acortar URLs mediante comandos naturales 
- Buscar y gestionar URLs existentes
- Crear y organizar grupos de URLs
- Búsquedas web integradas 


### 🔒 Seguridad
- Autenticación JWT con middleware personalizado 
- Rate limiting en endpoints críticos 
- Sistema de roles y permisos granular
- Protección contra IPs bloqueadas
  
### 📄 Licencia
Este proyecto está licenciado bajo la Licencia MIT 




[![Ask DeepWiki](https://deepwiki.com/badge.svg)](https://deepwiki.com/petersonsenadevs/shorto)
