import 'dart:convert';
import 'dart:io';

import '../config/app_config.dart';
import '../models/api_result.dart';
import '../models/crop.dart';
import '../models/diagnosis.dart';
import '../models/user_session.dart';
import 'session_store.dart';

class ApiClient {
  ApiClient({
    HttpClient? httpClient,
    String? baseUrl,
    SessionStore? sessionStore,
  })  : _httpClient = httpClient ?? HttpClient(),
        _baseUrl = baseUrl ?? AppConfig.apiBaseUrl,
        _sessionStore = sessionStore ?? SecureSessionStore();

  final HttpClient _httpClient;
  final String _baseUrl;
  final SessionStore _sessionStore;

  UserSession? get currentSession => _sessionStore.currentSession;

  Future<UserSession?> restoreSession() => _sessionStore.restore();

  Future<ApiResult<UserSession>> login({
    required String email,
    required String password,
  }) async {
    final response = await _postJson('/login', {
      'email': email,
      'password': password,
    });

    final result = _decode(response, UserSession.fromJson);

    if (result.isSuccess && result.data != null) {
      await _sessionStore.save(result.data!);
    }

    return result;
  }

  Future<ApiResult<UserSession>> register({
    required String name,
    required String email,
    required String password,
    String? phone,
    String languagePreference = 'en',
  }) async {
    final response = await _postJson('/register', {
      'name': name,
      'email': email,
      'phone': phone,
      'password': password,
      'password_confirmation': password,
      'language_preference': languagePreference,
    });

    final result = _decode(response, UserSession.fromJson);

    if (result.isSuccess && result.data != null) {
      await _sessionStore.save(result.data!);
    }

    return result;
  }

  Future<ApiResult<List<Crop>>> crops() async {
    final response = await _get('/crops');

    return _decode(response, (json) {
      final crops = json['crops'] as List<dynamic>;

      return crops
          .map((crop) => Crop.fromJson(crop as Map<String, dynamic>))
          .toList();
    });
  }

  Future<ApiResult<List<Diagnosis>>> diagnoses() async {
    final response = await _get('/diagnoses');

    return _decode(response, (json) {
      final diagnoses = json['diagnoses'] as List<dynamic>;

      return diagnoses
          .map((diagnosis) => Diagnosis.fromJson(diagnosis as Map<String, dynamic>))
          .toList();
    });
  }

  Future<ApiResult<Diagnosis>> storeDiagnosis({
    required File image,
    int? cropId,
  }) async {
    final response = await _postMultipart('/diagnoses', image: image, cropId: cropId);

    return _decode(response, (json) {
      return Diagnosis.fromJson(json['diagnosis'] as Map<String, dynamic>);
    });
  }

  Future<ApiResult<UserSession>> me() async {
    final response = await _get('/me');

    return _decode(response, (json) {
      final currentToken = _sessionStore.currentSession?.token ?? '';
      return UserSession.fromUserJson(
        json['user'] as Map<String, dynamic>,
        token: currentToken,
      );
    });
  }

  Future<ApiResult<UserSession>> updateProfile({
    required String name,
    String? phone,
    required String languagePreference,
  }) async {
    final response = await _patchJson('/me', {
      'name': name,
      'phone': phone,
      'language_preference': languagePreference,
    });

    final result = _decode(response, (json) {
      final currentToken = _sessionStore.currentSession?.token ?? '';
      return UserSession.fromUserJson(
        json['user'] as Map<String, dynamic>,
        token: currentToken,
      );
    });

    if (result.isSuccess && result.data != null) {
      await _sessionStore.save(result.data!);
    }

    return result;
  }

  Future<ApiResult<void>> logout() async {
    final response = await _postJson('/logout', {});

    if (response.statusCode >= 200 && response.statusCode < 300) {
      await _sessionStore.clear();

      return const ApiResult<void>.success(null);
    }

    return ApiResult.failure(_errorMessage(response));
  }

  Future<_ApiResponse> _get(String path) async {
    return _guarded(() async {
      final request = await _httpClient.getUrl(Uri.parse('$_baseUrl$path'));
      request.headers.set(HttpHeaders.acceptHeader, 'application/json');
      _attachToken(request);

      return _send(request);
    });
  }

  Future<_ApiResponse> _postJson(String path, Map<String, dynamic> body) async {
    return _guarded(() async {
      final request = await _httpClient.postUrl(Uri.parse('$_baseUrl$path'));
      request.headers.set(HttpHeaders.acceptHeader, 'application/json');
      request.headers.set(HttpHeaders.contentTypeHeader, 'application/json');
      _attachToken(request);
      request.write(jsonEncode(body));

      return _send(request);
    });
  }

  Future<_ApiResponse> _patchJson(String path, Map<String, dynamic> body) async {
    return _guarded(() async {
      final request = await _httpClient.patchUrl(Uri.parse('$_baseUrl$path'));
      request.headers.set(HttpHeaders.acceptHeader, 'application/json');
      request.headers.set(HttpHeaders.contentTypeHeader, 'application/json');
      _attachToken(request);
      request.write(jsonEncode(body));

      return _send(request);
    });
  }

  Future<_ApiResponse> _postMultipart(
    String path, {
    required File image,
    int? cropId,
  }) async {
    return _guarded(() async {
      final boundary = 'crop-diagnosis-${DateTime.now().microsecondsSinceEpoch}';
      final request = await _httpClient.postUrl(Uri.parse('$_baseUrl$path'));
      request.headers.set(HttpHeaders.acceptHeader, 'application/json');
      request.headers.set(
        HttpHeaders.contentTypeHeader,
        'multipart/form-data; boundary=$boundary',
      );
      _attachToken(request);

      if (cropId != null) {
        request.write('--$boundary\r\n');
        request.write('Content-Disposition: form-data; name="crop_id"\r\n\r\n');
        request.write('$cropId\r\n');
      }

      final fileName = image.uri.pathSegments.isEmpty
          ? 'crop-image.jpg'
          : image.uri.pathSegments.last;
      final contentType = _imageContentType(fileName);

      request.write('--$boundary\r\n');
      request.write(
        'Content-Disposition: form-data; name="image"; filename="$fileName"\r\n',
      );
      request.write('Content-Type: $contentType\r\n\r\n');
      await request.addStream(image.openRead());
      request.write('\r\n--$boundary--\r\n');

      return _send(request);
    });
  }

  void _attachToken(HttpClientRequest request) {
    final token = _sessionStore.currentSession?.token;
    final languagePreference = _sessionStore.currentSession?.languagePreference;

    if (token != null) {
      request.headers.set(HttpHeaders.authorizationHeader, 'Bearer $token');
    }

    if (languagePreference != null) {
      request.headers.set(HttpHeaders.acceptLanguageHeader, languagePreference);
    }
  }

  Future<_ApiResponse> _guarded(Future<_ApiResponse> Function() action) async {
    try {
      return await action();
    } on SocketException {
      return _unreachableResponse();
    } on HttpException {
      return _unreachableResponse();
    } on HandshakeException {
      return _unreachableResponse();
    }
  }

  Future<_ApiResponse> _send(HttpClientRequest request) async {
    final response = await request.close();
    final body = await response.transform(utf8.decoder).join();

    return _ApiResponse(response.statusCode, body);
  }

  _ApiResponse _unreachableResponse() {
    return _ApiResponse(
      0,
      jsonEncode({
        'message': 'Unable to reach the server at $_baseUrl.',
      }),
    );
  }

  ApiResult<T> _decode<T>(
    _ApiResponse response,
    T Function(Map<String, dynamic> json) parser,
  ) {
    Map<String, dynamic> json;

    try {
      json = jsonDecode(response.body) as Map<String, dynamic>;
    } on FormatException {
      return const ApiResult.failure('The server returned an invalid response.');
    }

    if (response.statusCode < 200 || response.statusCode >= 300) {
      return ApiResult.failure(json['message'] as String? ?? 'Request failed.');
    }

    return ApiResult.success(parser(json));
  }

  String _errorMessage(_ApiResponse response) {
    try {
      final json = jsonDecode(response.body) as Map<String, dynamic>;

      return json['message'] as String? ?? 'Request failed.';
    } on FormatException {
      return 'The server returned an invalid response.';
    }
  }

  String _imageContentType(String fileName) {
    final lowerName = fileName.toLowerCase();

    if (lowerName.endsWith('.png')) {
      return 'image/png';
    }

    if (lowerName.endsWith('.webp')) {
      return 'image/webp';
    }

    return 'image/jpeg';
  }
}

class _ApiResponse {
  const _ApiResponse(this.statusCode, this.body);

  final int statusCode;
  final String body;
}
