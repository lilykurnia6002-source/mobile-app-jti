import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../../common/network/api_client.dart';
import '../models/auth_session.dart';
import '../repositories/auth_repository.dart';

final authRepositoryProvider = Provider<AuthRepository>(
  (ref) => AuthRepository(ref.read(apiClientProvider)),
);

class AuthController extends AsyncNotifier<AuthSession?> {
  @override
  Future<AuthSession?> build() =>
      ref.read(authRepositoryProvider).currentSession();

  Future<void> login({required String email, required String password}) async {
    final session = await ref
        .read(authRepositoryProvider)
        .login(email: email, password: password);
    state = AsyncData(session);
  }

  Future<void> register({
    required String nama,
    required String email,
    required String password,
    required String noHp,
    String? alamat,
  }) async {
    final session = await ref
        .read(authRepositoryProvider)
        .register(
          nama: nama,
          email: email,
          password: password,
          noHp: noHp,
          alamat: alamat,
        );
    state = AsyncData(session);
  }

  Future<void> logout() async {
    await ref.read(authRepositoryProvider).logout();
    state = const AsyncData(null);
  }
}

final authControllerProvider =
    AsyncNotifierProvider<AuthController, AuthSession?>(AuthController.new);
