import 'package:flutter/material.dart';

import '../models/user_session.dart';
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
    required this.isDarkMode,
    required this.onThemeModeChanged,
    super.key,
  });

  final ApiClient apiClient;
  final UserSession session;
  final Future<void> Function() onLogout;
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
    final pages = [
      _HomeTab(
        name: widget.session.name,
        onSelectTab: _selectTab,
      ),
      DiagnosisCaptureScreen(
        apiClient: widget.apiClient,
        showAppBar: false,
      ),
      DiagnosisHistoryScreen(
        apiClient: widget.apiClient,
        showAppBar: false,
      ),
      CropLibraryScreen(
        apiClient: widget.apiClient,
        showAppBar: false,
      ),
      ProfileScreen(
        apiClient: widget.apiClient,
        fallbackSession: widget.session,
        onLogout: widget.onLogout,
        showAppBar: false,
      ),
    ];

    return Scaffold(
      appBar: AppBar(
        title: Text(
          _tabTitle(_selectedIndex),
          style: const TextStyle(fontWeight: FontWeight.w900),
        ),
        actions: [
          IconButton(
            tooltip: widget.isDarkMode ? 'Use light mode' : 'Use dark mode',
            onPressed: () =>
                widget.onThemeModeChanged(!widget.isDarkMode),
            icon: Icon(
              widget.isDarkMode
                  ? Icons.light_mode_outlined
                  : Icons.dark_mode_outlined,
            ),
          ),
          PopupMenuButton<String>(
            tooltip: 'Account menu',
            itemBuilder: (context) => [
              PopupMenuItem(
                enabled: false,
                child: Text(widget.session.name),
              ),
              const PopupMenuItem(
                value: 'profile',
                child: Row(
                  children: [
                    Icon(Icons.person_outline),
                    SizedBox(width: 8),
                    Text('Profile'),
                  ],
                ),
              ),
              const PopupMenuItem(
                value: 'logout',
                child: Row(
                  children: [
                    Icon(Icons.logout),
                    SizedBox(width: 8),
                    Text('Log out'),
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
        child: IndexedStack(
          index: _selectedIndex,
          children: pages,
        ),
      ),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _selectedIndex,
        onDestinationSelected: _selectTab,
        destinations: const [
          NavigationDestination(
            icon: Icon(Icons.home_outlined),
            selectedIcon: Icon(Icons.home),
            label: 'Home',
          ),
          NavigationDestination(
            icon: Icon(Icons.photo_camera_outlined),
            selectedIcon: Icon(Icons.photo_camera),
            label: 'Diagnose',
          ),
          NavigationDestination(
            icon: Icon(Icons.history_outlined),
            selectedIcon: Icon(Icons.history),
            label: 'History',
          ),
          NavigationDestination(
            icon: Icon(Icons.eco_outlined),
            selectedIcon: Icon(Icons.eco),
            label: 'Crops',
          ),
          NavigationDestination(
            icon: Icon(Icons.person_outline),
            selectedIcon: Icon(Icons.person),
            label: 'Profile',
          ),
        ],
      ),
    );
  }

  String _tabTitle(int index) {
    return switch (index) {
      1 => 'Start diagnosis',
      2 => 'Diagnosis history',
      3 => 'Crop library',
      4 => 'Profile',
      _ => 'CropDetec',
    };
  }
}

class _HeroPanel extends StatelessWidget {
  const _HeroPanel({required this.name});

  final String name;

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
              'PHASE 1',
              style: TextStyle(
                color: colorScheme.onPrimary,
                fontSize: 12,
                fontWeight: FontWeight.w900,
              ),
            ),
          ),
          const SizedBox(height: 18),
          Text(
            'Hello, $name.',
            style: Theme.of(context).textTheme.headlineLarge,
          ),
          const SizedBox(height: 14),
          Text(
            'This first mobile foundation is ready for authentication, API calls, image upload, diagnosis results, history, Shona support, and voice playback.',
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
  });

  final String name;
  final ValueChanged<int> onSelectTab;

  @override
  Widget build(BuildContext context) {
    return ListView(
      padding: const EdgeInsets.fromLTRB(20, 12, 20, 28),
      children: [
        _HeroPanel(name: name),
        const SizedBox(height: 20),
        _QuickActions(onSelectTab: onSelectTab),
      ],
    );
  }
}

class _QuickActions extends StatelessWidget {
  const _QuickActions({required this.onSelectTab});

  final ValueChanged<int> onSelectTab;

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        FilledButton.icon(
          onPressed: () => onSelectTab(1),
          icon: const Icon(Icons.photo_camera_outlined),
          label: const Text('Start diagnosis'),
        ),
        const SizedBox(height: 12),
        OutlinedButton.icon(
          onPressed: () => onSelectTab(2),
          icon: const Icon(Icons.history_outlined),
          label: const Text('View diagnosis history'),
        ),
        const SizedBox(height: 12),
        OutlinedButton.icon(
          onPressed: () => onSelectTab(3),
          icon: const Icon(Icons.eco_outlined),
          label: const Text('Open crop library'),
        ),
      ],
    );
  }
}
