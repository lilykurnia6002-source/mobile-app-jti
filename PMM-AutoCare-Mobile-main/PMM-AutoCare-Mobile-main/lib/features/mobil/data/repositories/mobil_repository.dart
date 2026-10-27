import '../../../../common/network/api_client.dart';
import '../models/mobil_model.dart';

class MobilRepository {
  MobilRepository(this._client);

  final ApiClient _client;

  Future<List<MobilModel>> getMobil() async {
    final response = await _client.get('mobil');
    return _list(response['data']).map(MobilModel.fromJson).toList();
  }

  Future<MobilModel> createMobil(MobilInput input) async {
    final response = await _client.post('mobil', input.toJson(), auth: true);
    return MobilModel.fromJson(response['data'] as Map<String, dynamic>);
  }

  Future<MobilModel> updateMobil(int id, MobilInput input) async {
    final response = await _client.put('mobil/$id', input.toJson());
    return MobilModel.fromJson(response['data'] as Map<String, dynamic>);
  }

  List<Map<String, dynamic>> _list(Object? value) => (value as List<dynamic>)
      .map((item) => item as Map<String, dynamic>)
      .toList();
}
