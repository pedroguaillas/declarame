# Interface Design System — Declárame

## Product Context

SaaS contable para Ecuador. Gestiona empresas contribuyentes, declaraciones SRI, retenciones y RIMPE. Usuarios: contadores o personal de firmas contables durante jornada laboral.

**Feel:** Profesional, preciso, eficiente. Como un archivero bien organizado — denso pero estructurado, confiable.

---

## Direction

- Paleta basada en los tokens del sistema (`--background`, `--foreground`, `--sidebar`, etc.)
- Modo oscuro/claro con detección del sistema como default, persistido en `localStorage`
- Superficie del sidebar separada del canvas pero del mismo matiz (solo varía lightness)
- Header y canvas comparten `bg-background` — no hay "mundo del header"
- Separadores: `border-border` únicamente, sin sombras dramáticas

---

## Dark Mode

- Clase `.dark` en `<html>` — ver `@custom-variant dark (&:is(.dark *))` en `app.css`
- Composable: `resources/js/composables/useColorMode.ts`
  - `initColorMode()` — llamar antes del mount en `app.ts` para evitar flash
  - `useColorMode()` — retorna `{ isDark, toggle }`
- Toggle: botón sol/luna en el header del AdminLayout
- localStorage key: `'color-mode'`

---

## Surfaces & Elevation

Estrategia: **border-only** — sin sombras, solo `border-border`.

| Superficie | Token | Uso |
|---|---|---|
| Canvas | `bg-background` | Fondo de página y header |
| Sidebar | `bg-sidebar` | Panel lateral |
| Cards | `bg-card` | Tarjetas de contenido |
| Inputs | `bg-input` | Controles de formulario |

---

## Typography

Font: **Figtree** (ya configurada en `app.css`)

| Nivel | Uso | Clases |
|---|---|---|
| Primary | Títulos de página | `text-2xl font-semibold text-foreground` |
| Secondary | Etiquetas de sección | `text-sm font-medium text-foreground` |
| Muted | Labels de inputs, metadata | `text-sm text-muted-foreground` |
| Micro | Cabeceras de tabla | `text-xs font-medium uppercase tracking-wider text-muted-foreground` |

---

## Spacing

Base unit: `0.25rem` (1 = 4px). Escala usada:
- Micro: `gap-0.5`, `gap-1` — entre iconos y texto dentro de un elemento
- Componente: `px-3 py-2` — botones y nav items
- Sección: `p-6` — padding de cards y main content
- Mayor: `mb-6` — separación entre bloques

---

## AdminLayout

- Header: `h-14`, `fixed`, `bg-background`, `border-b border-border`
- Sidebar: `w-56`, `fixed top-14`, `bg-sidebar`, `border-r border-border`
- Main: `ml-56 min-h-[calc(100vh-3.5rem)] p-6`
- Nav item activo: `bg-sidebar-accent text-sidebar-accent-foreground`
- Nav item hover: `hover:bg-sidebar-accent/60 hover:text-sidebar-accent-foreground`

---

## Components

### Toggle de tema (header)
```html
<button class="flex h-8 w-8 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-accent hover:text-foreground">
  <!-- SVG sol o luna según dark ref -->
</button>
```

### Separador vertical (header)
```html
<div class="mx-2 h-4 w-px bg-border" />
```

### Botón primario
```html
<button class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90 disabled:opacity-50">
```

### Botón secundario / cancelar
```html
<a class="rounded-md border border-border px-4 py-2 text-sm font-medium text-foreground hover:bg-accent">
```
