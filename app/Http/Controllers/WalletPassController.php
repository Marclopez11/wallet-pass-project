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

        // Crear el pase premium con diseño profesional
        $eventPass = GenericPassBuilder::make()
            ->setOrganisationName('PREMIUM EVENTS')
            ->setSerialNumber($serialNumber)
            ->setDescription($validated['nombre'])
            ->setHeaderFields(
                FieldContent::make('tier-status')
                    ->withLabel('✨ TIER')
                    ->withValue('VIP ACCESS'),
                FieldContent::make('gate-info')
                    ->withLabel('🚪 GATE')
                    ->withValue('MAIN ENTRANCE')
            )
            ->setPrimaryFields(
                FieldContent::make('event-title')
                    ->withLabel('🎯 EVENT')
                    ->withValue($this->formatearTituloEvento($validated['nombre']))
            )
            ->setSecondaryFields(
                FieldContent::make('event-date')
                    ->withLabel('📅 DATE')
                    ->withValue($fechaFormateada),
                FieldContent::make('event-time')
                    ->withLabel('🕐 TIME')
                    ->withValue($horaFormateada),
                FieldContent::make('doors-open')
                    ->withLabel('🔓 DOORS')
                    ->withValue($this->calcularHoraPuertas($validated['hora']))
            )
            ->setAuxiliaryFields(
                FieldContent::make('venue-name')
                    ->withLabel('📍 VENUE')
                    ->withValue($this->formatearVenue($validated['lugar'])),
                FieldContent::make('ticket-id')
                    ->withLabel('🎫 ID')
                    ->withValue($serialNumber)
            )
            ->setBackFields(
                FieldContent::make('event-overview')
                    ->withLabel('📋 Event Overview')
                    ->withValue($validated['descripcion']),

                FieldContent::make('event-schedule')
                    ->withLabel('⏰ Event Schedule')
                    ->withValue($this->generarHorarioEvento($validated['fecha'], $validated['hora'])),

                FieldContent::make('venue-details')
                    ->withLabel('🏢 Venue Information')
                    ->withValue($this->generarDetallesVenue($validated['lugar'])),

                FieldContent::make('access-instructions')
                    ->withLabel('🎯 Access Instructions')
                    ->withValue($this->generarInstruccionesAcceso($accessCode)),

                FieldContent::make('networking-info')
                    ->withLabel('🤝 Networking & Benefits')
                    ->withValue($this->generarBeneficios()),

                FieldContent::make('event-highlights')
                    ->withLabel('⭐ Event Highlights')
                    ->withValue($this->generarDestacados()),

                FieldContent::make('contact-support')
                    ->withLabel('📞 Event Support')
                    ->withValue($this->generarSoporte()),

                FieldContent::make('terms-conditions')
                    ->withLabel('📜 Terms & Conditions')
                    ->withValue($this->generarTerminos()),

                FieldContent::make('social-media')
                    ->withLabel('📱 Connect With Us')
                    ->withValue($this->generarRedesSociales($validated['nombre']))
            )

            ->setIconImage(
                Image::make(
                    x1Path: public_path('images/icons/icon.png'),
                    x2Path: public_path('images/icons/icon@2x.png'),
                    x3Path: public_path('images/icons/icon@3x.png')
                )
            )
            ->save();

        // Generar nombre de archivo premium
        $fileName = 'premium-event-' . strtolower(str_replace([' ', '&', 'á', 'é', 'í', 'ó', 'ú', 'ñ'], ['-', 'and', 'a', 'e', 'i', 'o', 'u', 'n'], $validated['nombre'])) . '-' . date('Y-m-d');

        return $eventPass->download($fileName);
    }

    /**
     * Formatear fecha de forma moderna y elegante
     */
    private function formatearFechaModerna($fecha)
    {
        $meses = [
            1 => 'JAN', 2 => 'FEB', 3 => 'MAR', 4 => 'APR',
            5 => 'MAY', 6 => 'JUN', 7 => 'JUL', 8 => 'AUG',
            9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DEC'
        ];

        $dias = [
            'Monday' => 'MON', 'Tuesday' => 'TUE', 'Wednesday' => 'WED',
            'Thursday' => 'THU', 'Friday' => 'FRI', 'Saturday' => 'SAT', 'Sunday' => 'SUN'
        ];

        $fechaObj = \DateTime::createFromFormat('Y-m-d', $fecha);
        $dia = $fechaObj->format('d');
        $mes = $meses[(int)$fechaObj->format('n')];
        $diaSemana = $dias[$fechaObj->format('l')];

        return "{$diaSemana} {$dia} {$mes}";
    }

    /**
     * Formatear fecha completa para el reverso
     */
    private function formatearFechaCompleta($fecha)
    {
        $fechaObj = \DateTime::createFromFormat('Y-m-d', $fecha);
        return $fechaObj->format('l, F j, Y');
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
     * Generar horario detallado del evento
     */
    private function generarHorarioEvento($fecha, $hora)
    {
        $fechaCompleta = $this->formatearFechaCompleta($fecha);
        $horaInicio = \DateTime::createFromFormat('H:i', $hora);
        $horaPuertas = clone $horaInicio;
        $horaPuertas->modify('-30 minutes');
        $horaFin = clone $horaInicio;
        $horaFin->modify('+4 hours');

        return "📅 {$fechaCompleta}\n\n" .
               "🚪 Doors Open: {$horaPuertas->format('H:i')}\n" .
               "🎬 Event Starts: {$horaInicio->format('H:i')}\n" .
               "🏁 Expected End: {$horaFin->format('H:i')}\n\n" .
               "⏰ Please arrive 15-30 minutes early for check-in";
    }

    /**
     * Generar detalles del venue
     */
    private function generarDetallesVenue($lugar)
    {
        return "🏢 Venue: {$lugar}\n\n" .
               "🚗 Parking: Available on-site\n" .
               "🚇 Public Transport: Accessible via metro/bus\n" .
               "♿ Accessibility: Full wheelchair access\n" .
               "📶 WiFi: Complimentary high-speed internet\n" .
               "🍽️ Catering: Premium refreshments included";
    }

    /**
     * Generar instrucciones de acceso
     */
    private function generarInstruccionesAcceso($accessCode)
    {
        return "🎫 Present this digital pass at entrance\n" .
               "📱 QR code contains encrypted access data\n" .
               "🔑 Access Code: {$accessCode}\n\n" .
               "⚠️ IMPORTANT:\n" .
               "• Screenshots are NOT valid\n" .
               "• Pass is non-transferable\n" .
               "• Valid ID may be required\n" .
               "• Lost passes can be recovered via email";
    }

    /**
     * Generar beneficios y networking
     */
    private function generarBeneficios()
    {
        return "🌟 VIP ACCESS INCLUDES:\n\n" .
               "🥂 Welcome reception with premium catering\n" .
               "🎁 Exclusive event merchandise\n" .
               "📚 Digital resource pack & recordings\n" .
               "🤝 Priority networking opportunities\n" .
               "📸 Professional event photography\n" .
               "🏆 Certificate of attendance\n" .
               "💼 Access to speaker meet & greet";
    }

    /**
     * Generar destacados del evento
     */
    private function generarDestacados()
    {
        return "⭐ FEATURED HIGHLIGHTS:\n\n" .
               "🎤 Keynote presentations by industry leaders\n" .
               "🛠️ Interactive workshops & hands-on sessions\n" .
               "🚀 Product demos & live demonstrations\n" .
               "🎯 Panel discussions with expert insights\n" .
               "🏅 Innovation showcase & startup pitch\n" .
               "🌐 Global networking with 500+ attendees\n" .
               "📱 Mobile app with agenda & networking";
    }

    /**
     * Generar información de soporte
     */
    private function generarSoporte()
    {
        return "📞 24/7 EVENT SUPPORT:\n\n" .
               "📧 Email: support@premiumevents.com\n" .
               "📱 WhatsApp: +34 900 123 456\n" .
               "💬 Live Chat: Available on event app\n" .
               "🏢 On-site: Information desk at main entrance\n\n" .
               "🆘 EMERGENCY CONTACT:\n" .
               "📞 Emergency Hotline: +34 900 999 000\n" .
               "🚨 Available during event hours";
    }

    /**
     * Generar términos y condiciones
     */
    private function generarTerminos()
    {
        return "📜 TERMS & CONDITIONS:\n\n" .
               "• Ticket grants access to specified event only\n" .
               "• Organizer reserves right to refuse entry\n" .
               "• Photography/recording may occur during event\n" .
               "• Outside food/beverages not permitted\n" .
               "• Event schedule subject to change\n" .
               "• No refunds for no-shows or early departure\n" .
               "• Attendee assumes all risks of participation\n\n" .
               "📋 Full terms available at:\n" .
               "www.premiumevents.com/terms";
    }

    /**
     * Generar redes sociales
     */
    private function generarRedesSociales($nombreEvento)
    {
        $hashtag = '#' . str_replace([' ', '&', '-'], '', $nombreEvento) . '2024';

        return "📱 SHARE YOUR EXPERIENCE:\n\n" .
               "📸 Official Hashtag: {$hashtag}\n" .
               "🐦 Twitter: @PremiumEvents\n" .
               "📘 Facebook: /PremiumEventsOfficial\n" .
               "📷 Instagram: @premium_events\n" .
               "💼 LinkedIn: /company/premium-events\n" .
               "🎥 YouTube: /PremiumEventsChannel\n\n" .
               "🏆 Tag us for a chance to be featured!";
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
