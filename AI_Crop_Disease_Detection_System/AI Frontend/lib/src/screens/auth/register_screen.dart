import 'package:flutter/material.dart';

import '../../models/user_session.dart';
import '../../services/api_client.dart';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({
    required this.apiClient,
    required this.onAuthenticated,
    required this.isDarkMode,
    required this.onThemeModeChanged,
    super.key,
  });

  final ApiClient apiClient;
  final ValueChanged<UserSession> onAuthenticated;
  final bool isDarkMode;
  final ValueChanged<bool> onThemeModeChanged;

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _phoneController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _isLoading = false;
  bool _obscurePassword = true;
  String? _errorMessage;
  String _languagePreference = 'en';

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _phoneController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) {
      return;
    }

    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    final result = await widget.apiClient.register(
      name: _nameController.text.trim(),
      email: _emailController.text.trim(),
      phone: _phoneController.text.trim().isEmpty
          ? null
          : _phoneController.text.trim(),
      password: _passwordController.text,
      languagePreference: _languagePreference,
    );

    if (!mounted) {
      return;
    }

    setState(() {
      _isLoading = false;
      _errorMessage = result.errorMessage;
    });

    if (result.data != null) {
      Navigator.of(context).pop();
      widget.onAuthenticated(result.data!);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Create account'),
        actions: [
          IconButton(
            tooltip: widget.isDarkMode ? 'Use light mode' : 'Use dark mode',
            onPressed: () => widget.onThemeModeChanged(!widget.isDarkMode),
            icon: Icon(
              widget.isDarkMode
                  ? Icons.light_mode_outlined
                  : Icons.dark_mode_outlined,
            ),
          ),
        ],
      ),
      body: SafeArea(
        child: ListView(
          padding: const EdgeInsets.fromLTRB(20, 12, 20, 28),
          children: [
            Text(
              'Farmer registration',
              style: Theme.of(context).textTheme.headlineLarge,
            ),
            const SizedBox(height: 10),
            Text(
              'Create an account so diagnosis results can be saved for later review.',
              style: Theme.of(context).textTheme.bodyLarge,
            ),
            const SizedBox(height: 22),
            Form(
              key: _formKey,
              child: Column(
                children: [
                  TextFormField(
                    controller: _nameController,
                    decoration: const InputDecoration(
                      labelText: 'Full name',
                      prefixIcon: Icon(Icons.person_outline),
                    ),
                    validator: _required('Enter your full name.'),
                  ),
                  const SizedBox(height: 14),
                  TextFormField(
                    controller: _emailController,
                    keyboardType: TextInputType.emailAddress,
                    decoration: const InputDecoration(
                      labelText: 'Email address',
                      prefixIcon: Icon(Icons.email_outlined),
                    ),
                    validator: _emailValidator,
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
                    initialValue: _languagePreference,
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
                  const SizedBox(height: 14),
                  TextFormField(
                    controller: _passwordController,
                    obscureText: _obscurePassword,
                    decoration: InputDecoration(
                      labelText: 'Password',
                      prefixIcon: const Icon(Icons.lock_outline),
                      suffixIcon: IconButton(
                        tooltip: _obscurePassword ? 'Show password' : 'Hide password',
                        onPressed: () {
                          setState(() {
                            _obscurePassword = !_obscurePassword;
                          });
                        },
                        icon: Icon(
                          _obscurePassword
                              ? Icons.visibility_outlined
                              : Icons.visibility_off_outlined,
                        ),
                      ),
                    ),
                    validator: _passwordValidator,
                  ),
                  if (_errorMessage != null) ...[
                    const SizedBox(height: 14),
                    _ErrorBanner(message: _errorMessage!),
                  ],
                  const SizedBox(height: 20),
                  FilledButton.icon(
                    onPressed: _isLoading ? null : _submit,
                    icon: _isLoading
                        ? const SizedBox(
                            height: 18,
                            width: 18,
                            child: CircularProgressIndicator(strokeWidth: 2),
                          )
                        : const Icon(Icons.person_add_alt_1_outlined),
                    label: Text(_isLoading ? 'Creating account' : 'Create account'),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _ErrorBanner extends StatelessWidget {
  const _ErrorBanner({required this.message});

  final String message;

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: colorScheme.errorContainer,
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: colorScheme.error),
      ),
      child: Text(
        message,
        style: TextStyle(
          color: colorScheme.onErrorContainer,
          fontWeight: FontWeight.w700,
        ),
      ),
    );
  }
}

String? Function(String?) _required(String message) {
  return (value) => (value?.trim().isEmpty ?? true) ? message : null;
}

String? _emailValidator(String? value) {
  final text = value?.trim() ?? '';

  if (text.isEmpty) {
    return 'Enter your email address.';
  }

  if (!text.contains('@')) {
    return 'Enter a valid email address.';
  }

  return null;
}

String? _passwordValidator(String? value) {
  final text = value ?? '';

  if (text.length < 8) {
    return 'Use at least 8 characters.';
  }

  return null;
}
