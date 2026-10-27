import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../../common/network/api_client.dart';
import '../../../auth/data/providers/auth_provider.dart';
import '../models/mobil_model.dart';
import '../repositories/mobil_repository.dart';

final mobilRepositoryProvider = Provider<MobilRepository>(
  (ref) => MobilRepository(ref.read(apiClientProvider)),
);

class MobilListController extends AsyncNotifier<List<MobilModel>> {
  @override
  Future<List<MobilModel>> build() =>
      _authenticated(ref, () => ref.read(mobilRepositoryProvider).getMobil());

  Future<void> refresh() async {
    state = const AsyncLoading();
    state = await AsyncValue.guard(
      () => _authenticated(
        ref,
        () => ref.read(mobilRepositoryProvider).getMobil(),
      ),
    );
  }

  Future<MobilModel> addMobil(MobilInput input) async {
    final created = await _authenticated(
      ref,
      () => ref.read(mobilRepositoryProvider).createMobil(input),
    );
    final current = state.valueOrNull ?? const <MobilModel>[];
    state = AsyncData([...current, created]);
    return created;
  }

  Future<MobilModel> editMobil(int id, MobilInput input) async {
    final updated = await _authenticated(
      ref,
      () => ref.read(mobilRepositoryProvider).updateMobil(id, input),
    );
    final current = state.valueOrNull ?? const <MobilModel>[];
    state = AsyncData([
      for (final item in current) item.id == id ? updated : item,
    ]);
    return updated;
  }
}

final mobilListProvider =
    AsyncNotifierProvider<MobilListController, List<MobilModel>>(
      MobilListController.new,
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
