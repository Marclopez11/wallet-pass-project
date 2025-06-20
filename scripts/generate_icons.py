#!/usr/bin/env python3
"""
Script para generar iconos modernos para Apple Wallet Pass
Genera automáticamente icon.png, icon@2x.png e icon@3x.png
Con diseño moderno y minimalista para pases premium

Requiere: pip install Pillow
"""

import os
from PIL import Image, ImageDraw, ImageFont

def create_icon(size, filename):
    """Crea un icono moderno y minimalista"""
    # Colores modernos y elegantes
    primary_color = "#1a1a1a"      # Negro moderno
    accent_color = "#007AFF"       # Azul Apple
    white = "#FFFFFF"              # Blanco puro

    # Crear imagen con fondo degradado sutil
    img = Image.new('RGB', (size, size), primary_color)

    # Crear un objeto para dibujar
    draw = ImageDraw.Draw(img)

    # Crear círculo principal con efecto moderno
    circle_margin = size // 5
    circle_coords = [
        circle_margin,
        circle_margin,
        size - circle_margin,
        size - circle_margin
    ]

    # Círculo de fondo blanco
    draw.ellipse(circle_coords, fill=white, outline=accent_color, width=max(2, size//40))

    # Configurar fuente moderna
    try:
        font_size = size // 3
        try:
            # macOS - San Francisco (fuente del sistema Apple)
            font = ImageFont.truetype("/System/Library/Fonts/SF-Pro-Display-Bold.otf", font_size)
        except:
            try:
                # macOS - Fallback Helvetica
                font = ImageFont.truetype("/System/Library/Fonts/Helvetica.ttc", font_size)
            except:
                try:
                    # Linux - Fuente moderna
                    font = ImageFont.truetype("/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf", font_size)
                except:
                    try:
                        # Windows - Segoe UI
                        font = ImageFont.truetype("seguisb.ttf", font_size)
                    except:
                        # Fallback
                        font = ImageFont.load_default()
    except:
        font = ImageFont.load_default()

    # Símbolo moderno "E" con estilo tipográfico
    text = "E"

    # Obtener el tamaño del texto
    bbox = draw.textbbox((0, 0), text, font=font)
    text_width = bbox[2] - bbox[0]
    text_height = bbox[3] - bbox[1]

    # Calcular posición para centrar el texto
    text_x = (size - text_width) // 2
    text_y = (size - text_height) // 2

    # Dibujar el texto con el color primario
    draw.text((text_x, text_y), text, fill=primary_color, font=font)

    # Agregar pequeño detalle accent en la esquina superior derecha
    accent_size = max(4, size // 12)
    accent_margin = max(6, size // 15)
    draw.ellipse([
        size - accent_margin - accent_size,
        accent_margin,
        size - accent_margin,
        accent_margin + accent_size
    ], fill=accent_color)

    return img

def main():
    """Función principal"""
    # Crear directorio de iconos si no existe
    icons_dir = os.path.join("public", "images", "icons")
    os.makedirs(icons_dir, exist_ok=True)

    # Configuración de iconos
    icons_config = [
        (58, "icon.png"),
        (116, "icon@2x.png"),
        (261, "icon@3x.png")  # 87 * 3 para 3x
    ]

    print("🎨 Generando iconos modernos para Event Pass...")

    for size, filename in icons_config:
        print(f"   Creando {filename} ({size}x{size}px)...")

        # Crear el icono
        icon = create_icon(size, filename)

        # Guardar el archivo
        filepath = os.path.join(icons_dir, filename)
        icon.save(filepath, "PNG", optimize=True)

        print(f"   ✅ {filename} guardado en {filepath}")

    print("\n🎉 ¡Iconos modernos generados exitosamente!")
    print(f"📁 Ubicación: {icons_dir}/")
    print("\nIconos creados:")
    for _, filename in icons_config:
        print(f"   • {filename}")

    print("\n💡 Diseño moderno y minimalista:")
    print("   • Fondo negro moderno (#1a1a1a)")
    print("   • Círculo blanco central con borde azul Apple")
    print("   • Letra 'E' con tipografía moderna")
    print("   • Detalle accent azul en esquina superior")
    print("   • Estilo premium compatible con iOS")

if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("\n\n❌ Proceso cancelado por el usuario")
    except Exception as e:
        print(f"\n❌ Error: {e}")
        print("\n💡 Asegúrate de tener Pillow instalado:")
        print("   pip3 install Pillow")
