import 'package:flutter/material.dart';

import '../models/user_session.dart';
import '../localization/app_strings.dart';
import '../services/api_client.dart';
import 'crop_library_screen.dart';
import 'diagnosis_capture_screen.dart';
import 'diagnosis_history_screen.dart';
import 'profile_screen.dart';

class FarmerDashboardScreen extends StatefulWidget {
  const FarmerDashboardScreen({
    required this.apiClient,
    required this.session,
    required this.onLogout,
    required this.onProfileUpdated,
    required this.isDarkMode,
    required this.onThemeModeChanged,
    super.key,
  });

  final ApiClient apiClient;
  final UserSession session;
  final Future<void> Function() onLogout;
  final ValueChanged<UserSession> onProfileUpdated;
  final bool isDarkMode;
  final ValueChanged<bool> onThemeModeChanged;

  @override
  State<FarmerDashboardScreen> createState() => _FarmerDashboardScreenState();
}

class _FarmerDashboardScreenState extends State<FarmerDashboardScreen> {
  int _selectedIndex = 0;

  void _selectTab(int index) {
    setState(() {
      _selectedIndex = index;
    });
  }

  @override
  Widget build(BuildContext context) {
    final strings = AppStrings.forLanguage(widget.session.languagePreference);
    final pages = [
      _HomeTab(
        name: widget.session.name,
        onSelectTab: _selectTab,
        strings: strings,
      ),
      DiagnosisCaptureScreen(apiClient: widget.apiClient, showAppBar: false),
      DiagnosisHistoryScreen(apiClient: widget.apiClient, showAppBar: false),
      CropLibraryScreen(apiClient: widget.apiClient, showAppBar: false),
      ProfileScreen(
        apiClient: widget.apiClient,
        fallbackSession: widget.session,
        onLogout: widget.onLogout,
        onProfileUpdated: widget.onProfileUpdated,
        showAppBar: false,
      ),
    ];

    return Scaffold(
      appBar: AppBar(
        title: Text(
          _tabTitle(_selectedIndex, strings),
          style: const TextStyle(fontWeight: FontWeight.w900),
        ),
        actions: [
          IconButton(
            tooltip: widget.isDarkMode
                ? strings.useLightMode
                : strings.useDarkMode,
            onPressed: () => widget.onThemeModeChanged(!widget.isDarkMode),
            icon: Icon(
              widget.isDarkMode
                  ? Icons.light_mode_outlined
                  : Icons.dark_mode_outlined,
            ),
          ),
          PopupMenuButton<String>(
            tooltip: strings.accountMenu,
            itemBuilder: (context) => [
              PopupMenuItem(enabled: false, child: Text(widget.session.name)),
              PopupMenuItem(
                value: 'profile',
                child: Row(
                  children: [
                    const Icon(Icons.person_outline),
                    const SizedBox(width: 8),
                    Text(strings.profile),
                  ],
                ),
              ),
              PopupMenuItem(
                value: 'logout',
                child: Row(
                  children: [
                    const Icon(Icons.logout),
                    const SizedBox(width: 8),
                    Text(strings.logOut),
                  ],
                ),
              ),
            ],
            onSelected: (value) {
              if (value == 'profile') {
                _selectTab(4);
              }

              if (value == 'logout') {
                widget.onLogout();
              }
            },
          ),
        ],
      ),
      body: SafeArea(
        child: IndexedStack(index: _selectedIndex, children: pages),
      ),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _selectedIndex,
        onDestinationSelected: _selectTab,
        destinations: [
          NavigationDestination(
            icon: const Icon(Icons.home_outlined),
            selectedIcon: const Icon(Icons.home),
            label: strings.home,
          ),
          NavigationDestination(
            icon: const Icon(Icons.photo_camera_outlined),
            selectedIcon: const Icon(Icons.photo_camera),
            label: strings.diagnose,
          ),
          NavigationDestination(
            icon: const Icon(Icons.history_outlined),
            selectedIcon: const Icon(Icons.history),
            label: strings.history,
          ),
          NavigationDestination(
            icon: const Icon(Icons.eco_outlined),
            selectedIcon: const Icon(Icons.eco),
            label: strings.crops,
          ),
          NavigationDestination(
            icon: const Icon(Icons.person_outline),
            selectedIcon: const Icon(Icons.person),
            label: strings.profile,
          ),
        ],
      ),
    );
  }

  String _tabTitle(int index, AppStrings strings) {
    return switch (index) {
      1 => strings.startDiagnosis,
      2 => strings.diagnosisHistory,
      3 => strings.cropLibrary,
      4 => strings.profile,
      _ => 'CropDetec',
    };
  }
}

class _HeroPanel extends StatelessWidget {
  const _HeroPanel({required this.name, required this.strings});

  final String name;
  final AppStrings strings;

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    return Container(
      padding: const EdgeInsets.all(22),
      decoration: BoxDecoration(
        color: colorScheme.primaryContainer,
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: colorScheme.outlineVariant),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
            decoration: BoxDecoration(
              color: colorScheme.primary,
              borderRadius: BorderRadius.circular(8),
            ),
            child: Text(
              strings.phaseOne,
              style: TextStyle(
                color: colorScheme.onPrimary,
                fontSize: 12,
                fontWeight: FontWeight.w900,
              ),
            ),
          ),
          const SizedBox(height: 18),
          Text(
            '${strings.hello}, $name.',
            style: Theme.of(context).textTheme.headlineLarge,
          ),
          const SizedBox(height: 14),
          Text(
            strings.homeIntroduction,
            style: Theme.of(context).textTheme.bodyLarge,
          ),
        ],
      ),
    );
  }
}

class _HomeTab extends StatelessWidget {
  const _HomeTab({
    required this.name,
    required this.onSelectTab,
    required this.strings,
  });

  final String name;
  final ValueChanged<int> onSelectTab;
  final AppStrings strings;

  @override
  Widget build(BuildContext context) {
    return ListView(
      padding: const EdgeInsets.fromLTRB(20, 12, 20, 28),
      children: [
        _HeroPanel(name: name, strings: strings),
        const SizedBox(height: 20),
        _QuickActions(onSelectTab: onSelectTab, strings: strings),
      ],
    );
  }
}

class _QuickActions extends StatelessWidget {
  const _QuickActions({required this.onSelectTab, required this.strings});

  final ValueChanged<int> onSelectTab;
  final AppStrings strings;

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        FilledButton.icon(
          onPressed: () => onSelectTab(1),
          icon: const Icon(Icons.photo_camera_outlined),
          label: Text(strings.startDiagnosis),
        ),
        const SizedBox(height: 12),
        OutlinedButton.icon(
          onPressed: () => onSelectTab(2),
          icon: const Icon(Icons.history_outlined),
          label: Text(strings.viewDiagnosisHistory),
        ),
        const SizedBox(height: 12),
        OutlinedButton.icon(
          onPressed: () => onSelectTab(3),
          icon: const Icon(Icons.eco_outlined),
          label: Text(strings.openCropLibrary),
        ),
      ],
    );
  }
}
