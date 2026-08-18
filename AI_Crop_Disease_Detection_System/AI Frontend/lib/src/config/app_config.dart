class AppConfig {
  const AppConfig._();

  static const apiBaseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'http://10.0.2.2:8000/api',
  );

  static const storageBaseUrl = String.fromEnvironment(
    'STORAGE_BASE_URL',
    defaultValue: 'http://10.0.2.2:8000/storage',
  );
}
