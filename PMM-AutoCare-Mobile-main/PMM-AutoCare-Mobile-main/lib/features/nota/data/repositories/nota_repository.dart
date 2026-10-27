import '../../../../common/network/api_client.dart';
import '../models/nota_model.dart';

class NotaRepository {
  NotaRepository(this._client);

  final ApiClient _client;

  Future<NotaModel> getNota(int idPesanan) async {
    final response = await _client.get('pesanan/$idPesanan/nota');
    return NotaModel.fromJson(response['data'] as Map<String, dynamic>);
  }
}
