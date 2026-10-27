import '../../../../common/network/api_client.dart';
import '../models/auth_session.dart';

class AuthRepository {
  AuthRepository(this._client);
  final ApiClient _client;

  Future<AuthSession> login({
    required String email,
    required String password,
  }) async {
    final response = await _client.post('auth/login', {
      'email': email,
      'password': password,
    });
    return _saveSession(response['data'] as Map<String, dynamic>);
  }

  Future<AuthSession> register({
    required String nama,
    required String email,
    required String password,
    required String noHp,
    String? alamat,
  }) async {
    final response = await _client.post('auth/register', {
      'nama': nama,
      'email': email,
      'password': password,
      'no_hp': noHp,
      if (alamat != null && alamat.isNotEmpty) 'alamat': alamat,
    });
    return _saveSession(response['data'] as Map<String, dynamic>);
  }

  Future<AuthSession> _saveSession(Map<String, dynamic> data) async {
    await _client.saveToken(data['token'] as String);
    final user = _asMap(data['user']) ?? _asMap(data['pengguna']);
    final pelanggan = _asMap(data['pelanggan']);
    final role =
        (user?['role'] as String?) ?? (data['role'] as String?) ?? 'pelanggan';
    await _client.saveRole(role);
    return AuthSession(
      role: role,
      name:
          (user?['name'] as String?) ??
          (user?['nama'] as String?) ??
          (pelanggan?['nama'] as String?),
    );
  }

  Future<void> logout() async {
    try {
      await _client.post('auth/logout', const {}, auth: true);
    } catch (_) {
      // Local sign-out remains correct when the remote token is expired.
    } finally {
      await _client.clearToken();
      await _client.clearRole();
    }
  }

  Future<AuthSession?> currentSession() async {
    final token = await _client.readToken();
    if (token == null || token.isEmpty) return null;
    return AuthSession(role: await _client.readRole() ?? 'pelanggan');
  }

  static Map<String, dynamic>? _asMap(Object? value) =>
      value is Map<String, dynamic> ? value : null;
}
