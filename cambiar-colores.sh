#!/usr/bin/env bash
#
# cambiar-colores.sh
#
# Reemplaza las clases de Tailwind "blue" e "indigo" por "primary" (teal)
# en TODAS las vistas Blade del proyecto. No toca red/green/emerald/amber/
# purple porque esas son colores de estado (éxito, peligro, advertencia, etc.)
#
# USO:
#   1) Copia este script a la raíz de tu proyecto Laravel (junto a artisan)
#   2) Ejecuta: bash cambiar-colores.sh
#   3) Revisa los cambios con: git diff  (si usas git)
#
set -e

PROJECT_ROOT="$(pwd)"
VIEWS_DIR="$PROJECT_ROOT/resources/views"

if [ ! -d "$VIEWS_DIR" ]; then
    echo "❌ No se encontró resources/views en $PROJECT_ROOT"
    echo "   Ejecuta este script desde la raíz de tu proyecto Laravel."
    exit 1
fi

echo "🎨 Reemplazando clases blue-* e indigo-* por primary-* en resources/views ..."

# Detecta si es sed de GNU (Linux) o BSD (Mac) para usar la sintaxis correcta de -i
if sed --version >/dev/null 2>&1; then
    SED_INPLACE=(-i)
else
    SED_INPLACE=(-i '')
fi

COUNT=0
while IFS= read -r -d '' file; do
    if grep -qE '\-(blue|indigo)-' "$file"; then
        sed "${SED_INPLACE[@]}" \
            -e 's/-blue-/-primary-/g' \
            -e 's/-indigo-/-primary-/g' \
            "$file"
        COUNT=$((COUNT + 1))
        echo "  ✔ $file"
    fi
done < <(find "$VIEWS_DIR" -name "*.blade.php" -print0)

echo ""
echo "✅ Listo. Se modificaron $COUNT archivos."
echo ""
echo "Siguientes pasos:"
echo "  1. npm run build     (o 'npm run dev' si estás en desarrollo)"
echo "  2. Revisa visualmente el sistema: login, dashboard, botones, citas"
echo "  3. Si algo se ve mal, revisa con: git diff resources/views"
