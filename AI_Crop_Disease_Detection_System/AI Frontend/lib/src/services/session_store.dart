import '../models/user_session.dart';

abstract class SessionStore {
  UserSession? get currentSession;

  Future<void> save(UserSession session);

  Future<void> clear();
}

class InMemorySessionStore implements SessionStore {
  UserSession? _session;

  @override
  UserSession? get currentSession => _session;

  @override
  Future<void> save(UserSession session) async {
    _session = session;
  }

  @override
  Future<void> clear() async {
    _session = null;
  }
}
