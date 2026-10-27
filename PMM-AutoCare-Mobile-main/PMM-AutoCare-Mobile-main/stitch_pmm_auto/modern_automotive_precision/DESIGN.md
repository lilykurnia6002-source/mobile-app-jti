---
name: Modern Automotive Precision
colors:
  surface: '#FFFFFF'
  surface-dim: '#cbdbf5'
  surface-bright: '#f8f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#eff4ff'
  surface-container: '#e5eeff'
  surface-container-high: '#dce9ff'
  surface-container-highest: '#d3e4fe'
  on-surface: '#0b1c30'
  on-surface-variant: '#44474f'
  inverse-surface: '#213145'
  inverse-on-surface: '#eaf1ff'
  outline: '#747780'
  outline-variant: '#c4c6d0'
  surface-tint: '#475e8c'
  primary: '#03224d'
  on-primary: '#ffffff'
  primary-container: '#1f3864'
  on-primary-container: '#8ba2d5'
  inverse-primary: '#afc6fb'
  secondary: '#7b5800'
  on-secondary: '#ffffff'
  secondary-container: '#febf33'
  on-secondary-container: '#6e4f00'
  tertiary: '#361e00'
  on-tertiary: '#ffffff'
  tertiary-container: '#543100'
  on-tertiary-container: '#cc995f'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#d8e2ff'
  primary-fixed-dim: '#afc6fb'
  on-primary-fixed: '#001a41'
  on-primary-fixed-variant: '#2e4673'
  secondary-fixed: '#ffdea5'
  secondary-fixed-dim: '#fbbc30'
  on-secondary-fixed: '#271900'
  on-secondary-fixed-variant: '#5d4200'
  tertiary-fixed: '#ffddbb'
  tertiary-fixed-dim: '#f4bc7f'
  on-tertiary-fixed: '#2b1700'
  on-tertiary-fixed-variant: '#643e0c'
  background: '#f8f9ff'
  on-background: '#0b1c30'
  surface-variant: '#d3e4fe'
  status-pending-bg: '#FEF3C7'
  status-pending-text: '#B45309'
  status-pending-border: '#FDE68A'
  status-progress-bg: '#DBEAFE'
  status-progress-text: '#1D4ED8'
  status-progress-border: '#BFDBFE'
  status-completed-bg: '#DCFCE7'
  status-completed-text: '#15803D'
  status-completed-border: '#BBF7D0'
  status-failed-bg: '#FEE2E2'
  status-failed-text: '#B91C1C'
  status-failed-border: '#FECACA'
  app-bg: '#F8FAFC'
  text-primary: '#0F172A'
  border-subtle: '#E2E8F0'
typography:
  display-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 28px
    fontWeight: '700'
    lineHeight: 36px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
    letterSpacing: -0.015em
  headline-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 20px
    fontWeight: '700'
    lineHeight: 28px
    letterSpacing: -0.01em
  headline-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 18px
    fontWeight: '700'
    lineHeight: 24px
  title-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: '600'
    lineHeight: 22px
  body-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  body-md-medium:
    fontFamily: Plus Jakarta Sans
    fontSize: 14px
    fontWeight: '500'
    lineHeight: 20px
  label-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.02em
  label-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
  currency-display:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: '700'
    lineHeight: 22px
    letterSpacing: -0.01em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  2xs: 0.25rem
  xs: 0.5rem
  sm: 0.75rem
  md: 1rem
  lg: 1.25rem
  xl: 1.5rem
  2xl: 2rem
  container-padding-mobile: 1rem
  card-gap: 0.75rem
  section-gap: 1.5rem
---

# Design System — PMM AUTO Mobile

## Brand Identity
- **Application**: PMM AUTO Mobile (Car workshop service booking & tracking app)
- **Target Audience**: Car owners in Indonesia aged 25-55, needs fast, scannable, crystal-clear information.
- **Tone & Mood**: Professional, reliable, modern, clean, reassuring, high craft.

## Colors
- **Primary / Brand Navy**: `#1F3864` (Main brand color, headers, primary buttons, active tabs)
- **Secondary / Accent Yellow**: `#E5A919` / `#F59E0B` (Partner accent color, highlights, badges, pending status)
- **Background**: `#F8FAFC` (Light neutral slate background)
- **Surface**: `#FFFFFF` (Card background, sheets, modal surfaces)
- **Text Primary**: `#0F172A` (Dark slate titles and headings)
- **Text Secondary**: `#64748B` (Muted gray body and helper text)
- **Border**: `#E2E8F0` (Subtle divider and border lines)

### Status Colors
- **Menunggu (Pending / Yellow)**: Background `#FEF3C7`, Text `#B45309`, Border `#FDE68A`
- **Sedang Dikerjakan / Berjalan (In Progress / Blue)**: Background `#DBEAFE`, Text `#1D4ED8`, Border `#BFDBFE`
- **Selesai (Completed / Green)**: Background `#DCFCE7`, Text `#15803D`, Border `#BBF7D0`
- **Batal / Gagal (Failed / Red)**: Background `#FEE2E2`, Text `#B91C1C`, Border `#FECACA`

## Typography
- **Primary Font**: Plus Jakarta Sans, Inter, system sans-serif
- **Scale**:
  - Display / Big Header: 24px - 28px, Bold
  - Screen Titles: 18px - 20px, Bold (Font Weight: 700)
  - Section Headers: 16px, Semi-Bold (Font Weight: 600)
  - Body Text: 14px, Regular (Font Weight: 400)
  - Captions / Meta: 12px, Medium (Font Weight: 500)
  - Currency Numbers: Bold, tabular numerals (e.g. `Rp475.000`)

## Elevation & Geometry
- **Border Radius**: 12px for cards, inputs, and buttons; 9999px for status pills.
- **Shadows**: Soft subtle cards shadow `0 2px 8px -2px rgba(15, 23, 42, 0.06), 0 1px 3px -1px rgba(15, 23, 42, 0.04)`
- **Dividers**: 1px solid `#E2E8F0`

## Components
- **Primary Button**: Full-width, height 48px-52px, background `#1F3864`, text `#FFFFFF`, rounded 12px, bold font. Hover/active states with slight scale or `#162a4d`.
- **Status Pill**: Small rounded badge (pill shape, padding 4px 10px, text 12px bold) colored by state.
- **Order Card**: Card containing order code, car plate & model, schedule, status pill, and price in Indonesian Rupiah.
- **Step Progress Tracker**: 6-step vertical timeline with completed checkmark indicators, active pulsing state in `#1F3864` or `#E5A919`, and gray pending steps.
- **Bottom Navigation**: 4 tabs (Beranda, Pesanan, Notifikasi, Profil) with icons and labels.
