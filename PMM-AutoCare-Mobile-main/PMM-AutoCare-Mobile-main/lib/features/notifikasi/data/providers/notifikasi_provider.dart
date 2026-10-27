import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../../common/network/api_client.dart';
import '../../../auth/data/providers/auth_provider.dart';
import '../models/notifikasi_model.dart';
import '../repositories/notifikasi_repository.dart';

final notifikasiRepositoryProvider = Provider<NotifikasiRepository>(
  (ref) => NotifikasiRepository(ref.read(apiClientProvider)),
);

final unreadNotificationCountProvider = FutureProvider.autoDispose<int>((
  ref,
) async {
  try {
    return await ref.read(notifikasiRepositoryProvider).getUnreadCount();
  } on ApiException catch (error) {
    if (error.statusCode == 401) {
      await ref.read(authControllerProvider.notifier).logout();
    }
    rethrow;
  }
});

class NotifikasiController extends AsyncNotifier<NotifikasiPage> {
  @override
  Future<NotifikasiPage> build() => _getNotifikasi();

  Future<NotifikasiPage> _getNotifikasi({int page = 1}) async {
    try {
      return await ref
          .read(notifikasiRepositoryProvider)
          .getNotifikasi(page: page);
    } on ApiException catch (error) {
      if (error.statusCode == 401) {
        await ref.read(authControllerProvider.notifier).logout();
      }
      rethrow;
    }
  }

  Future<void> loadMore() async {
    final current = state.valueOrNull;
    if (current == null || !current.hasNextPage || current.isLoadingMore) {
      return;
    }

    state = AsyncData(current.copyWith(isLoadingMore: true));
    try {
      final nextPage = await _getNotifikasi(page: current.currentPage + 1);
      state = AsyncData(
        nextPage.copyWith(
          items: [...current.items, ...nextPage.items],
          isLoadingMore: false,
        ),
      );
    } catch (_) {
      state = AsyncData(current);
      rethrow;
    }
  }

  Future<void> markAsRead(int idNotifikasi) async {
    try {
      await ref.read(notifikasiRepositoryProvider).markAsRead(idNotifikasi);
      final current = state.valueOrNull;
      if (current != null) {
        state = AsyncData(
          current.copyWith(
            items: [
              for (final notification in current.items)
                if (notification.idNotifikasi == idNotifikasi)
                  notification.markAsRead()
                else
                  notification,
            ],
          ),
        );
      }
      ref.invalidate(unreadNotificationCountProvider);
    } on ApiException catch (error) {
      if (error.statusCode == 401) {
        await ref.read(authControllerProvider.notifier).logout();
      }
      rethrow;
    }
  }
}

final notifikasiProvider =
    AsyncNotifierProvider<NotifikasiController, NotifikasiPage>(
      NotifikasiController.new,
    );
