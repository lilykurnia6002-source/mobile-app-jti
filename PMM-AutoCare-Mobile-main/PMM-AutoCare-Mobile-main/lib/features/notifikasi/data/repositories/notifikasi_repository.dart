import '../../../../common/network/api_client.dart';
import '../models/notifikasi_model.dart';

class NotifikasiRepository {
  NotifikasiRepository(this._client);

  final ApiClient _client;

  Future<NotifikasiPage> getNotifikasi({
    int page = 1,
    int perPage = 15,
    bool unreadOnly = false,
  }) async {
    final response = await _client.get(
      'notifikasi',
      queryParameters: {
        'page': '$page',
        'per_page': '$perPage',
        if (unreadOnly) 'belum_baca': '1',
      },
    );
    final data = (response['data'] as List<dynamic>)
        .cast<Map<String, dynamic>>()
        .map(NotifikasiModel.fromJson)
        .toList(growable: false);
    final meta = response['meta'] as Map<String, dynamic>;
    return NotifikasiPage(
      items: data,
      total: meta['total'] as int,
      currentPage: meta['current_page'] as int,
      lastPage: meta['last_page'] as int,
    );
  }

  Future<int> getUnreadCount() async =>
      (await getNotifikasi(perPage: 1, unreadOnly: true)).total;

  Future<void> markAsRead(int idNotifikasi) async {
    await _client.patch('notifikasi/$idNotifikasi/baca', const {});
  }
}
