import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../../common/network/api_client.dart';
import '../../../auth/data/providers/auth_provider.dart';
import '../models/dashboard_model.dart';
import '../repositories/dashboard_repository.dart';

final dashboardRepositoryProvider = Provider<DashboardRepository>(
  (ref) => DashboardRepository(ref.read(apiClientProvider)),
);

final dashboardProvider = FutureProvider.autoDispose<DashboardModel>((
  ref,
) async {
  try {
    return await ref.read(dashboardRepositoryProvider).getBeranda();
  } on ApiException catch (error) {
    if (error.statusCode == 401) {
      await ref.read(authControllerProvider.notifier).logout();
    }
    rethrow;
  }
});
