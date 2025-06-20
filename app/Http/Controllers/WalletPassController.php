<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\LaravelMobilePass\Builders\GenericPassBuilder;
use Spatie\LaravelMobilePass\Entities\FieldContent;
use Spatie\LaravelMobilePass\Entities\Image;
use Spatie\LaravelMobilePass\Entities\Barcode;
use Spatie\LaravelMobilePass\Enums\BarcodeType;
use Spatie\LaravelMobilePass\Models\MobilePass;

class WalletPassController extends Controller
{
    /**
     * Muestra el formulario para crear un pase de evento
     */
    public function showForm()
    {
        return view('wallet-pass');
    }

    /**
     * Genera un pase de evento con diseño premium profesional
     */
    public function createEventPass(Request $request)
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:500',
            'lugar' => 'required|string|max:255',
            'fecha' => 'required|date',
            'hora' => 'required|string'
        ]);

        // Generar códigos únicos premium
        $serialNumber = 'EVT-' . strtoupper(substr(md5(uniqid()), 0, 6));
        $accessCode = 'ACCESS-' . strtoupper(substr(md5(uniqid() . $validated['nombre']), 0, 8));

        // QR Code con información estructurada
        $qrData = json_encode([
            'event_id' => $serialNumber,
            'event_name' => $validated['nombre'],
            'venue' => $validated['lugar'],
            'date' => $validated['fecha'],
            'time' => $validated['hora'],
            'access_code' => $accessCode,
            'type' => 'premium_event_ticket',
            'issued_at' => now()->toISOString(),
            'valid_until' => \Carbon\Carbon::parse($validated['fecha'] . ' ' . $validated['hora'])->addHours(6)->toISOString()
        ]);

        // Formatear fecha y hora elegantes
        $fechaFormateada = $this->formatearFechaModerna($validated['fecha']);
        $horaFormateada = $this->formatearHoraModerna($validated['hora']);
        $fechaCompleta = $this->formatearFechaCompleta($validated['fecha']);

        // Crear imágenes de fondo premium estáticas si no existen
        $this->crearImagenesEstaticasSiNoExisten();

        // Crear el pase premium con diseño profesional y colores
        $eventPass = GenericPassBuilder::make([
            // COLORES PREMIUM - Fondo blanco a rojo
            'backgroundColor' => '#ffffff', // Fondo blanco
            'foregroundColor' => '#000000', // Texto negro
            'labelColor' => '#c00001', // Rojo para labels
            // QR CODE - Añadir directamente en la configuración
            'barcode' => [
                'format' => BarcodeType::QR->value,
                'message' => $qrData,
                'messageEncoding' => 'utf-8'
            ]
        ])
            ->setOrganisationName('PREMIUM EVENTS')
            ->setSerialNumber($serialNumber)
            ->setDescription($validated['nombre'])
            // HEADER FIELDS - Información superior
            ->setHeaderFields(
                FieldContent::make('tier-status')
                    ->withLabel('NIVEL')
                    ->withValue('ACCESO VIP')
            )
            // PRIMARY FIELD - Título del evento (principal y grande)
            ->setPrimaryFields(
                FieldContent::make('event-title')
                    ->withLabel('EVENTO')
                    ->withValue($this->formatearTituloEvento($validated['nombre']))
            )
            // SECONDARY FIELDS - Nom i cognoms a la izquierda, fecha y hora a la derecha
            ->setSecondaryFields(
                FieldContent::make('attendee-name')
                    ->withLabel('NOMBRE Y APELLIDOS')
                    ->withValue('MARC LOPEZ MARCO'),
                FieldContent::make('event-datetime')
                    ->withLabel('FECHA Y HORA')
                    ->withValue($fechaFormateada . ' - ' . $horaFormateada)
            )
            // AUXILIARY FIELDS - Lloc
            ->setAuxiliaryFields(
                FieldContent::make('venue-name')
                    ->withLabel('LUGAR')
                    ->withValue($this->formatearVenue($validated['lugar']))
            )
            // BACK FIELDS - Información del reverso
            ->setBackFields(
                FieldContent::make('event-overview')
                    ->withLabel('Descripción del Evento')
                    ->withValue($validated['descripcion']),

                FieldContent::make('event-schedule')
                    ->withLabel('Horario del Evento')
                    ->withValue($this->generarHorarioEvento($validated['fecha'], $validated['hora'])),

                FieldContent::make('venue-details')
                    ->withLabel('Información del Lugar')
                    ->withValue($this->generarDetallesVenue($validated['lugar'])),

                FieldContent::make('access-instructions')
                    ->withLabel('Instrucciones de Acceso')
                    ->withValue($this->generarInstruccionesAcceso($accessCode)),

                FieldContent::make('networking-info')
                    ->withLabel('Networking y Beneficios')
                    ->withValue($this->generarBeneficios()),

                FieldContent::make('event-highlights')
                    ->withLabel('Destacados del Evento')
                    ->withValue($this->generarDestacados()),

                FieldContent::make('contact-support')
                    ->withLabel('Soporte del Evento')
                    ->withValue($this->generarSoporte()),

                FieldContent::make('terms-conditions')
                    ->withLabel('Términos y Condiciones')
                    ->withValue($this->generarTerminos()),

                FieldContent::make('social-media')
                    ->withLabel('Conéctate con Nosotros')
                    ->withValue($this->generarRedesSociales($validated['nombre']))
            )
            // IMÁGENES ESTÁTICAS
            ->setIconImage(
                Image::make(
                    x1Path: public_path('images/icons/icon.png'),
                    x2Path: public_path('images/icons/icon@2x.png'),
                    x3Path: public_path('images/icons/icon@3x.png')
                )
            )
            ->setLogoImage(
                Image::make(
                    x1Path: public_path('images/premium/logo.png'),
                    x2Path: public_path('images/premium/logo@2x.png'),
                    x3Path: public_path('images/premium/logo@3x.png')
                )
            )
            ->save();

        // Generar nombre de archivo premium
        $fileName = 'premium-event-' . strtolower(str_replace([' ', '&', 'á', 'é', 'í', 'ó', 'ú', 'ñ'], ['-', 'and', 'a', 'e', 'i', 'o', 'u', 'n'], $validated['nombre'])) . '-' . date('Y-m-d');

        return $eventPass->download($fileName);
    }

    /**
     * Crear imágenes estáticas si no existen
     */
    private function crearImagenesEstaticasSiNoExisten()
    {
        // Crear directorio si no existe
        $premiumDir = public_path('images/premium');
        if (!file_exists($premiumDir)) {
            mkdir($premiumDir, 0755, true);
        }

        $iconsDir = public_path('images/icons');
        if (!file_exists($iconsDir)) {
            mkdir($iconsDir, 0755, true);
        }

        // Solo crear las imágenes si no existen
        if (!file_exists(public_path('images/premium/logo.png'))) {
            $this->generarLogoPremium();
        }

        if (!file_exists(public_path('images/premium/background.png'))) {
            $this->generarFondoPremium();
        }

        if (!file_exists(public_path('images/icons/icon.png'))) {
            $this->generarIconoPremium();
        }
    }

    /**
     * Generar logo premium alargado para ocupar todo el header
     */
    private function generarLogoPremium()
    {
        $logoSizes = [
            ['width' => 320, 'height' => 50, 'suffix' => ''],   // Logo alargado
            ['width' => 640, 'height' => 100, 'suffix' => '@2x'],
            ['width' => 960, 'height' => 150, 'suffix' => '@3x']
        ];

        foreach ($logoSizes as $size) {
            $width = $size['width'];
            $height = $size['height'];
            $suffix = $size['suffix'];

            // Crear imagen con fondo transparente
            $image = imagecreatetruecolor($width, $height);

            // Hacer fondo transparente
            imagesavealpha($image, true);
            $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
            imagefill($image, 0, 0, $transparent);

            // Colores premium
            $white = imagecolorallocate($image, 255, 255, 255);
            $red = imagecolorallocate($image, 192, 0, 1); // #c00001
            $black = imagecolorallocate($image, 0, 0, 0);

            // Crear diseño del logo alargado - barra horizontal elegante
            $centerX = $width / 2;
            $centerY = $height / 2;

            // Barra principal horizontal
            $barHeight = $height * 0.6;
            $barY = $centerY - ($barHeight / 2);

            // Fondo de la barra con gradiente horizontal
            for ($x = 0; $x < $width; $x++) {
                $ratio = $x / $width;
                // Gradiente de rojo a rojo más oscuro
                $r = (int)(192 * (1 - $ratio * 0.3));
                $g = 0;
                $b = (int)(1 * (1 + $ratio * 0.5));

                $gradientColor = imagecolorallocate($image, $r, $g, $b);
                imagefilledrectangle($image, $x, $barY, $x + 1, $barY + $barHeight, $gradientColor);
            }

            // Texto "PREMIUM EVENTS" centrado
            $fontSize = $height * 0.25;
            $text = "PREMIUM EVENTS";

            // Calcular posición centrada del texto
            $textWidth = strlen($text) * ($fontSize * 0.6);
            $textX = $centerX - ($textWidth / 2);
            $textY = $centerY + ($fontSize / 3);

            // Sombra del texto
            imagestring($image, 5, $textX + 1, $textY + 1, $text, $black);
            // Texto principal
            imagestring($image, 5, $textX, $textY, $text, $white);

            // Bordes decorativos
            imagerectangle($image, 10, $barY, $width - 10, $barY + $barHeight, $white);
            imagerectangle($image, 8, $barY - 2, $width - 8, $barY + $barHeight + 2, $red);

            // Guardar imagen
            $filename = public_path("images/premium/logo{$suffix}.png");
            imagepng($image, $filename);
            imagedestroy($image);
        }
    }

    /**
     * Generar fondo premium con gradiente de blanco a #c00001
     */
    private function generarFondoPremium()
    {
        $backgroundSizes = [
            ['width' => 180, 'height' => 220, 'suffix' => ''],
            ['width' => 360, 'height' => 440, 'suffix' => '@2x'],
            ['width' => 540, 'height' => 660, 'suffix' => '@3x']
        ];

        foreach ($backgroundSizes as $size) {
            $width = $size['width'];
            $height = $size['height'];
            $suffix = $size['suffix'];

            $image = imagecreatetruecolor($width, $height);

            // Crear gradiente vertical de blanco a #c00001
            for ($y = 0; $y < $height; $y++) {
                $ratio = $y / $height;

                // Gradiente de blanco (255,255,255) a rojo (#c00001 = 192,0,1)
                $r = (int)(255 * (1 - $ratio) + 192 * $ratio);
                $g = (int)(255 * (1 - $ratio) + 0 * $ratio);
                $b = (int)(255 * (1 - $ratio) + 1 * $ratio);

                $color = imagecolorallocate($image, $r, $g, $b);
                imageline($image, 0, $y, $width, $y, $color);
            }

            // Guardar imagen
            $filename = public_path("images/premium/background{$suffix}.png");
            imagepng($image, $filename);
            imagedestroy($image);
        }
    }

    /**
     * Generar icono premium
     */
    private function generarIconoPremium()
    {
        $iconSizes = [
            ['width' => 58, 'height' => 58, 'suffix' => ''],
            ['width' => 116, 'height' => 116, 'suffix' => '@2x'],
            ['width' => 174, 'height' => 174, 'suffix' => '@3x']
        ];

        foreach ($iconSizes as $size) {
            $width = $size['width'];
            $height = $size['height'];
            $suffix = $size['suffix'];

            // Crear imagen con fondo transparente
            $image = imagecreatetruecolor($width, $height);

            // Hacer fondo transparente
            imagesavealpha($image, true);
            $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
            imagefill($image, 0, 0, $transparent);

            // Colores premium
            $white = imagecolorallocate($image, 255, 255, 255);
            $red = imagecolorallocate($image, 192, 0, 1); // #c00001

            // Crear diseño del icono - círculo simple
            $centerX = $width / 2;
            $centerY = $height / 2;
            $radius = min($width, $height) * 0.4;

            imagefilledellipse($image, $centerX, $centerY, $radius * 2, $radius * 2, $red);
            imageellipse($image, $centerX, $centerY, $radius * 2, $radius * 2, $white);

            // Guardar imagen
            $filename = public_path("images/icons/icon{$suffix}.png");
            imagepng($image, $filename);
            imagedestroy($image);
        }
    }

    /**
     * Formatear fecha de forma moderna y elegante en español
     */
    private function formatearFechaModerna($fecha)
    {
        $meses = [
            1 => 'ENE', 2 => 'FEB', 3 => 'MAR', 4 => 'ABR',
            5 => 'MAY', 6 => 'JUN', 7 => 'JUL', 8 => 'AGO',
            9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DIC'
        ];

        $dias = [
            'Monday' => 'LUN', 'Tuesday' => 'MAR', 'Wednesday' => 'MIÉ',
            'Thursday' => 'JUE', 'Friday' => 'VIE', 'Saturday' => 'SÁB', 'Sunday' => 'DOM'
        ];

        $fechaObj = \DateTime::createFromFormat('Y-m-d', $fecha);
        $dia = $fechaObj->format('d');
        $mes = $meses[(int)$fechaObj->format('n')];
        $diaSemana = $dias[$fechaObj->format('l')];

        return "{$diaSemana} {$dia} {$mes}";
    }

    /**
     * Formatear fecha completa para el reverso en español
     */
    private function formatearFechaCompleta($fecha)
    {
        $mesesCompletos = [
            1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
            5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
            9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
        ];

        $diasCompletos = [
            'Monday' => 'lunes', 'Tuesday' => 'martes', 'Wednesday' => 'miércoles',
            'Thursday' => 'jueves', 'Friday' => 'viernes', 'Saturday' => 'sábado', 'Sunday' => 'domingo'
        ];

        $fechaObj = \DateTime::createFromFormat('Y-m-d', $fecha);
        $dia = $fechaObj->format('j');
        $mes = $mesesCompletos[(int)$fechaObj->format('n')];
        $año = $fechaObj->format('Y');
        $diaSemana = $diasCompletos[$fechaObj->format('l')];

        return "{$diaSemana}, {$dia} de {$mes} de {$año}";
    }

    /**
     * Formatear hora de forma premium
     */
    private function formatearHoraModerna($hora)
    {
        $horaObj = \DateTime::createFromFormat('H:i', $hora);
        return $horaObj->format('H:i') . 'H';
    }

    /**
     * Calcular hora de apertura de puertas (30 min antes)
     */
    private function calcularHoraPuertas($hora)
    {
        $horaObj = \DateTime::createFromFormat('H:i', $hora);
        $horaObj->modify('-30 minutes');
        return $horaObj->format('H:i') . 'H';
    }

    /**
     * Formatear título del evento para máximo impacto
     */
    private function formatearTituloEvento($nombre)
    {
        // Convertir a mayúsculas solo si es corto, sino usar title case elegante
        if (strlen($nombre) <= 25) {
            return strtoupper($nombre);
        }

        return $this->toTitleCase($nombre);
    }

    /**
     * Convertir a Title Case elegante
     */
    private function toTitleCase($string)
    {
        return ucwords(strtolower($string));
    }

    /**
     * Formatear venue de forma elegante
     */
    private function formatearVenue($lugar)
    {
        // Si es muy largo, truncar inteligentemente
        if (strlen($lugar) > 30) {
            $palabras = explode(' ', $lugar);
            $resultado = '';

            foreach ($palabras as $palabra) {
                if (strlen($resultado . ' ' . $palabra) <= 30) {
                    $resultado .= ($resultado ? ' ' : '') . $palabra;
                } else {
                    break;
                }
            }

            return $resultado . '...';
        }

        return $lugar;
    }

    /**
     * Generar horario detallado del evento - Verano 2025
     */
    private function generarHorarioEvento($fecha, $hora)
    {
        $fechaCompleta = $this->formatearFechaCompleta($fecha);
        $horaInicio = \DateTime::createFromFormat('H:i', $hora);
        $horaPuertas = clone $horaInicio;
        $horaPuertas->modify('-30 minutes');
        $horaFin = clone $horaInicio;
        $horaFin->modify('+4 hours');

        return "{$fechaCompleta}\n\n" .
               "Apertura de puertas: {$horaPuertas->format('H:i')}\n" .
               "Inicio del evento: {$horaInicio->format('H:i')}\n" .
               "Finalización estimada: {$horaFin->format('H:i')}\n\n" .
               "Por favor, llega 15-30 minutos antes para el registro de entrada\n\n" .
               "FESTIVAL DE VERANO 2025\n" .
               "La mejor experiencia del verano está aquí";
    }

    /**
     * Generar detalles del venue - Verano 2025
     */
    private function generarDetallesVenue($lugar)
    {
        return "Lugar: {$lugar}\n\n" .
               "Aparcamiento: Disponible en el recinto\n" .
               "Transporte público: Accesible por metro y autobús\n" .
               "Accesibilidad: Acceso completo para sillas de ruedas\n" .
               "WiFi: Internet de alta velocidad gratuito\n" .
               "Catering: Refrescos premium incluidos\n" .
               "Zona chill-out: Área de descanso con sombra\n" .
               "Temperaturas de verano: Se recomienda ropa ligera";
    }

    /**
     * Generar instrucciones de acceso - Verano 2025
     */
    private function generarInstruccionesAcceso($accessCode)
    {
        return "Presenta este pase digital en la entrada\n" .
               "El código QR contiene datos de acceso encriptados\n" .
               "Código de acceso: {$accessCode}\n\n" .
               "IMPORTANTE:\n" .
               "• Las capturas de pantalla NO son válidas\n" .
               "• El pase no es transferible\n" .
               "• Se puede requerir identificación válida\n" .
               "• Los pases perdidos se pueden recuperar por email\n" .
               "• Entrada válida solo para el Festival de Verano 2025";
    }

    /**
     * Generar beneficios y networking - Verano 2025
     */
    private function generarBeneficios()
    {
        return "EL ACCESO VIP INCLUYE:\n\n" .
               "Recepción de bienvenida con catering premium\n" .
               "Merchandising exclusivo del evento\n" .
               "Pack digital de recursos y grabaciones\n" .
               "Oportunidades prioritarias de networking\n" .
               "Fotografía profesional del evento\n" .
               "Certificado de asistencia\n" .
               "Acceso a meet & greet con ponentes\n" .
               "Área VIP con aire acondicionado\n" .
               "Bebidas refrescantes ilimitadas";
    }

    /**
     * Generar destacados del evento - Festival Verano 2025
     */
    private function generarDestacados()
    {
        return "DESTACADOS PRINCIPALES:\n\n" .
               "Conferencias magistrales de líderes de la industria\n" .
               "Talleres interactivos y sesiones prácticas\n" .
               "Demostraciones de productos en vivo\n" .
               "Mesas redondas con expertos reconocidos\n" .
               "Escaparate de innovación y presentaciones de startups\n" .
               "Networking global con más de 500 asistentes\n" .
               "App móvil con agenda y funciones de networking\n" .
               "Zona de relajación con música chill-out\n" .
               "Actividades especiales de verano al aire libre";
    }

    /**
     * Generar información de soporte - Verano 2025
     */
    private function generarSoporte()
    {
        return "SOPORTE DEL EVENTO 24/7:\n\n" .
               "Email: soporte@eventosverano2025.com\n" .
               "WhatsApp: +34 900 123 456\n" .
               "Chat en vivo: Disponible en la app del evento\n" .
               "Presencial: Punto de información en entrada principal\n\n" .
               "CONTACTO DE EMERGENCIA:\n" .
               "Línea de emergencia: +34 900 999 000\n" .
               "Disponible durante las horas del evento\n\n" .
               "FESTIVAL DE VERANO 2025\n" .
               "Tu bienestar es nuestra prioridad";
    }

    /**
     * Generar términos y condiciones - Verano 2025
     */
    private function generarTerminos()
    {
        return "TÉRMINOS Y CONDICIONES:\n\n" .
               "• El ticket otorga acceso únicamente al evento especificado\n" .
               "• El organizador se reserva el derecho de rechazar la entrada\n" .
               "• Pueden realizarse fotografías/grabaciones durante el evento\n" .
               "• No se permite comida/bebida del exterior\n" .
               "• El programa del evento puede cambiar sin previo aviso\n" .
               "• No hay reembolsos por no presentarse o salir temprano\n" .
               "• El asistente asume todos los riesgos de participación\n" .
               "• Evento válido solo para Festival de Verano 2025\n\n" .
               "Términos completos disponibles en:\n" .
               "www.eventosverano2025.com/terminos";
    }

    /**
     * Generar redes sociales - Verano 2025
     */
    private function generarRedesSociales($nombreEvento)
    {
        $hashtag = '#' . str_replace([' ', '&', '-'], '', $nombreEvento) . 'Verano2025';

        return "COMPARTE TU EXPERIENCIA:\n\n" .
               "Hashtag oficial: {$hashtag}\n" .
               "Twitter: @EventosVerano2025\n" .
               "Facebook: /EventosVerano2025Oficial\n" .
               "Instagram: @eventos_verano_2025\n" .
               "LinkedIn: /company/eventos-verano-2025\n" .
               "YouTube: /EventosVeranoChannel\n" .
               "TikTok: @eventosverano2025\n\n" .
               "¡Etiquétanos para tener la oportunidad de aparecer destacado!\n" .
               "¡Vive el verano de 2025 al máximo!";
    }

    /**
     * Descarga un pase específico
     */
    public function downloadPass(MobilePass $mobilePass, string $fileName = null)
    {
        $downloadName = $fileName ?: ('premium-event-pass-' . time());

        return $mobilePass->download($downloadName);
    }

    /**
     * Lista todos los pases creados
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $passes = MobilePass::latest()->get()->map(function ($pass) {
            return [
                'id' => $pass->id,
                'type' => $pass->type,
                'description' => $pass->content['description'] ?? 'Sin descripción',
                'created_at' => $pass->created_at->format('d/m/Y H:i'),
                'download_url' => route('wallet-pass.download', $pass)
            ];
        });

        return response()->json([
            'passes' => $passes,
            'total' => $passes->count()
        ]);
    }
}
