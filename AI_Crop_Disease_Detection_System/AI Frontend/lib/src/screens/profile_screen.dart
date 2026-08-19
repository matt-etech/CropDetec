import 'package:flutter/material.dart';

import '../models/user_session.dart';
import '../localization/app_strings.dart';
import '../services/api_client.dart';
import '../utils/zimbabwe_phone.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({
    required this.apiClient,
    required this.fallbackSession,
    required this.onLogout,
    required this.onProfileUpdated,
    this.showAppBar = true,
    super.key,
  });

  final ApiClient apiClient;
  final UserSession fallbackSession;
  final Future<void> Function() onLogout;
  final ValueChanged<UserSession> onProfileUpdated;
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
      phone: normalizeZimbabwePhone(_phoneController.text),
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

    if (result.data != null) {
      widget.onProfileUpdated(result.data!);
    }
  }

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    final strings = AppStrings.forLanguage(_languagePreference);

    return Scaffold(
      appBar: widget.showAppBar ? AppBar(title: Text(strings.profile)) : null,
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
                      Text(
                        profile.email,
                        style: Theme.of(context).textTheme.titleLarge,
                      ),
                      const SizedBox(height: 8),
                      Text(
                        strings.profileHint,
                        style: Theme.of(context).textTheme.bodyLarge,
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 16),
                TextFormField(
                  controller: _nameController,
                  decoration: InputDecoration(
                    labelText: strings.fullName,
                    prefixIcon: const Icon(Icons.person_outline),
                  ),
                  validator: (value) => (value?.trim().isEmpty ?? true)
                      ? strings.enterName
                      : null,
                ),
                const SizedBox(height: 14),
                TextFormField(
                  controller: _phoneController,
                  keyboardType: TextInputType.phone,
                  decoration: InputDecoration(
                    labelText: strings.phoneNumber,
                    hintText: '0771234567',
                    prefixIcon: const Icon(Icons.phone_outlined),
                  ),
                  validator: validateZimbabwePhone,
                ),
                const SizedBox(height: 14),
                DropdownButtonFormField<String>(
                  initialValue: _languagePreference,
                  decoration: InputDecoration(
                    labelText: strings.preferredLanguage,
                    prefixIcon: const Icon(Icons.language_outlined),
                  ),
                  items: [
                    DropdownMenuItem(value: 'en', child: Text(strings.english)),
                    DropdownMenuItem(value: 'sn', child: Text(strings.shona)),
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
                  label: Text(
                    _isSaving ? strings.savingProfile : strings.saveProfile,
                  ),
                ),
                const SizedBox(height: 12),
                OutlinedButton.icon(
                  onPressed: widget.onLogout,
                  icon: const Icon(Icons.logout),
                  label: Text(strings.logOut),
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}
