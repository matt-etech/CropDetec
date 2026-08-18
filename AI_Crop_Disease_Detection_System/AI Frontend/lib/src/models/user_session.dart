class UserSession {
  const UserSession({
    required this.token,
    required this.name,
    required this.email,
    this.phone,
    this.languagePreference = 'en',
  });

  final String token;
  final String name;
  final String email;
  final String? phone;
  final String languagePreference;

  factory UserSession.fromJson(Map<String, dynamic> json) {
    final user = json['user'] as Map<String, dynamic>;

    return UserSession.fromUserJson(
      user,
      token: json['token'] as String? ?? '',
    );
  }

  factory UserSession.fromUserJson(
    Map<String, dynamic> user, {
    required String token,
  }) {
    return UserSession(
      token: token,
      name: user['name'] as String,
      email: user['email'] as String,
      phone: user['phone'] as String?,
      languagePreference: user['language_preference'] as String? ?? 'en',
    );
  }
}
