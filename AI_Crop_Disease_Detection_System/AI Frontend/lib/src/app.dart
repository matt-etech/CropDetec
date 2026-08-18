import 'package:flutter/material.dart';

import 'models/user_session.dart';
import 'screens/auth/login_screen.dart';
import 'screens/farmer_dashboard_screen.dart';
import 'screens/splash_gate.dart';
import 'services/api_client.dart';
import 'theme/app_theme.dart';

class CropDiseaseApp extends StatefulWidget {
  const CropDiseaseApp({super.key});

  @override
  State<CropDiseaseApp> createState() => _CropDiseaseAppState();
}

class _CropDiseaseAppState extends State<CropDiseaseApp> {
  late final ApiClient _apiClient;
  UserSession? _session;
  bool _isCheckingSession = true;
  bool _isDarkMode = false;

  @override
  void initState() {
    super.initState();
    _apiClient = ApiClient();
    _restoreSession();
  }

  Future<void> _restoreSession() async {
    setState(() {
      _session = _apiClient.currentSession;
      _isCheckingSession = false;
    });
  }

  void _handleAuthenticated(UserSession session) {
    setState(() {
      _session = session;
    });
  }

  Future<void> _handleLogout() async {
    await _apiClient.logout();

    if (!mounted) {
      return;
    }

    setState(() {
      _session = null;
    });
  }

  void _handleThemeModeChanged(bool isDarkMode) {
    setState(() {
      _isDarkMode = isDarkMode;
    });
  }

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'CropDetec',
      theme: AppTheme.light,
      darkTheme: AppTheme.dark,
      themeMode: _isDarkMode ? ThemeMode.dark : ThemeMode.light,
      builder: (context, child) {
        return _ThemeBackdrop(
          isDarkMode: _isDarkMode,
          child: child ?? const SizedBox.shrink(),
        );
      },
      home: _isCheckingSession
          ? const SplashGate()
          : _session == null
              ? LoginScreen(
                  apiClient: _apiClient,
                  onAuthenticated: _handleAuthenticated,
                  isDarkMode: _isDarkMode,
                  onThemeModeChanged: _handleThemeModeChanged,
                )
              : FarmerDashboardScreen(
                  apiClient: _apiClient,
                  session: _session!,
                  onLogout: _handleLogout,
                  isDarkMode: _isDarkMode,
                  onThemeModeChanged: _handleThemeModeChanged,
                ),
    );
  }
}

class _ThemeBackdrop extends StatelessWidget {
  const _ThemeBackdrop({
    required this.isDarkMode,
    required this.child,
  });

  final bool isDarkMode;
  final Widget child;

  @override
  Widget build(BuildContext context) {
    return SizedBox.expand(
      child: ColoredBox(
        color: isDarkMode ? Colors.black : Colors.white,
        child: CustomPaint(
          painter: _CheckeredBackdropPainter(isDarkMode: isDarkMode),
          child: child,
        ),
      ),
    );
  }
}

class _CheckeredBackdropPainter extends CustomPainter {
  const _CheckeredBackdropPainter({required this.isDarkMode});

  final bool isDarkMode;

  @override
  void paint(Canvas canvas, Size size) {
    final linePaint = Paint()
      ..color = isDarkMode
          ? Colors.white.withAlpha(12)
          : AppTheme.black.withAlpha(10)
      ..strokeWidth = 1;
    final accentPaint = Paint()
      ..color = AppTheme.green.withAlpha(isDarkMode ? 8 : 7)
      ..strokeWidth = 1;
    const spacing = 32.0;

    for (var x = 0.0; x <= size.width; x += spacing) {
      canvas.drawLine(Offset(x, 0), Offset(x, size.height), linePaint);
    }

    for (var y = 0.0; y <= size.height; y += spacing) {
      canvas.drawLine(Offset(0, y), Offset(size.width, y), linePaint);
    }

    for (var x = spacing; x <= size.width; x += spacing * 2) {
      canvas.drawLine(Offset(x, 0), Offset(x, size.height), accentPaint);
    }

    for (var y = spacing; y <= size.height; y += spacing * 2) {
      canvas.drawLine(Offset(0, y), Offset(size.width, y), accentPaint);
    }
  }

  @override
  bool shouldRepaint(covariant _CheckeredBackdropPainter oldDelegate) {
    return oldDelegate.isDarkMode != isDarkMode;
  }
}
