import '../../../../common/network/api_client.dart';
import '../models/pesanan_model.dart';

class PesananRepository {
  PesananRepository(this._client);

  final ApiClient _client;

  Future<PesananPage> getPesanan({int page = 1}) async {
    final response = await _client.get(
      'pesanan',
      queryParameters: {'page': '$page'},
    );
    final meta = response['meta'] as Map<String, dynamic>;
    return PesananPage(
      items: _list(response['data']).map(PesananSummary.fromJson).toList(),
      currentPage: (meta['current_page'] as num).toInt(),
      lastPage: (meta['last_page'] as num).toInt(),
      total: (meta['total'] as num).toInt(),
    );
  }

  Future<PesananDetail> getDetail(int id) async {
    final response = await _client.get('pesanan/$id');
    return PesananDetail.fromJson(response['data'] as Map<String, dynamic>);
  }

  Future<List<RiwayatPesananStatus>> getRiwayatStatus(int id) async {
    final response = await _client.get('pesanan/$id/riwayat-status');
    final data = response['data'] as Map<String, dynamic>;
    return _list(data['riwayat']).map(RiwayatPesananStatus.fromJson).toList();
  }

  Future<BookingInputs> getBookingInputs() async {
    final responses = await Future.wait([
      _client.get('mobil'),
      _client.get('layanan'),
      _client.get('slot/tanggal-tersedia'),
    ]);
    final dates =
        (responses[2]['data'] as Map<String, dynamic>)['tanggal_tersedia'];
    return BookingInputs(
      kendaraan: _list(responses[0]['data']).map(Kendaraan.fromJson).toList(),
      layanan: _list(responses[1]['data']).map(Layanan.fromJson).toList(),
      tanggalTersedia: (dates as List<dynamic>).cast<String>(),
    );
  }

  Future<List<BookingSlot>> getSlot(String tanggal) async {
    final response = await _client.get(
      'slot',
      queryParameters: {'tanggal': tanggal},
    );
    return _list(response['data']).map(BookingSlot.fromJson).toList();
  }

  Future<PesananDibuat> buatPesanan({
    required int idMobil,
    required int idSlot,
    required String keluhan,
    required Iterable<int> layananIds,
  }) async {
    final response = await _client.post('pesanan', {
      'id_mobil': idMobil,
      'id_slot': idSlot,
      'keluhan': keluhan,
      'detail_layanan': [
        for (final id in layananIds) {'id_layanan': id, 'jumlah': 1},
      ],
    }, auth: true);
    return PesananDibuat.fromJson(response['data'] as Map<String, dynamic>);
  }

  Future<void> batalkanPesanan(int id) async {
    await _client.patch('pesanan/$id/batal', {});
  }

  List<Map<String, dynamic>> _list(Object? value) => (value as List<dynamic>)
      .map((item) => item as Map<String, dynamic>)
      .toList();
}
