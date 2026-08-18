# cambiar-colores.ps1
#
# Reemplaza las clases de Tailwind "blue" e "indigo" por "primary" (teal)
# en TODAS las vistas Blade del proyecto. No toca red/green/emerald/amber/
# purple porque esas son colores de estado (exito, peligro, advertencia, etc.)
#
# USO (en la terminal de VS Code, con PowerShell seleccionado):
#   1) Copia este archivo a la raiz de tu proyecto Laravel (junto a artisan)
#   2) Si es la primera vez que ejecutas scripts .ps1, puede que necesites permitirlo:
#        Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
#      (Esto solo aplica para la ventana actual de terminal, no cambia nada global)
#   3) Ejecuta: .\cambiar-colores.ps1

$ErrorActionPreference = "Stop"

$viewsDir = Join-Path (Get-Location) "resources\views"

if (-not (Test-Path $viewsDir)) {
    Write-Host "No se encontro resources\views en $(Get-Location)" -ForegroundColor Red
    Write-Host "Ejecuta este script desde la raiz de tu proyecto Laravel (junto a 'artisan')." -ForegroundColor Red
    exit 1
}

Write-Host "Reemplazando clases blue-* e indigo-* por primary-* en resources\views ..." -ForegroundColor Cyan

$files = Get-ChildItem -Path $viewsDir -Filter "*.blade.php" -Recurse
$count = 0

foreach ($file in $files) {
    $content = Get-Content -Path $file.FullName -Raw -Encoding UTF8

    if ($content -match "-blue-|-indigo-") {
        $newContent = $content -replace "-blue-", "-primary-" -replace "-indigo-", "-primary-"
        Set-Content -Path $file.FullName -Value $newContent -Encoding UTF8 -NoNewline
        $count++
        Write-Host "  OK  $($file.FullName)" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "Listo. Se modificaron $count archivos." -ForegroundColor Cyan
Write-Host ""
Write-Host "Siguientes pasos:"
Write-Host "  1. npm run build     (o 'npm run dev' si estas en desarrollo)"
Write-Host "  2. Revisa visualmente el sistema: login, dashboard, botones, citas"
Write-Host "  3. Si algo se ve mal, revisa con: git diff resources/views"