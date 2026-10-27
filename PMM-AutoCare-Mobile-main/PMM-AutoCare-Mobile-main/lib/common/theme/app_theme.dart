import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

/// Tokens desain Stitch untuk PMM AUTO Mobile ("Modern Automotive Precision")
class AppColors {
  AppColors._();

  // Brand Navy
  static const primary = Color(0xFF03224D);
  static const primaryContainer = Color(0xFF1F3864);
  static const onPrimary = Color(0xFFFFFFFF);
  static const onPrimaryContainer = Color(0xFF8BA2D5);

  // Brand Accent Yellow
  static const secondary = Color(0xFF7B5800);
  static const secondaryContainer = Color(0xFFFEBF33);
  static const onSecondaryContainer = Color(0xFF6E4F00);
  static const secondaryFixed = Color(0xFFFFDEA5);
  static const secondaryFixedDim = Color(0xFFFBBC30);

  // Surfaces & Backgrounds
  static const background = Color(0xFFF8F9FF);
  static const appBg = Color(0xFFF8FAFC);
  static const surface = Color(0xFFFFFFFF);
  static const surfaceContainerLowest = Color(0xFFFFFFFF);
  static const surfaceContainerLow = Color(0xFFEFF4FF);
  static const surfaceContainer = Color(0xFFE5EEFF);
  static const surfaceContainerHigh = Color(0xFFDCE9FF);
  static const surfaceContainerHighest = Color(0xFFD3E4FE);

  // Text & Outlines
  static const textPrimary = Color(0xFF0F172A);
  static const onSurface = Color(0xFF0B1C30);
  static const onSurfaceVariant = Color(0xFF44474F);
  static const textMuted = Color(0xFF64748B);
  static const outline = Color(0xFF747780);
  static const outlineVariant = Color(0xFFC4C6D0);
  static const borderSubtle = Color(0xFFE2E8F0);
  static const focusRing = secondaryContainer;
  static const disabled = Color(0xFFE2E8F0);
  static const onDisabled = Color(0xFF747780);

  // Status Badges
  static const statusPendingBg = Color(0xFFFEF3C7);
  static const statusPendingText = Color(0xFFB45309);
  static const statusPendingBorder = Color(0xFFFDE68A);

  static const statusProgressBg = Color(0xFFDBEAFE);
  static const statusProgressText = Color(0xFF1D4ED8);
  static const statusProgressBorder = Color(0xFFBFDBFE);

  static const statusCompletedBg = Color(0xFFDCFCE7);
  static const statusCompletedText = Color(0xFF15803D);
  static const statusCompletedBorder = Color(0xFFBBF7D0);

  static const statusFailedBg = Color(0xFFFEE2E2);
  static const statusFailedText = Color(0xFFB91C1C);
  static const statusFailedBorder = Color(0xFFFECACA);
}

class AppSpacing {
  AppSpacing._();

  static const xs = 4.0;
  static const sm = 8.0;
  static const md = 12.0;
  static const lg = 16.0;
  static const xl = 24.0;
  static const xxl = 32.0;
}

class AppRadii {
  AppRadii._();

  static const sm = 8.0;
  static const md = 12.0;
  static const card = 16.0;
  static const hero = 24.0;
  static const pill = 999.0;
}

/// Helper mapping status pesanan ke warna background dan teks
const statusColors = <String, (Color bg, Color text)>{
  'menunggu': (AppColors.statusPendingBg, AppColors.statusPendingText),
  'diterima': (AppColors.statusProgressBg, AppColors.statusProgressText),
  'dikerjakan': (AppColors.statusProgressBg, AppColors.statusProgressText),
  'selesai': (AppColors.statusCompletedBg, AppColors.statusCompletedText),
  'ditolak': (AppColors.statusFailedBg, AppColors.statusFailedText),
  'dibatalkan': (AppColors.statusFailedBg, AppColors.statusFailedText),
};

const statusBorderColors = <String, Color>{
  'menunggu': AppColors.statusPendingBorder,
  'diterima': AppColors.statusProgressBorder,
  'dikerjakan': AppColors.statusProgressBorder,
  'selesai': AppColors.statusCompletedBorder,
  'ditolak': AppColors.statusFailedBorder,
  'dibatalkan': AppColors.statusFailedBorder,
};

ThemeData buildAppTheme() {
  final base = ThemeData(
    useMaterial3: true,
    colorScheme:
        ColorScheme.fromSeed(
          seedColor: AppColors.primaryContainer,
          primary: AppColors.primaryContainer,
          onPrimary: AppColors.onPrimary,
          secondary: AppColors.secondaryContainer,
          onSecondary: AppColors.onSecondaryContainer,
          surface: AppColors.surface,
          onSurface: AppColors.onSurface,
        ).copyWith(
          onSurfaceVariant: AppColors.onSurfaceVariant,
          outline: AppColors.outline,
          outlineVariant: AppColors.outlineVariant,
          error: AppColors.statusFailedText,
          onError: AppColors.onPrimary,
        ),
    scaffoldBackgroundColor: AppColors.background,
    textTheme: GoogleFonts.plusJakartaSansTextTheme().copyWith(
      displayLarge: GoogleFonts.plusJakartaSans(
        fontSize: 28,
        fontWeight: FontWeight.w700,
        letterSpacing: -0.5,
        color: AppColors.textPrimary,
      ),
      headlineLarge: GoogleFonts.plusJakartaSans(
        fontSize: 24,
        fontWeight: FontWeight.w700,
        letterSpacing: -0.3,
        color: AppColors.textPrimary,
      ),
      headlineMedium: GoogleFonts.plusJakartaSans(
        fontSize: 20,
        fontWeight: FontWeight.w700,
        letterSpacing: -0.2,
        color: AppColors.textPrimary,
      ),
      headlineSmall: GoogleFonts.plusJakartaSans(
        fontSize: 18,
        fontWeight: FontWeight.w700,
        color: AppColors.textPrimary,
      ),
      titleLarge: GoogleFonts.plusJakartaSans(
        fontSize: 18,
        fontWeight: FontWeight.w700,
        color: AppColors.textPrimary,
      ),
      titleMedium: GoogleFonts.plusJakartaSans(
        fontSize: 16,
        fontWeight: FontWeight.w600,
        color: AppColors.textPrimary,
      ),
      titleSmall: GoogleFonts.plusJakartaSans(
        fontSize: 14,
        fontWeight: FontWeight.w600,
        color: AppColors.textPrimary,
      ),
      bodyLarge: GoogleFonts.plusJakartaSans(
        fontSize: 16,
        fontWeight: FontWeight.w400,
        color: AppColors.onSurface,
      ),
      bodyMedium: GoogleFonts.plusJakartaSans(
        fontSize: 14,
        fontWeight: FontWeight.w400,
        color: AppColors.onSurfaceVariant,
      ),
      bodySmall: GoogleFonts.plusJakartaSans(
        fontSize: 12,
        fontWeight: FontWeight.w400,
        color: AppColors.onSurfaceVariant,
      ),
      labelLarge: GoogleFonts.plusJakartaSans(
        fontSize: 14,
        fontWeight: FontWeight.w700,
        color: AppColors.onSurface,
      ),
      labelMedium: GoogleFonts.plusJakartaSans(
        fontSize: 12,
        fontWeight: FontWeight.w600,
        letterSpacing: 0.2,
        color: AppColors.onSurfaceVariant,
      ),
      labelSmall: GoogleFonts.plusJakartaSans(
        fontSize: 11,
        fontWeight: FontWeight.w600,
        color: AppColors.outline,
      ),
    ),
  );

  return base.copyWith(
    appBarTheme: base.appBarTheme.copyWith(
      backgroundColor: AppColors.surface,
      foregroundColor: AppColors.onSurface,
      elevation: 0,
      scrolledUnderElevation: 0,
      surfaceTintColor: Colors.transparent,
      centerTitle: false,
      titleTextStyle: GoogleFonts.plusJakartaSans(
        color: AppColors.textPrimary,
        fontSize: 16,
        fontWeight: FontWeight.w600,
      ),
    ),
    filledButtonTheme: FilledButtonThemeData(
      style: FilledButton.styleFrom(
        backgroundColor: AppColors.primaryContainer,
        foregroundColor: AppColors.onPrimary,
        disabledBackgroundColor: AppColors.disabled,
        disabledForegroundColor: AppColors.onDisabled,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppRadii.md),
        ),
        padding: const EdgeInsets.symmetric(
          vertical: AppSpacing.md,
          horizontal: AppSpacing.xl,
        ),
        minimumSize: const Size(0, 48),
        textStyle: GoogleFonts.plusJakartaSans(
          fontWeight: FontWeight.w700,
          fontSize: 14,
        ),
      ),
    ),
    outlinedButtonTheme: OutlinedButtonThemeData(
      style: OutlinedButton.styleFrom(
        foregroundColor: AppColors.primaryContainer,
        side: const BorderSide(color: AppColors.outlineVariant),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppRadii.md),
        ),
        padding: const EdgeInsets.symmetric(
          vertical: AppSpacing.md,
          horizontal: AppSpacing.xl,
        ),
        minimumSize: const Size(0, 48),
        textStyle: GoogleFonts.plusJakartaSans(
          fontWeight: FontWeight.w700,
          fontSize: 14,
        ),
      ),
    ),
    textButtonTheme: TextButtonThemeData(
      style: TextButton.styleFrom(
        foregroundColor: AppColors.primaryContainer,
        minimumSize: const Size(48, 48),
        textStyle: GoogleFonts.plusJakartaSans(
          fontWeight: FontWeight.w700,
          fontSize: 14,
        ),
      ),
    ),
    cardTheme: CardThemeData(
      color: AppColors.surface,
      elevation: 0,
      margin: EdgeInsets.zero,
      surfaceTintColor: Colors.transparent,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(AppRadii.card),
        side: const BorderSide(color: AppColors.borderSubtle, width: 1),
      ),
    ),
    dividerTheme: const DividerThemeData(
      color: AppColors.borderSubtle,
      thickness: 1,
      space: 1,
    ),
    progressIndicatorTheme: const ProgressIndicatorThemeData(
      color: AppColors.primaryContainer,
      linearTrackColor: AppColors.surfaceContainer,
    ),
    inputDecorationTheme: base.inputDecorationTheme.copyWith(
      filled: true,
      fillColor: AppColors.surface,
      contentPadding: const EdgeInsets.symmetric(
        horizontal: AppSpacing.lg,
        vertical: AppSpacing.md,
      ),
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(AppRadii.md),
        borderSide: const BorderSide(color: AppColors.outlineVariant),
      ),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(AppRadii.md),
        borderSide: const BorderSide(color: AppColors.outlineVariant),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(AppRadii.md),
        borderSide: const BorderSide(color: AppColors.focusRing, width: 2),
      ),
      errorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(AppRadii.md),
        borderSide: const BorderSide(color: AppColors.statusFailedText),
      ),
      focusedErrorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(AppRadii.md),
        borderSide: const BorderSide(
          color: AppColors.statusFailedText,
          width: 2,
        ),
      ),
    ),
    snackBarTheme: SnackBarThemeData(
      behavior: SnackBarBehavior.floating,
      backgroundColor: AppColors.primary,
      contentTextStyle: GoogleFonts.plusJakartaSans(
        color: AppColors.onPrimary,
        fontSize: 13,
      ),
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(AppRadii.md),
      ),
    ),
  );
}
