import '../../../../common/network/api_client.dart';
import '../models/dashboard_model.dart';

class DashboardRepository {
  DashboardRepository(this._client);
  final ApiClient _client;

  Future<DashboardModel> getBeranda() async {
    final response = await _client.get('dashboard');
    return DashboardModel.fromJson(response['data'] as Map<String, dynamic>);
  }
}
