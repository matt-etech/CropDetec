import 'dart:convert';

import 'package:flutter_secure_storage/flutter_secure_storage.dart';

import '../models/user_session.dart';

abstract class SessionStore {
  UserSession? get currentSession;

  Future<UserSession?> restore();

  Future<void> save(UserSession session);

  Future<void> clear();
}

class InMemorySessionStore implements SessionStore {
  UserSession? _session;

  @override
  UserSession? get currentSession => _session;

  @override
  Future<UserSession?> restore() async => _session;

  @override
  Future<void> save(UserSession session) async {
    _session = session;
  }

  @override
  Future<void> clear() async {
    _session = null;
  }
}

class SecureSessionStore implements SessionStore {
  SecureSessionStore({FlutterSecureStorage? storage})
      : _storage = storage ?? const FlutterSecureStorage();

  static const _sessionKey = 'cropdetec_user_session';

  final FlutterSecureStorage _storage;
  UserSession? _session;

  @override
  UserSession? get currentSession => _session;

  @override
  Future<UserSession?> restore() async {
    final encoded = await _storage.read(key: _sessionKey);

    if (encoded == null || encoded.isEmpty) {
      _session = null;
      return null;
    }

    try {
      final json = jsonDecode(encoded) as Map<String, dynamic>;
      _session = UserSession(
        token: json['token'] as String,
        name: json['name'] as String,
        email: json['email'] as String,
        phone: json['phone'] as String?,
        languagePreference: json['language_preference'] as String? ?? 'en',
      );
      return _session;
    } on FormatException {
      await clear();
      return null;
    } on TypeError {
      await clear();
      return null;
    }
  }

  @override
  Future<void> save(UserSession session) async {
    _session = session;
    await _storage.write(
      key: _sessionKey,
      value: jsonEncode({
        'token': session.token,
        'name': session.name,
        'email': session.email,
        'phone': session.phone,
        'language_preference': session.languagePreference,
      }),
    );
  }

  @override
  Future<void> clear() async {
    _session = null;
    await _storage.delete(key: _sessionKey);
  }
}
