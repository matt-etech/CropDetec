import 'package:flutter/material.dart';

class AppTheme {
  const AppTheme._();

  static const green = Color(0xFF16A34A);
  static const greenLight = Color(0xFFF0FDF4);
  static const greenBorder = Color(0xFFD7E5DC);
  static const black = Color(0xFF111827);
  static const muted = Color(0xFF52615A);

  static const _darkGreen = Color(0xFF22C55E);
  static const _darkBackground = Color(0xFF000000);
  static const _darkSurface = Color(0xFF0B1220);
  static const _darkBorder = Color(0xFF1F2937);
  static const _darkText = Color(0xFFF8FAFC);
  static const _darkMuted = Color(0xFFCBD5E1);

  static ThemeData get light {
    return _buildTheme(
      brightness: Brightness.light,
      primary: green,
      secondary: const Color(0xFF15803D),
      background: Colors.transparent,
      surface: Colors.white,
      surfaceBorder: greenBorder,
      panel: greenLight,
      text: black,
      subduedText: muted,
      buttonText: Colors.white,
    );
  }

  static ThemeData get dark {
    return _buildTheme(
      brightness: Brightness.dark,
      primary: _darkGreen,
      secondary: const Color(0xFF4ADE80),
      background: Colors.transparent,
      surface: _darkSurface,
      surfaceBorder: _darkBorder,
      panel: const Color(0xFF052E16),
      text: _darkText,
      subduedText: _darkMuted,
      buttonText: _darkBackground,
    );
  }

  static ThemeData _buildTheme({
    required Brightness brightness,
    required Color primary,
    required Color secondary,
    required Color background,
    required Color surface,
    required Color surfaceBorder,
    required Color panel,
    required Color text,
    required Color subduedText,
    required Color buttonText,
  }) {
    return ThemeData(
      colorScheme: ColorScheme.fromSeed(
        seedColor: primary,
        brightness: brightness,
        primary: primary,
        secondary: secondary,
        surface: surface,
      ),
      scaffoldBackgroundColor: background,
      appBarTheme: AppBarTheme(
        backgroundColor: background,
        foregroundColor: text,
        elevation: 0,
        centerTitle: false,
      ),
      navigationBarTheme: NavigationBarThemeData(
        backgroundColor: surface,
        indicatorColor: panel,
        labelTextStyle: WidgetStateProperty.all(
          TextStyle(color: text, fontWeight: FontWeight.w700),
        ),
        iconTheme: WidgetStateProperty.resolveWith((states) {
          return IconThemeData(
            color: states.contains(WidgetState.selected) ? primary : subduedText,
          );
        }),
      ),
      cardTheme: CardThemeData(
        color: surface,
        elevation: 0,
        margin: EdgeInsets.zero,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(8),
          side: BorderSide(color: surfaceBorder),
        ),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: surface,
        labelStyle: TextStyle(color: subduedText),
        prefixIconColor: subduedText,
        suffixIconColor: subduedText,
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(8),
          borderSide: BorderSide(color: surfaceBorder),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(8),
          borderSide: BorderSide(color: primary, width: 2),
        ),
        errorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(8),
          borderSide: const BorderSide(color: Color(0xFFF43F5E)),
        ),
        focusedErrorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(8),
          borderSide: const BorderSide(color: Color(0xFFF43F5E), width: 2),
        ),
      ),
      filledButtonTheme: FilledButtonThemeData(
        style: FilledButton.styleFrom(
          backgroundColor: primary,
          foregroundColor: buttonText,
          minimumSize: const Size.fromHeight(52),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
          textStyle: const TextStyle(fontWeight: FontWeight.w800),
        ),
      ),
      outlinedButtonTheme: OutlinedButtonThemeData(
        style: OutlinedButton.styleFrom(
          foregroundColor: primary,
          minimumSize: const Size.fromHeight(52),
          side: BorderSide(color: primary),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
          textStyle: const TextStyle(fontWeight: FontWeight.w800),
        ),
      ),
      textTheme: TextTheme(
        headlineLarge: TextStyle(
          color: primary,
          fontSize: 34,
          fontWeight: FontWeight.w900,
          height: 1.05,
        ),
        titleLarge: TextStyle(
          color: text,
          fontSize: 20,
          fontWeight: FontWeight.w800,
        ),
        bodyLarge: TextStyle(
          color: subduedText,
          fontSize: 16,
          height: 1.5,
        ),
      ),
      useMaterial3: true,
    );
  }
}
