---
name: Botanical Atelier
colors:
  surface: '#fcf9f3'
  surface-dim: '#dcdad4'
  surface-bright: '#fcf9f3'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f6f3ed'
  surface-container: '#f0eee8'
  surface-container-high: '#ebe8e2'
  surface-container-highest: '#e5e2dc'
  on-surface: '#1c1c18'
  on-surface-variant: '#424843'
  inverse-surface: '#31312d'
  inverse-on-surface: '#f3f0ea'
  outline: '#727973'
  outline-variant: '#c1c8c2'
  surface-tint: '#456553'
  primary: '#032517'
  on-primary: '#ffffff'
  primary-container: '#1b3b2b'
  on-primary-container: '#83a590'
  inverse-primary: '#abcfb8'
  secondary: '#536251'
  on-secondary: '#ffffff'
  secondary-container: '#d4e4ce'
  on-secondary-container: '#586755'
  tertiary: '#420d00'
  on-tertiary: '#ffffff'
  tertiary-container: '#5f210d'
  on-tertiary-container: '#e1866a'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#c7ebd4'
  primary-fixed-dim: '#abcfb8'
  on-primary-fixed: '#002113'
  on-primary-fixed-variant: '#2d4d3c'
  secondary-fixed: '#d7e7d1'
  secondary-fixed-dim: '#bbcbb6'
  on-secondary-fixed: '#111f11'
  on-secondary-fixed-variant: '#3c4b3a'
  tertiary-fixed: '#ffdbd0'
  tertiary-fixed-dim: '#ffb59f'
  on-tertiary-fixed: '#3a0a00'
  on-tertiary-fixed-variant: '#76321d'
  background: '#fcf9f3'
  on-background: '#1c1c18'
  surface-variant: '#e5e2dc'
typography:
  display-lg:
    fontFamily: Playfair Display
    fontSize: 3rem
    fontWeight: '600'
    lineHeight: 3.5rem
    letterSpacing: -0.02em
  display-lg-mobile:
    fontFamily: Playfair Display
    fontSize: 2.25rem
    fontWeight: '600'
    lineHeight: 2.75rem
    letterSpacing: -0.01em
  headline-lg:
    fontFamily: Playfair Display
    fontSize: 2.25rem
    fontWeight: '600'
    lineHeight: 2.75rem
    letterSpacing: -0.01em
  headline-lg-mobile:
    fontFamily: Playfair Display
    fontSize: 1.75rem
    fontWeight: '600'
    lineHeight: 2.25rem
  headline-md:
    fontFamily: Playfair Display
    fontSize: 1.75rem
    fontWeight: '500'
    lineHeight: 2.25rem
  headline-sm:
    fontFamily: Playfair Display
    fontSize: 1.25rem
    fontWeight: '500'
    lineHeight: 1.75rem
  body-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 1.125rem
    fontWeight: '400'
    lineHeight: 1.75rem
  body-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 0.9375rem
    fontWeight: '400'
    lineHeight: 1.5rem
  body-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 0.8125rem
    fontWeight: '400'
    lineHeight: 1.25rem
  label-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 0.875rem
    fontWeight: '600'
    lineHeight: 1.25rem
    letterSpacing: 0.01em
  label-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 0.75rem
    fontWeight: '600'
    lineHeight: 1rem
    letterSpacing: 0.04em
  label-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 0.6875rem
    fontWeight: '500'
    lineHeight: 0.875rem
    letterSpacing: 0.05em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  gutter: 1.5rem
  gutter-mobile: 1rem
  margin: 2.5rem
  margin-mobile: 1rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2.5rem
---

## Brand & Style
This design system reflects a curated, high-end botanical atelier aesthetic tailored for artisanal florists, studio managers, and floral designers. The design combines editorial elegance with practical operational clarity. 

Drawing from modern warm minimalism and organic editorial design, the interface balances generous whitespace, tactile cream surfaces, and delicate botanical styling. It evokes feelings of craftsmanship, tranquility, sensory richness, and composed professionalism. Structural lines mimic slender flower stems and architectural greenhouse panes, keeping daily metrics, inventory tiers, and bespoke order logs feeling effortlessly organized and visually restorative.

## Colors
The color palette balances lush foliage, sunlit earth, and soft floral tones:
- **Primary (`#1B3B2B` - Deep Forest Green):** Provides grounding contrast for primary actions, navigational anchors, and primary typography.
- **Secondary (`#8A9A86` - Sage Green):** Used for supporting surfaces, active states, tags, botanical counters, and subtle interactive indicators.
- **Tertiary (`#C26D53` - Terracotta):** An energetic, earthy accent applied to key metrics, critical alerts, stock-low warnings, and seasonal highlight indicators.
- **Neutral (`#F9F6F0` - Warm Sand/Cream):** The canvas background base, steering away from sterile digital whites to offer warmth and paper-like tactile comfort.
- **Accent Soft Blush (`#E8D5CE` / `#F3E8E3`):** Reserved for card tints, soft badges, seasonal flower tags, and hover highlights.

Functional semantic states leverage deep terracotta for warnings/errors and rich laurel tones for success badges, preserving the organic atelier atmosphere without intrusive synthetic neon colors.

## Typography
The typographic system pairs high-contrast serif headlines with a warm, geometric sans-serif for body and navigational elements.
- **Playfair Display** lends an editorial, bespoke boutique presence to dashboard titles, revenue highlights, customer names, and feature cards.
- **Plus Jakarta Sans** ensures legible numbers, order tracking, and interface labels without losing warmth. 
- Use uppercase styling strictly for small metadata tags (`label-md` and `label-sm`) with slight tracking (+0.04em to +0.05em) to establish calm, structured groupings.

## Layout & Spacing
The layout follows a fluid 12-column grid on desktop and tablet screens, folding into a 4-column structure on mobile devices.
- **Desktop (>= 1200px):** 12-column structure with generous `2.5rem` margins and `1.5rem` gutters. Dashboard panels favor asymmetric splits (e.g., an 8-column workspace for stem prep and arrangements flanked by a 4-column stem ledger and delivery calendar).
- **Tablet (768px - 1199px):** 8-column structure with `1.5rem` gutters and margins; sidebar collapses into a floating bottom bar or sliding drawer.
- **Mobile (< 768px):** 4-column layout with `1rem` margins and gutters, stacking multi-metric modules vertically to ensure touch targets remain breathable.
- Spacing follows an organic 8pt harmonic scale, maintaining deliberate room between card sections to evoke uncluttered botanical workbenches.

## Elevation & Depth
Depth is expressed through warm layered planes rather than heavy synthetic drop shadows:
- **Base Canvas:** Deep warm sand/cream (`#F9F6F0`).
- **Surface Tier 1 (Cards & Side Panels):** Crisp warm porcelain (`#FFFFFF`) with ultra-fine botanical hairline borders (`1px solid rgba(138, 154, 134, 0.2)`).
- **Surface Tier 2 (Flyouts, Popovers & Modals):** Tinted soft blush cream (`#FDFCFA`) complemented by an ambient, diffused shadow: `0 12px 32px -4px rgba(27, 59, 43, 0.06), 0 4px 12px -2px rgba(27, 59, 43, 0.03)`.
- **Subtle Floral Borders:** Selected hero metrics and featured arrangement cards carry a decorative dual hairline border—a solid internal frame paired with an outer 1px translucent sage boundary set 3px apart.

## Shapes
A roundedness factor of 2 provides a balanced, organic curve (`0.5rem` on basic items, `1rem` on container cards, and `1.5rem` on focal hero panels). 
- Form inputs, segmented tabs, and buttons feature `0.5rem` (8px) corners to preserve refined geometry.
- Dashboard cards, modal sheets, and data widgets use `1rem` (16px) corners to feel approachable and soft.
- Status badges and avatar stem indicators adopt full pill geometry (`9999px`) to mirror water droplets and smooth river pebbles.

## Components

### Buttons
- **Primary:** Background in Deep Forest Green (`#1B3B2B`), text in Warm Cream (`#F9F6F0`), 8px border radius, subtle hover transition lifting tone to `#244B38`.
- **Secondary / Atelier Outline:** Transparent fill, 1px border in Sage Green (`#8A9A86`), text in `#1B3B2B`. On hover, fills with delicate Rose Blush tint (`#F3E8E3`).
- **Tertiary Accent:** Warm Terracotta (`#C26D53`) with white text, dedicated exclusively to time-sensitive calls to action (e.g., "Confirm Delivery", "Same-day Dispatch").

### Cards & Metric Displays
- Container cards use pure white backgrounds against the cream canvas, bounded by a 1px border in `#8A9A86` at 20% opacity.
- Metric cards showcase Playfair Display numbers paired with an uppercase label in Plus Jakarta Sans (`label-sm`).
- Trend indicators leverage a subtle Sage background chip with forest typography for positive botanical stock, and Terracotta tint chips for spoilage or depleted blooms.

### Chips & Floral Status Badges
- Compact pill-shaped tags used for flower varieties (e.g., "Ranunculus", "Heritage Rose", "Eucalyptus") and order statuses ("Arranging", "Conditioning", "Dispatched").
- Background tints match soft floral washes (e.g., blush `#F3E8E3` for wedding work, sage `#E6EBE5` for standard boutique orders) with high-contrast forest text.

### Input Fields & Controls
- Input fields leverage a warm cream background (`#F4F0E8`), 1px subtle border in `#D4DDD2`, and `0.5rem` corner radius.
- Active focus state is defined by a 1.5px solid ring in Deep Forest Green without harsh outer glow.
- Checkboxes and radios display custom botanical checks inside Forest Green bounds, gently transitioning with a smooth 150ms ease.

### Floral Specifics: Stem Ledger & Inventory Table
- Alternating row zebra tints using transparent sage overlays (`rgba(138, 154, 134, 0.04)`).
- Visual stem counters display thumbnail botanical previews in circular 32px frames with 1px terracotta or sage outer rims indicating hydration and freshness tiers.