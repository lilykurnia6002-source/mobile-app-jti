import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../../common/network/api_client.dart';
import '../../../auth/data/providers/auth_provider.dart';
import '../models/pesanan_model.dart';
import '../repositories/pesanan_repository.dart';

final pesananRepositoryProvider = Provider<PesananRepository>(
  (ref) => PesananRepository(ref.read(apiClientProvider)),
);

class PesananListController extends AutoDisposeAsyncNotifier<PesananPage> {
  @override
  Future<PesananPage> build() => _authenticated(
    ref,
    () => ref.read(pesananRepositoryProvider).getPesanan(),
  );

  Future<void> loadMore() async {
    final current = state.valueOrNull;
    if (current == null || !current.hasNextPage || current.isLoadingMore) {
      return;
    }

    state = AsyncData(current.copyWith(isLoadingMore: true));
    try {
      final next = await _authenticated(
        ref,
        () => ref
            .read(pesananRepositoryProvider)
            .getPesanan(page: current.currentPage + 1),
      );
      state = AsyncData(
        PesananPage(
          items: [...current.items, ...next.items],
          currentPage: next.currentPage,
          lastPage: next.lastPage,
          total: next.total,
        ),
      );
    } on ApiException catch (error) {
      state = AsyncData(current.copyWith(loadMoreError: error.message));
    } catch (error) {
      state = AsyncData(current.copyWith(loadMoreError: error.toString()));
    }
  }
}

final pesananListProvider =
    AsyncNotifierProvider.autoDispose<PesananListController, PesananPage>(
      PesananListController.new,
    );

final pesananDetailProvider = FutureProvider.autoDispose
    .family<PesananDetail, int>(
      (ref, id) => _authenticated(
        ref,
        () => ref.read(pesananRepositoryProvider).getDetail(id),
      ),
    );

final riwayatPesananStatusProvider = FutureProvider.autoDispose
    .family<List<RiwayatPesananStatus>, int>(
      (ref, id) => _authenticated(
        ref,
        () => ref.read(pesananRepositoryProvider).getRiwayatStatus(id),
      ),
    );

final bookingInputsProvider = FutureProvider.autoDispose<BookingInputs>(
  (ref) => _authenticated(
    ref,
    () => ref.read(pesananRepositoryProvider).getBookingInputs(),
  ),
);

final bookingSlotsProvider = FutureProvider.autoDispose
    .family<List<BookingSlot>, String>(
      (ref, tanggal) => _authenticated(
        ref,
        () => ref.read(pesananRepositoryProvider).getSlot(tanggal),
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
