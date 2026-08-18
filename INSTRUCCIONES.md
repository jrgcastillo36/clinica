# Cambio de paleta de colores → Teal/Turquesa

## Qué hace este cambio
Tu sistema usa `blue` (578 apariciones) e `indigo` (106 apariciones) como
colores de marca en botones, enlaces, focus rings y navegación, repartidos
en 75 archivos Blade. Los colores `red`, `green`, `emerald`, `amber` y
`purple` son colores de **estado** (éxito, peligro, advertencia, WhatsApp,
etc.) y no se tocan.

Este cambio:
1. Crea una nueva paleta `primary` (teal) en `tailwind.config.js`.
2. Reemplaza automáticamente todas las clases `blue-*` e `indigo-*` por
   `primary-*` en las vistas.

Resultado: para cambiar el color del sistema en el futuro, solo edites
los valores hex en `tailwind.config.js` — no vuelves a tocar vistas.

## Pasos para aplicarlo

1. **Haz un respaldo** (o mejor, trabaja sobre una rama de git):
   ```bash
   git checkout -b cambio-paleta-teal
   ```

2. **Reemplaza tu `tailwind.config.js`** actual por el que te adjunto
   (`tailwind.config.js`), o simplemente agrégale el bloque `colors.primary`
   si ya le hiciste otros cambios personalizados.

3. **Copia `cambiar-colores.sh`** a la raíz de tu proyecto (junto al archivo
   `artisan`) y ejecútalo:
   ```bash
   bash cambiar-colores.sh
   ```
   Esto recorre `resources/views/**/*.blade.php` y cambia `-blue-` / `-indigo-`
   por `-primary-` en más de 500 clases automáticamente.

4. **Reconstruye los assets**:
   ```bash
   npm run build
   ```
   (o `npm run dev` si estás trabajando en local con el servidor corriendo)

5. **Verifica visualmente** estas pantallas clave (son las que más usan el
   color de marca):
   - Login / navegación lateral (sidebar)
   - Botones primarios (`primary-button.blade.php`)
   - Dashboard y calendario de citas
   - Formularios (focus rings al hacer clic en inputs)

6. Si algo se ve mal o quieres revertir un archivo puntual:
   ```bash
   git diff resources/views/ruta/al/archivo.blade.php
   git checkout -- resources/views/ruta/al/archivo.blade.php
   ```

## Ajustar el tono más adelante
Si el teal no queda como esperas, solo edita los valores hex dentro de
`colors.primary` en `tailwind.config.js` — no hace falta tocar ninguna vista.
Por ejemplo, para un teal más oscuro/saturado, baja los números de los
value de `500-700`; para uno más suave, súbelos.

## Nota
Noté que tu `package.json` tiene tanto `tailwindcss` v3 como
`@tailwindcss/vite` v4 instalados, pero el proyecto está configurado y
funcionando con **Tailwind v3** (usa `tailwind.config.js` + `postcss.config.js`).
El paquete `@tailwindcss/vite` no se está usando actualmente — no afecta este
cambio, pero si más adelante migras a Tailwind v4 el enfoque de temas cambia
(se hace con `@theme` en el CSS en vez de `tailwind.config.js`). Si quieres,
puedo ayudarte con eso en otro momento.
