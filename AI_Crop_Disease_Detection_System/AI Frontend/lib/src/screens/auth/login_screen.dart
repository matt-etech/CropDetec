import 'package:flutter/material.dart';

import '../../models/user_session.dart';
import '../../services/api_client.dart';
import 'register_screen.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({
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
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _isLoading = false;
  bool _obscurePassword = true;
  String? _errorMessage;

  @override
  void dispose() {
    _emailController.dispose();
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

    final result = await widget.apiClient.login(
      email: _emailController.text.trim(),
      password: _passwordController.text,
    );

    if (!mounted) {
      return;
    }

    setState(() {
      _isLoading = false;
      _errorMessage = result.errorMessage;
    });

    if (result.data != null) {
      widget.onAuthenticated(result.data!);
    }
  }

  void _openRegister() {
    Navigator.of(context).push(
      MaterialPageRoute(
        builder: (_) => RegisterScreen(
          apiClient: widget.apiClient,
          onAuthenticated: widget.onAuthenticated,
          isDarkMode: widget.isDarkMode,
          onThemeModeChanged: widget.onThemeModeChanged,
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: ListView(
          padding: const EdgeInsets.fromLTRB(20, 28, 20, 28),
          children: [
            Align(
              alignment: Alignment.centerRight,
              child: IconButton(
                tooltip: widget.isDarkMode ? 'Use light mode' : 'Use dark mode',
                onPressed: () => widget.onThemeModeChanged(!widget.isDarkMode),
                icon: Icon(
                  widget.isDarkMode
                      ? Icons.light_mode_outlined
                      : Icons.dark_mode_outlined,
                ),
              ),
            ),
            const SizedBox(height: 8),
            const _AuthHeader(),
            const SizedBox(height: 28),
            Form(
              key: _formKey,
              child: Column(
                children: [
                  TextFormField(
                    controller: _emailController,
                    keyboardType: TextInputType.emailAddress,
                    decoration: const InputDecoration(
                      labelText: 'Email address',
                      prefixIcon: Icon(Icons.email_outlined),
                    ),
                    validator: _requiredEmail,
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
                    validator: _requiredPassword,
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
                        : const Icon(Icons.login),
                    label: Text(_isLoading ? 'Signing in' : 'Sign in'),
                  ),
                  const SizedBox(height: 12),
                  OutlinedButton.icon(
                    onPressed: _isLoading ? null : _openRegister,
                    icon: const Icon(Icons.person_add_alt_1_outlined),
                    label: const Text('Create account'),
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

class _AuthHeader extends StatelessWidget {
  const _AuthHeader();

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
            height: 48,
            width: 48,
            alignment: Alignment.center,
            decoration: BoxDecoration(
              color: colorScheme.primary,
              borderRadius: BorderRadius.circular(8),
            ),
            child: const Text(
              'AI',
              style: TextStyle(
                color: Colors.white,
                fontWeight: FontWeight.w900,
              ),
            ),
          ),
          const SizedBox(height: 18),
          Text('Welcome back', style: Theme.of(context).textTheme.headlineLarge),
          const SizedBox(height: 12),
          Text(
            'Sign in to capture crop images, review diagnosis history, and keep recommendations linked to your account.',
            style: Theme.of(context).textTheme.bodyLarge,
          ),
        ],
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

String? _requiredEmail(String? value) {
  final text = value?.trim() ?? '';

  if (text.isEmpty) {
    return 'Enter your email address.';
  }

  if (!text.contains('@')) {
    return 'Enter a valid email address.';
  }

  return null;
}

String? _requiredPassword(String? value) {
  if ((value ?? '').isEmpty) {
    return 'Enter your password.';
  }

  return null;
}
