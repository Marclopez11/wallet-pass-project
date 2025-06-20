# 🎫 Laravel Wallet Pass - Guía de Instalación

Este proyecto está inspirado en [Spatie's Laravel Mobile Pass package](https://spatie.be/docs/laravel-mobile-pass) y te permite generar pases móviles para Apple Wallet y Google Pay.

## 📋 Requisitos Previos

- **PHP 8.2+**
- **Laravel 12+**
- **Composer**
- **Apple Developer Account** (para certificados de wallet pass)

## 🚀 Instalación

### 1. Clonar el repositorio
```bash
git clone <tu-repositorio>
cd wallet-pass-project
```

### 2. Instalar dependencias
```bash
composer install
```

### 3. Configurar el entorno
```bash
cp .env.example .env  # Si no existe .env
php artisan key:generate
```

### 4. Ejecutar migraciones
```bash
php artisan migrate
```

## ⚙️ Configuración

### Variables de Entorno

Agrega estas variables a tu archivo `.env`:

```env
# Configuración básica del pase
MOBILE_PASS_ORGANISATION_NAME="Tu Organización"
MOBILE_PASS_TYPE_IDENTIFIER=pass.com.ejemplo.walletpass
MOBILE_PASS_TEAM_IDENTIFIER=TU_TEAM_ID

# Configuración de certificados Apple (Requerido para producción)
MOBILE_PASS_APPLE_CERTIFICATE_PATH=/ruta/a/tu/certificado.p12
MOBILE_PASS_APPLE_CERTIFICATE_PASSWORD=tu_password

# Alternativamente, puedes usar el contenido del certificado en base64
MOBILE_PASS_APPLE_CERTIFICATE_CONTENTS=contenido_base64_del_certificado

# Configuración del webservice Apple (Opcional)
MOBILE_PASS_APPLE_WEBSERVICE_SECRET=tu_secret_key
MOBILE_PASS_APPLE_WEBSERVICE_HOST=https://tu-dominio.com
```

### Obtener Certificados de Apple

Para generar pases que funcionen en Apple Wallet, necesitas:

1. **Cuenta de Apple Developer** activa
2. **Certificado Pass Type ID** de Apple

#### Pasos para obtener el certificado:

1. **Genera un CSR (Certificate Signing Request)**:
   - Abre "Keychain Access" en macOS
   - Menú → Certificate Assistant → Request a Certificate From a Certificate Authority
   - Guarda el archivo `.certSigningRequest`

2. **Crea un Pass Type ID en Apple Developer Portal**:
   - Ve a [developer.apple.com](https://developer.apple.com)
   - Certificates, Identifiers & Profiles → Identifiers
   - Crea un nuevo Pass Type ID (ej: `pass.com.tuempresa.walletpass`)

3. **Genera el certificado**:
   - En el Pass Type ID, crea un certificado de producción
   - Sube tu archivo CSR
   - Descarga el certificado `.cer`

4. **Exporta a .p12**:
   - Instala el certificado `.cer` en Keychain Access
   - Exporta como `.p12` con contraseña
   - Configura la ruta y contraseña en `.env`

## 🎨 Uso del Proyecto

### Iniciar el servidor
```bash
php artisan serve
```

### Acceder a la aplicación
Visita `http://localhost:8000` para ver la interfaz de demostración.

### Rutas disponibles

| Ruta | Descripción |
|------|-------------|
| `GET /` | Interfaz principal con opciones de creación |
| `GET /wallet-pass` | Lista de pases generados (JSON) |
| `GET /wallet-pass/create/boarding-pass` | Crea y descarga un pase de embarque |
| `GET /wallet-pass/create/coupon` | Crea y descarga un cupón |
| `GET /wallet-pass/create/store-card` | Crea y descarga una tarjeta de fidelidad |
| `GET /wallet-pass/create/event-ticket` | Crea y descarga una entrada de evento |
| `GET /wallet-pass/download/{id}` | Descarga un pase específico |

## 🧪 Ejemplos de Código

### Crear un pase de embarque personalizado

```php
use Spatie\LaravelMobilePass\Builders\AirlinePassBuilder;
use Spatie\LaravelMobilePass\Entities\FieldContent;

$boardingPass = AirlinePassBuilder::make()
    ->setOrganisationName('Mi Aerolínea')
    ->setSerialNumber('VOL' . now()->format('YmdHis'))
    ->setDescription('Vuelo Madrid-Barcelona')
    ->setHeaderFields(
        FieldContent::make('flight')
            ->withLabel('Vuelo')
            ->withValue('IB3000'),
        FieldContent::make('seat')
            ->withLabel('Asiento')
            ->withValue('15A')
    )
    ->setPrimaryFields(
        FieldContent::make('origin')
            ->withLabel('Madrid')
            ->withValue('MAD'),
        FieldContent::make('destination')
            ->withLabel('Barcelona')
            ->withValue('BCN')
    )
    ->save();

// Descargar directamente
return $boardingPass->download('mi-boarding-pass');
```

### Crear un cupón de descuento

```php
use Spatie\LaravelMobilePass\Builders\CouponPassBuilder;

$coupon = CouponPassBuilder::make()
    ->setOrganisationName('Mi Tienda')
    ->setSerialNumber('CUPÓN-' . now()->format('YmdHis'))
    ->setDescription('25% de descuento')
    ->setHeaderFields(
        FieldContent::make('discount')
            ->withLabel('Descuento')
            ->withValue('25% OFF')
    )
    ->setPrimaryFields(
        FieldContent::make('offer')
            ->withLabel('Oferta válida hasta')
            ->withValue(now()->addDays(30)->format('d/m/Y'))
    )
    ->save();
```

## 🔍 Solución de Problemas

### Error: "Certificate not found"
- Verifica que la ruta del certificado sea correcta
- Asegúrate de que el archivo `.p12` existe y tiene los permisos correctos

### Error: "Invalid certificate password"
- Confirma que la contraseña en `.env` sea correcta
- El archivo `.p12` debe tener una contraseña válida

### Los pases no se abren en iPhone
- Verifica que el `MOBILE_PASS_TYPE_IDENTIFIER` coincida con tu Pass Type ID de Apple
- Asegúrate de estar usando un certificado de producción válido

### Error: "Team identifier required"
- Obtén tu Team ID desde Apple Developer Portal
- Configura `MOBILE_PASS_TEAM_IDENTIFIER` en `.env`

## 📚 Recursos Adicionales

- [Documentación oficial de Spatie Laravel Mobile Pass](https://spatie.be/docs/laravel-mobile-pass)
- [Apple Wallet Developer Guide](https://developer.apple.com/wallet/)
- [Apple Pass Design and Creation Guidelines](https://developer.apple.com/design/human-interface-guidelines/wallet/)

## 🤝 Contribuciones

Este proyecto está inspirado en el trabajo de [Spatie](https://spatie.be) y utiliza su excelente paquete Laravel Mobile Pass.

## 📄 Licencia

MIT License - consulta el archivo LICENSE para más detalles. 
