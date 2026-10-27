import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../../common/network/api_client.dart';
import '../../../auth/data/providers/auth_provider.dart';
import '../models/owner_report.dart';
import '../repositories/owner_report_repository.dart';

final ownerReportRepositoryProvider = Provider<OwnerReportRepository>(
  (ref) => OwnerReportRepository(ref.read(apiClientProvider)),
);

final ownerReportProvider = FutureProvider.autoDispose
    .family<OwnerReport, ReportPeriod>((ref, period) async {
      try {
        return await ref
            .read(ownerReportRepositoryProvider)
            .getReport(start: period.start, end: period.end);
      } on ApiException catch (error) {
        if (error.statusCode == 401) {
          await ref.read(authControllerProvider.notifier).logout();
        }
        rethrow;
      }
    });
