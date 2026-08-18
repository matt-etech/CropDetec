import 'package:flutter/material.dart';

import '../theme/app_theme.dart';

class SplashGate extends StatelessWidget {
  const SplashGate({super.key});

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
      body: Center(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            _AppMark(),
            SizedBox(height: 18),
            Text(
              'CropDetec',
              style: TextStyle(
                color: AppTheme.green,
                fontSize: 22,
                fontWeight: FontWeight.w900,
              ),
            ),
            SizedBox(height: 16),
            CircularProgressIndicator(color: AppTheme.green),
          ],
        ),
      ),
    );
  }
}

class _AppMark extends StatelessWidget {
  const _AppMark();

  @override
  Widget build(BuildContext context) {
    return Container(
      height: 64,
      width: 64,
      alignment: Alignment.center,
      decoration: BoxDecoration(
        color: AppTheme.green,
        borderRadius: BorderRadius.circular(8),
      ),
      child: const Text(
        'AI',
        style: TextStyle(
          color: Colors.white,
          fontSize: 24,
          fontWeight: FontWeight.w900,
        ),
      ),
    );
  }
}
