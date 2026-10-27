import 'dart:convert';

import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:http/http.dart' as http;

// Pass the backend origin at build/run time, for example:
// `flutter run --dart-define=API_BASE_URL=http://127.0.0.1:8000`.
// The host differs by runtime: Android emulator uses 10.0.2.2, iOS
// Simulator uses 127.0.0.1, and physical devices use the host machine's LAN
// IP. Keeping it explicit avoids shipping a host-specific default.
const apiBaseUrl = String.fromEnvironment('API_BASE_URL');

Uri _apiUri(String path, {Map<String, String>? queryParameters}) {
  if (apiBaseUrl.isEmpty) {
    throw StateError(
      'API_BASE_URL is not configured. Pass --dart-define=API_BASE_URL=<backend origin>.',
    );
  }

  final base = Uri.tryParse(apiBaseUrl);
  if (base == null ||
      base.host.isEmpty ||
      (base.scheme != 'http' && base.scheme != 'https') ||
      base.query.isNotEmpty ||
      base.fragment.isNotEmpty) {
    throw StateError(
      'API_BASE_URL must be an absolute HTTP(S) URL without a query or fragment.',
    );
  }

  final basePath = base.path.replaceFirst(RegExp(r'/+$'), '');
  return base.replace(
    path: '$basePath/api/v1/$path',
    queryParameters: queryParameters,
  );
}

final apiClientProvider = Provider<ApiClient>((ref) => ApiClient());

class ApiException implements Exception {
  ApiException(this.message, {this.statusCode, this.fieldErrors});
  final String message;
  final int? statusCode;

  /// Per-field validation messages from a 422 response's
  /// `data.errors` object (Laravel's `{field: [pesan, ...]}` shape), or
  /// null when the backend didn't return structured field errors.
  final Map<String, List<String>>? fieldErrors;

  @override
  String toString() => message;
}

class ApiClient {
  final _storage = const FlutterSecureStorage();
  static const _tokenKey = 'auth_token';
  static const _roleKey = 'auth_role';

  Future<void> saveToken(String token) =>
      _storage.write(key: _tokenKey, value: token);

  Future<String?> readToken() => _storage.read(key: _tokenKey);

  Future<void> saveRole(String role) =>
      _storage.write(key: _roleKey, value: role);

  Future<String?> readRole() => _storage.read(key: _roleKey);

  Future<void> clearToken() => _storage.delete(key: _tokenKey);

  Future<void> clearRole() => _storage.delete(key: _roleKey);

  Future<Map<String, String>> _headers({bool auth = false}) async {
    final headers = <String, String>{
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };
    final token = await readToken();
    if (auth && token != null && token.isNotEmpty) {
      headers['Authorization'] = 'Bearer $token';
    }
    return headers;
  }

  Future<Map<String, dynamic>> post(
    String path,
    Map<String, dynamic> body, {
    bool auth = false,
  }) async {
    final response = await http.post(
      _apiUri(path),
      headers: await _headers(auth: auth),
      body: jsonEncode(body),
    );
    return _decode(response);
  }

  Future<Map<String, dynamic>> put(
    String path,
    Map<String, dynamic> body,
  ) async {
    final response = await http.put(
      _apiUri(path),
      headers: await _headers(auth: true),
      body: jsonEncode(body),
    );
    return _decode(response);
  }

  Future<Map<String, dynamic>> patch(
    String path,
    Map<String, dynamic> body,
  ) async {
    final response = await http.patch(
      _apiUri(path),
      headers: await _headers(auth: true),
      body: jsonEncode(body),
    );
    return _decode(response);
  }

  Future<Map<String, dynamic>> get(
    String path, {
    Map<String, String>? queryParameters,
  }) async {
    final response = await http.get(
      _apiUri(path, queryParameters: queryParameters),
      headers: await _headers(auth: true),
    );
    return _decode(response);
  }

  Map<String, dynamic> _decode(http.Response response) {
    final body = jsonDecode(response.body) as Map<String, dynamic>;
    if (response.statusCode >= 200 &&
        response.statusCode < 300 &&
        body['success'] == true) {
      return body;
    }
    final data = body['data'];
    final rawErrors = data is Map<String, dynamic> ? data['errors'] : null;
    throw ApiException(
      (body['message'] as String?) ??
          'Terjadi kesalahan (HTTP ${response.statusCode})',
      statusCode: response.statusCode,
      fieldErrors: rawErrors is Map<String, dynamic>
          ? rawErrors.map(
              (key, value) =>
                  MapEntry(key, (value as List<dynamic>).cast<String>()),
            )
          : null,
    );
  }
}
