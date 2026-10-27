import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../../common/network/api_client.dart';
import '../../../auth/data/providers/auth_provider.dart';
import '../models/nota_model.dart';
import '../repositories/nota_repository.dart';

final notaRepositoryProvider = Provider<NotaRepository>(
  (ref) => NotaRepository(ref.read(apiClientProvider)),
);

final notaProvider = FutureProvider.autoDispose.family<NotaModel, int>(
  (ref, idPesanan) => _authenticated(
    ref,
    () => ref.read(notaRepositoryProvider).getNota(idPesanan),
  ),
);

Future<T> _authenticated<T>(Ref ref, Future<T> Function() request) async {
  try {
    return await request();
  } on ApiException catch (error) {
    if (error.statusCode == 401) {
      await ref.read(authControllerProvider.notifier).logout();
    }
    rethrow;
  }
}
