# 🎫 Generador de Pases de Eventos para Apple Wallet

Este proyecto Laravel permite generar pases de eventos compatibles con Apple Wallet de forma sencilla. Los usuarios pueden crear pases personalizados especificando el nombre del evento, descripción, lugar, fecha y hora.

## 📋 Características

- ✅ Generación de pases de eventos para Apple Wallet
- ✅ Formulario web intuitivo para crear pases
- ✅ Descarga directa de archivos `.pkpass`
- ✅ Historial de pases generados
- ✅ Interfaz responsive con Bootstrap 5
- ✅ Campos personalizables: nombre, descripción, lugar, fecha y hora

## 🛠️ Tecnologías Utilizadas

- **Laravel 10+**
- **Spatie Laravel Mobile Pass** - Para generar pases compatibles con Apple Wallet
- **Bootstrap 5** - Para la interfaz de usuario
- **PHP 8.1+**
- **MySQL/SQLite** - Base de datos

## 📦 Instalación

### Opción A: Proyecto Completo (Recomendado)

Si quieres usar este proyecto completo con interfaz web incluida:

### Requisitos Previos

- PHP 8.1 o superior
- Composer
- Node.js y npm (opcional, para assets)
- Servidor web (Apache/Nginx) o Laravel Valet
- Base de datos MySQL o SQLite

### Paso 1: Clonar el Repositorio

```bash
git clone https://github.com/tu-usuario/wallet-pass-project.git
cd wallet-pass-project
```

### Paso 2: Instalar Dependencias de PHP

```bash
composer install
```

### Paso 3: Configurar el Archivo .env

```bash
cp .env.example .env
php artisan key:generate
```

Edita el archivo `.env` con tu configuración de base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wallet_pass
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password
```

### Paso 4: Configurar Base de Datos

```bash
php artisan migrate
```

### Paso 5: Configurar Certificados de Apple (PASO CRÍTICO)

⚠️ **IMPORTANTE**: Este es el paso más importante y complejo. Los pases NO funcionarán sin estos certificados correctamente configurados.

#### 5.1. Crear Cuenta de Apple Developer

1. Ve a [developer.apple.com](https://developer.apple.com)
2. Haz clic en **"Account"** en la esquina superior derecha
3. Si no tienes cuenta de desarrollador:
   - Haz clic en **"Enroll"**
   - Selecciona **"Start Your Enrollment"**
   - Elige **"Individual"** (para uso personal) o **"Organization"** (para empresas)
   - Completa el proceso de registro (costo: 99 USD/año)
   - **Espera la aprobación** (puede tomar 24-48 horas)

#### 5.2. Generar Certificate Signing Request (CSR) - MUY DETALLADO

**REQUISITO**: Necesitas una Mac con macOS para este paso.

1. **Abrir Keychain Access**:
   - Presiona `Cmd + Espacio` y busca "Keychain Access"
   - O ve a `Aplicaciones > Utilidades > Acceso a Llaveros`

2. **Crear el CSR**:
   - En la barra de menú, ve a **"Keychain Access"**
   - Selecciona **"Certificate Assistant"**
   - Haz clic en **"Request a Certificate From a Certificate Authority..."**

3. **Rellenar el formulario CSR**:
   ```
   📧 User Email Address: tu-email@ejemplo.com
   👤 Common Name: Tu Nombre (ej: "Juan Pérez")
   📭 CA Email Address: DÉJALO VACÍO (muy importante)
   
   ✅ Request is: Saved to disk (seleccionar esta opción)
   ✅ Let me specify key pair information (marcar esta casilla)
   ```

4. **Haz clic en "Continue"**

5. **Configurar información de claves**:
   ```
   🔐 Key Size: 2048 bits (OBLIGATORIO)
   🔑 Algorithm: RSA (OBLIGATORIO)
   ```

6. **Guardar el archivo**:
   - Nombre sugerido: `CertificateSigningRequest.certSigningRequest`
   - Guárdalo en el Escritorio para encontrarlo fácilmente
   - **NO MODIFIQUES el nombre o extensión**

#### 5.3. Encontrar tu Team ID

**ANTES** de crear el Pass Type ID, necesitas encontrar tu Team ID:

1. Ve a [developer.apple.com/account/](https://developer.apple.com/account/)
2. Inicia sesión con tu cuenta de desarrollador
3. En la sección **"Membership details"** verás:
   ```
   Team ID: ABC123XYZ (ejemplo)
   ```
4. **Copia este Team ID** - lo necesitarás más tarde

#### 5.4. Crear Pass Type ID

1. Ve a [developer.apple.com/account/resources/identifiers](https://developer.apple.com/account/resources/identifiers)
2. Haz clic en el botón **"+"** (azul) para crear nuevo
3. Selecciona **"Pass Type IDs"** y haz clic en **"Continue"**

4. **Configurar el Pass Type ID**:
   ```
   📝 Description: "Pases de Eventos" (o el nombre que prefieras)
   🆔 Identifier: pass.com.TUDOMINIO.eventos
   
   Ejemplos de identificadores:
   - pass.com.miempresa.eventos
   - pass.com.juan.eventos  
   - pass.com.ejemplo.eventos
   ```
   
   ⚠️ **IMPORTANTE**: El identificador debe seguir el formato `pass.com.algo.algo` y **ser único globalmente**.

5. Haz clic en **"Register"**
6. **Guarda el identificador** que acabas de crear - lo necesitarás en el `.env`

#### 5.5. Crear Certificado de Pass Type ID

1. En la lista de Pass Type IDs, haz clic en el que acabas de crear
2. En la sección **"Production Certificates"**, haz clic en **"Create Certificate"**
   
   💡 **¿Development o Production?**
   - **Development**: Solo para pruebas durante desarrollo
   - **Production**: Para pases que funcionarán en dispositivos reales
   - **Recomendación**: Usar Production desde el inicio

3. Selecciona **"Production"** y haz clic en **"Continue"**

4. **Subir el archivo CSR**:
   - Haz clic en **"Choose File"**
   - Selecciona el archivo `CertificateSigningRequest.certSigningRequest` que creaste
   - Haz clic en **"Continue"**

5. **Descargar el certificado**:
   - Haz clic en **"Download"**
   - Se descargará un archivo con extensión `.cer`
   - Nombre típico: `pass_type_id_production.cer`

#### 5.6. Instalar y Exportar el Certificado - PASO A PASO

1. **Instalar el certificado en Keychain Access**:
   - Haz **doble clic** en el archivo `.cer` descargado
   - Se abrirá Keychain Access automáticamente
   - El certificado se instalará en tu llavero

2. **Encontrar el certificado instalado**:
   - En Keychain Access, asegúrate de estar en **"login"** (inicio de sesión)
   - Ve a la categoría **"My Certificates"** (Mis certificados)
   - Busca un certificado que diga algo como:
     ```
     "Pass Type ID: pass.com.tudominio.eventos"
     ```

3. **Verificar que el certificado tenga clave privada**:
   - Haz clic en la flecha al lado del certificado para expandirlo
   - **DEBE aparecer una clave privada** debajo del certificado
   - Si no ves la clave privada, algo salió mal en el proceso del CSR

4. **Exportar a formato .p12 - PASO CRÍTICO**:

   **4.1. Preparar para exportar**:
   - En Keychain Access, asegúrate de estar en la categoría **"My Certificates"**
   - Localiza tu certificado (debe decir "Pass Type ID: pass.com.tudominio.eventos")
   - **MUY IMPORTANTE**: Haz clic derecho sobre **EL CERTIFICADO** (la línea principal)
   - **NO hagas clic** sobre la clave privada que aparece debajo

   **4.2. Iniciar exportación**:
   - Selecciona **"Export"** (Exportar) del menú contextual
   - Si no aparece la opción "Export", significa que has hecho clic en el lugar equivocado

   **4.3. Configurar el archivo de exportación**:
   ```
   📁 Save As: wallet-pass-certificate
   📍 Where: Escritorio (para encontrarlo fácilmente)
   📄 File Format: Personal Information Exchange (.p12)
   ```
   
   **⚠️ IMPORTANTE**: Asegúrate de que el formato sea **.p12**, no .cer ni .pem

   **4.4. Guardar archivo**:
   - Haz clic en **"Save"**
   - El sistema iniciará el proceso de exportación

5. **Configurar contraseña del .p12 - CRÍTICO**:

   **5.1. Primera ventana - Contraseña del archivo .p12**:
   - Te aparecerá una ventana pidiendo una contraseña para el archivo .p12
   - **Usa una contraseña segura** (ej: `walletpass2024`)
   - **ANOTA ESTA CONTRASEÑA EN UN LUGAR SEGURO** - la necesitarás en el `.env`
   - Escribe la misma contraseña en "Password" y "Verify"
   - Haz clic en **"OK"**

   **5.2. Segunda ventana - Autorización del sistema**:
   - Te aparecerá otra ventana pidiendo la contraseña de tu **usuario de Mac**
   - Esta es la contraseña que usas para hacer login en tu Mac
   - Escríbela y haz clic en **"Allow"** o **"Permitir"**
   - Esta paso autoriza la exportación de la clave privada

   **5.3. Verificar exportación exitosa**:
   - Deberías ver el archivo `wallet-pass-certificate.p12` en tu Escritorio
   - El archivo debe tener un tamaño de varios KB (no debe estar vacío)

6. **Mover el certificado al proyecto**:

   **6.1. Crear directorio de certificados**:
   ```bash
   # Desde la raíz de tu proyecto Laravel
   mkdir -p storage/certificates
   ```

   **6.2. Copiar el archivo .p12**:
   ```bash
   # Copiar desde el Escritorio al proyecto
   cp ~/Desktop/wallet-pass-certificate.p12 storage/certificates/
   ```

   **6.3. Verificar que el archivo se copió correctamente**:
   ```bash
   # Verificar que existe
   ls -la storage/certificates/wallet-pass-certificate.p12
   
   # Debe mostrar algo como:
   # -rw-r--r--  1 usuario  staff  4567 Dec 15 10:30 wallet-pass-certificate.p12
   ```

   **6.4. Configurar permisos (importante para servidores)**:
   ```bash
   # Dar permisos correctos al archivo
   chmod 644 storage/certificates/wallet-pass-certificate.p12
   ```

#### 5.7. Configurar Variables de Entorno

Edita tu archivo `.env` y agrega estas líneas (reemplaza con tus valores reales):

```env
# Configuración Wallet Pass - REEMPLAZA CON TUS VALORES REALES
MOBILE_PASS_ORGANISATION_NAME="Tu Organización o Nombre"
MOBILE_PASS_TYPE_IDENTIFIER=pass.com.tudominio.eventos
MOBILE_PASS_TEAM_IDENTIFIER=ABC123XYZ
MOBILE_PASS_APPLE_CERTIFICATE_PATH=/ruta/completa/al/proyecto/storage/certificates/wallet-pass-certificate.p12
MOBILE_PASS_APPLE_CERTIFICATE_PASSWORD=walletpass2024
MOBILE_PASS_APPLE_WEBSERVICE_SECRET=mi-clave-secreta-super-segura
MOBILE_PASS_APPLE_WEBSERVICE_HOST=http://localhost:8000
```

**Donde reemplazar**:
- `Tu Organización o Nombre`: El nombre que aparecerá en los pases
- `pass.com.tudominio.eventos`: El Pass Type ID que creaste en el paso 5.4
- `ABC123XYZ`: Tu Team ID del paso 5.3
- `/ruta/completa/al/proyecto`: La ruta absoluta a tu proyecto Laravel
- `walletpass2024`: La contraseña que pusiste al exportar el .p12
- `mi-clave-secreta-super-segura`: Una clave secreta cualquiera (invéntatela)

#### 5.8. Verificar la Configuración

```bash
# Limpiar cache de configuración
php artisan config:clear

# Verificar que el archivo .p12 existe
ls -la storage/certificates/

# Probar que la configuración es correcta
php artisan tinker
```

En tinker, ejecuta:
```php
config('mobile-pass.apple.certificate_path')
config('mobile-pass.apple.certificate_password')
config('mobile-pass.type_identifier')
config('mobile-pass.team_identifier')
exit
```

Si todo está bien configurado, deberías ver tus valores sin errores.

#### 🚨 Problemas Comunes y Soluciones

**Error: "file_get_contents(): Failed to open stream"**
- ✅ Verifica que la ruta del certificado sea **absoluta** (empezar con `/`)
- ✅ Verifica que el archivo `.p12` exista en la ubicación especificada
- ✅ Verifica permisos del archivo: `chmod 644 storage/certificates/*.p12`

**Error: "Unable to load certificate"**
- ✅ Verifica que la contraseña del .p12 sea correcta
- ✅ Regenera el certificado si es necesario

**El pase se descarga pero no se abre en iPhone**
- ✅ Verifica que uses certificado de **Production**
- ✅ Verifica que el Pass Type ID coincida exactamente
- ✅ Verifica que el Team ID sea correcto

**No veo la clave privada en Keychain Access**
- ✅ Regenera el CSR desde el mismo Mac donde vas a exportar
- ✅ Asegúrate de seleccionar "Let me specify key pair information"

### Paso 6: Crear Iconos Requeridos

Los pases de Apple Wallet requieren iconos específicos. Puedes crearlos manualmente o usar el script de Python incluido:

#### Opción A: Script Automático (Python)
```bash
# Instalar Python y Pillow
pip3 install Pillow

# Ejecutar el script
python3 scripts/generate_icons.py
```

#### Opción B: Manual
Crea estos archivos en `public/images/icons/`:
- `icon.png` (58x58 px)
- `icon@2x.png` (116x116 px)  
- `icon@3x.png` (261x261 px)

### Paso 7: Limpiar Caché de Configuración

```bash
php artisan config:clear
php artisan cache:clear
```

### Paso 8: Iniciar el Servidor

```bash
php artisan serve
```

O para acceso desde otros dispositivos en tu red local:

```bash
php artisan serve --host=0.0.0.0 --port=8080
```

---

## 🔧 Opción B: Instalación Solo de la Biblioteca

Si ya tienes un proyecto Laravel y solo quieres añadir la funcionalidad de pases:

### Paso 1: Instalar el Paquete

```bash
composer require spatie/laravel-mobile-pass
```

### Paso 2: Publicar Configuración y Migraciones

```bash
# Publicar archivo de configuración
php artisan vendor:publish --tag=mobile-pass-config

# Publicar migraciones
php artisan vendor:publish --tag=mobile-pass-migrations

# Ejecutar migraciones
php artisan migrate
```

### Paso 3: Configurar Variables de Entorno

Añade estas variables a tu archivo `.env`:

```env
# Configuración Wallet Pass
MOBILE_PASS_ORGANISATION_NAME="Tu Organización"
MOBILE_PASS_TYPE_IDENTIFIER=pass.com.tudominio.eventos
MOBILE_PASS_TEAM_IDENTIFIER=TU_TEAM_ID
MOBILE_PASS_APPLE_CERTIFICATE_PATH=/ruta/completa/al/certificado.p12
MOBILE_PASS_APPLE_CERTIFICATE_PASSWORD=tu_password_del_p12
MOBILE_PASS_APPLE_WEBSERVICE_SECRET=clave-secreta-para-webservice
MOBILE_PASS_APPLE_WEBSERVICE_HOST=http://tu-dominio.com
```

### Paso 4: Crear Controlador Básico

Crea un controlador para manejar los pases:

```bash
php artisan make:controller PassController
```

Ejemplo de controlador básico:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\LaravelMobilePass\Builders\GenericPassBuilder;
use Spatie\LaravelMobilePass\Entities\FieldContent;
use Spatie\LaravelMobilePass\Entities\Image;

class PassController extends Controller
{
    public function createEventPass(Request $request)
    {
        $eventPass = GenericPassBuilder::make()
            ->setOrganisationName('EVENT PASS')
            ->setSerialNumber('EVENT-' . strtoupper(substr(md5(uniqid()), 0, 8)))
            ->setDescription($request->input('nombre'))
            ->setHeaderFields(
                FieldContent::make('status')
                    ->withLabel('✨ STATUS')
                    ->withValue('PREMIUM ACCESS')
            )
            ->setPrimaryFields(
                FieldContent::make('event-name')
                    ->withLabel('🎯 EVENT')
                    ->withValue(strtoupper($request->input('nombre')))
            )
            ->setSecondaryFields(
                FieldContent::make('date')
                    ->withLabel('📅 WHEN')
                    ->withValue($request->input('fecha')),
                FieldContent::make('time')
                    ->withLabel('🕐 TIME')
                    ->withValue($request->input('hora'))
            )
            ->setAuxiliaryFields(
                FieldContent::make('venue')
                    ->withLabel('📍 WHERE')
                    ->withValue($request->input('lugar'))
            )
            ->setIconImage(
                Image::make(
                    x1Path: public_path('images/icons/icon.png'),
                    x2Path: public_path('images/icons/icon@2x.png'), 
                    x3Path: public_path('images/icons/icon@3x.png')
                )
            )
            ->save();

        return $eventPass->download('event-pass');
    }
}
```

### Paso 5: Configurar Rutas

Añade rutas en `routes/web.php`:

```php
use App\Http\Controllers\PassController;

Route::post('/create-pass', [PassController::class, 'createEventPass'])->name('create.pass');
```

### Paso 6: Crear Vista (Opcional)

Crea una vista básica en `resources/views/create-pass.blade.php`:

```html
<form action="{{ route('create.pass') }}" method="POST">
    @csrf
    <input type="text" name="nombre" placeholder="Nombre del evento" required>
    <input type="text" name="lugar" placeholder="Lugar" required>
    <input type="date" name="fecha" required>
    <input type="time" name="hora" required>
    <button type="submit">Crear Pase</button>
</form>
```

---

## 🚀 Uso

1. Abre tu navegador y ve a `http://localhost:8000` (o la URL de tu servidor)
2. Rellena el formulario con los datos del evento:
   - **Nombre del Evento**: Título del evento
   - **Lugar**: Ubicación donde se realizará
   - **Fecha**: Fecha del evento
   - **Hora**: Hora de inicio
   - **Descripción**: Breve descripción del evento
3. Haz clic en **"Crear y Descargar Pase"**
4. El archivo `.pkpass` se descargará automáticamente
5. Si abres el archivo en un iPhone, se agregará automáticamente a Apple Wallet

## 📱 Probar en iPhone

Para probar los pases en tu iPhone:

1. Asegúrate de que tu iPhone esté en la misma red WiFi que tu servidor
2. Obtén la IP local de tu ordenador: `ifconfig | grep inet` (Mac/Linux) o `ipconfig` (Windows)
3. Inicia el servidor con: `php artisan serve --host=tu.ip.local --port=8080`
4. Accede desde el iPhone a: `http://tu.ip.local:8080`
5. Crea un pase y descárgalo
6. El pase se abrirá automáticamente en Apple Wallet

## 🔧 Personalización

### Cambiar el Diseño del Pase

Edita `app/Http/Controllers/WalletPassController.php` para personalizar:

- **Organización**: Modifica `setOrganisationName()`
- **Campos mostrados**: Ajusta `setHeaderFields()`, `setPrimaryFields()`, `setSecondaryFields()`, `setAuxiliaryFields()`
- **Iconos**: Cambia la ruta en `setIconImage()`

### Agregar Campos Adicionales

Para agregar nuevos campos al formulario:

1. Actualiza la vista en `resources/views/wallet-pass.blade.php`
2. Modifica la validación en el controlador
3. Agrega el campo al pase en el método `createEventPass()`

## 📂 Estructura del Proyecto

```
wallet-pass-project/
├── app/Http/Controllers/WalletPassController.php  # Controlador principal
├── resources/views/wallet-pass.blade.php          # Vista del formulario
├── routes/web.php                                 # Rutas del proyecto
├── storage/certificates/                          # Certificados de Apple
├── public/images/icons/                          # Iconos del pase
├── config/mobile-pass.php                        # Configuración del paquete
└── database/migrations/                          # Migraciones de BD
```

## 🐛 Resolución de Problemas

### Error: "file_get_contents(): Failed to open stream"
- Verifica que la ruta del certificado en `.env` sea absoluta y correcta
- Asegúrate de que el archivo `.p12` exista y tenga permisos de lectura

### Error: "icon.png not found"
- Verifica que los iconos estén en `public/images/icons/`
- Ejecuta el script de generación de iconos

### El pase no se abre en iPhone
- Verifica que todos los certificados estén configurados correctamente
- Asegúrate de que el Pass Type ID coincida con el configurado en Apple Developer
- Comprueba que la fecha del certificado no haya expirado

### Error 500 al crear pases
- Ejecuta `php artisan config:clear`
- Verifica los logs en `storage/logs/laravel.log`
- Asegúrate de que la base de datos esté configurada correctamente

## 📄 Licencia

Este proyecto es de código abierto bajo la licencia MIT.

## 🤝 Contribuir

¡Las contribuciones son bienvenidas! Por favor:

1. Haz fork del proyecto
2. Crea una rama para tu feature (`git checkout -b feature/nueva-caracteristica`)
3. Commit tus cambios (`git commit -am 'Agregar nueva característica'`)
4. Push a la rama (`git push origin feature/nueva-caracteristica`)
5. Abre un Pull Request

## 📥 Descarga

### Para Usuarios Finales

Si solo quieres usar la aplicación sin instalar nada:

#### 🌐 Versión Web (Recomendado)
- **Demo Online**: [tu-dominio.com](https://tu-dominio.com) *(próximamente)*
- **Requiere**: Solo un navegador web moderno
- **Funcionalidades**: Creación y descarga de pases completa
- **Dispositivos**: Compatible con cualquier dispositivo con navegador

#### 📱 Uso Móvil Optimizado
- **Safari en iPhone**: Mejor experiencia para agregar pases directamente
- **Chrome/Firefox**: Funciona perfectamente en cualquier navegador
- **Responsive**: Interfaz adaptada para móviles y tablets

### Para Desarrolladores

#### 🚀 Descarga Rápida del Proyecto
```bash
# Clonar y configurar en menos de 5 minutos
git clone https://github.com/tu-usuario/wallet-pass-project.git
cd wallet-pass-project
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

#### 📦 Solo la Biblioteca
```bash
# Agregar a proyecto existente
composer require spatie/laravel-mobile-pass
```

### Formatos Disponibles

| Método | Formato | Tamaño | Requisitos |
|--------|---------|--------|------------|
| **Código Fuente** | `.zip` | ~2MB | PHP 8.1+, Composer |
| **Solo Biblioteca** | Composer | ~500KB | Laravel 9+ |
| **Docker** | `docker-compose` | ~300MB | Docker instalado |
| **Demo Web** | Navegador | 0MB | Internet |

### Versiones y Compatibilidad

#### ✅ Versiones Soportadas
- **Laravel**: 9.x, 10.x, 11.x
- **PHP**: 8.1, 8.2, 8.3
- **iOS**: 12.0+
- **Apple Wallet**: Todas las versiones actuales

#### 📱 Dispositivos Compatibles
- **iPhone**: iOS 12+ (recomendado iOS 15+)
- **iPad**: iPadOS 13+ 
- **Apple Watch**: watchOS 6+ (herencia automática)
- **Mac**: macOS Big Sur+ (con Apple Silicon)

### Despliegue Rápido

#### 🔧 Desarrollo Local (5 minutos)
```bash
git clone [proyecto] && cd wallet-pass-project
composer install && cp .env.example .env
php artisan key:generate && php artisan serve
```

#### 🌍 Producción (Heroku/DigitalOcean)
- **Heroku**: Deploy con un clic *(próximamente)*
- **DigitalOcean**: Template de App Platform *(próximamente)*
- **Docker**: `docker-compose up` listo para producción

#### ⚡ CDN y Performance
- **Assets optimizados**: CSS/JS minificados
- **Imágenes**: WebP con fallback automático
- **Caché**: Redis/Memcached ready
- **SSL**: Let's Encrypt integrado

### Roadmap de Descargas

#### 🎯 Próximas Funcionalidades
- [ ] **Progressive Web App (PWA)**: Instalable desde navegador
- [ ] **API REST**: Endpoints para integración externa
- [ ] **Plugin WordPress**: Para sitios web existentes
- [ ] **Electron App**: Aplicación de escritorio
- [ ] **CLI Tool**: Generación desde terminal

#### 🔮 Versión 2.0 (Q2 2024)
- [ ] **Multi-tenant**: Múltiples organizaciones
- [ ] **Dashboard Analytics**: Estadísticas de uso
- [ ] **Template Store**: Plantillas prediseñadas
- [ ] **QR Dinámicos**: Códigos actualizables

---

## 📞 Soporte

Si tienes problemas o preguntas:

1. Revisa la sección de **Resolución de Problemas**
2. Consulta la [documentación oficial de Spatie](https://spatie.be/docs/laravel-mobile-pass)
3. Abre un issue en el repositorio

---

**Powered by [Spatie Laravel Mobile Pass](https://spatie.be/docs/laravel-mobile-pass)**
