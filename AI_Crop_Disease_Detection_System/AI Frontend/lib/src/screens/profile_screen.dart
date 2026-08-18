import 'package:flutter/material.dart';

import '../models/user_session.dart';
import '../services/api_client.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({
    required this.apiClient,
    required this.fallbackSession,
    required this.onLogout,
    this.showAppBar = true,
    super.key,
  });

  final ApiClient apiClient;
  final UserSession fallbackSession;
  final Future<void> Function() onLogout;
  final bool showAppBar;

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _phoneController = TextEditingController();
  late Future<UserSession> _profile;
  String _languagePreference = 'en';
  bool _isSaving = false;
  String? _message;

  @override
  void initState() {
    super.initState();
    _profile = _loadProfile();
  }

  @override
  void dispose() {
    _nameController.dispose();
    _phoneController.dispose();
    super.dispose();
  }

  Future<UserSession> _loadProfile() async {
    final result = await widget.apiClient.me();
    final profile = result.data ?? widget.fallbackSession;

    _nameController.text = profile.name;
    _phoneController.text = profile.phone ?? '';
    _languagePreference = profile.languagePreference;

    return profile;
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) {
      return;
    }

    setState(() {
      _isSaving = true;
      _message = null;
    });

    final result = await widget.apiClient.updateProfile(
      name: _nameController.text.trim(),
      phone: _phoneController.text.trim().isEmpty
          ? null
          : _phoneController.text.trim(),
      languagePreference: _languagePreference,
    );

    if (!mounted) {
      return;
    }

    setState(() {
      _isSaving = false;
      _message = result.isSuccess
          ? 'Profile updated successfully.'
          : result.errorMessage ?? 'Could not update profile.';
      if (result.data != null) {
        _profile = Future.value(result.data);
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    return Scaffold(
      appBar: widget.showAppBar ? AppBar(title: const Text('Profile')) : null,
      body: FutureBuilder<UserSession>(
        future: _profile,
        builder: (context, snapshot) {
          final profile = snapshot.data ?? widget.fallbackSession;

          return Form(
            key: _formKey,
            child: ListView(
              padding: const EdgeInsets.all(20),
              children: [
                Container(
                  padding: const EdgeInsets.all(22),
                  decoration: BoxDecoration(
                    color: colorScheme.primaryContainer,
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(color: colorScheme.outlineVariant),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(profile.email, style: Theme.of(context).textTheme.titleLarge),
                      const SizedBox(height: 8),
                      Text(
                        'Update your farmer profile and preferred language.',
                        style: Theme.of(context).textTheme.bodyLarge,
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 16),
                TextFormField(
                  controller: _nameController,
                  decoration: const InputDecoration(
                    labelText: 'Full name',
                    prefixIcon: Icon(Icons.person_outline),
                  ),
                  validator: (value) =>
                      (value?.trim().isEmpty ?? true) ? 'Enter your name.' : null,
                ),
                const SizedBox(height: 14),
                TextFormField(
                  controller: _phoneController,
                  keyboardType: TextInputType.phone,
                  decoration: const InputDecoration(
                    labelText: 'Phone number',
                    prefixIcon: Icon(Icons.phone_outlined),
                  ),
                ),
                const SizedBox(height: 14),
                DropdownButtonFormField<String>(
                  value: _languagePreference,
                  decoration: const InputDecoration(
                    labelText: 'Preferred language',
                    prefixIcon: Icon(Icons.language_outlined),
                  ),
                  items: const [
                    DropdownMenuItem(value: 'en', child: Text('English')),
                    DropdownMenuItem(value: 'sn', child: Text('Shona')),
                  ],
                  onChanged: (value) {
                    setState(() {
                      _languagePreference = value ?? 'en';
                    });
                  },
                ),
                if (_message != null) ...[
                  const SizedBox(height: 14),
                  Text(
                    _message!,
                    style: TextStyle(
                      color: _message!.contains('success')
                          ? colorScheme.primary
                          : colorScheme.error,
                      fontWeight: FontWeight.w800,
                    ),
                  ),
                ],
                const SizedBox(height: 20),
                FilledButton.icon(
                  onPressed: _isSaving ? null : _save,
                  icon: _isSaving
                      ? const SizedBox(
                          height: 18,
                          width: 18,
                          child: CircularProgressIndicator(strokeWidth: 2),
                        )
                      : const Icon(Icons.save_outlined),
                  label: Text(_isSaving ? 'Saving profile' : 'Save profile'),
                ),
                const SizedBox(height: 12),
                OutlinedButton.icon(
                  onPressed: widget.onLogout,
                  icon: const Icon(Icons.logout),
                  label: const Text('Log out'),
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}
