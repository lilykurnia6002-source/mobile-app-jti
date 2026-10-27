import '../../../../common/network/api_client.dart';
import '../models/owner_report.dart';

class OwnerReportRepository {
  OwnerReportRepository(this._client);
  final ApiClient _client;

  Future<OwnerReport> getReport({
    required DateTime start,
    required DateTime end,
  }) async {
    final query = <String, String>{'start': _date(start), 'end': _date(end)};
    final responses = await Future.wait([
      _client.get('reports/transactions', queryParameters: query),
      _client.get('reports/productivity', queryParameters: query),
      _client.get('reports/non-cash-ratio', queryParameters: query),
    ]);
    return OwnerReport.fromResponses(
      start: start,
      end: end,
      transactions: responses[0],
      productivity: responses[1],
      nonCash: responses[2],
    );
  }

  static String _date(DateTime date) =>
      '${date.year.toString().padLeft(4, '0')}-'
      '${date.month.toString().padLeft(2, '0')}-'
      '${date.day.toString().padLeft(2, '0')}';
}
