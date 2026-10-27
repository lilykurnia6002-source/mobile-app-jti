import '../../../../common/network/api_client.dart';
import '../models/profile_model.dart';

class ProfileRepository {
  ProfileRepository(this._client);

  final ApiClient _client;

  Future<ProfileModel> getProfile() async {
    final response = await _client.get('profil');
    return ProfileModel.fromJson(response['data'] as Map<String, dynamic>);
  }

  Future<ProfileModel> updateProfile(ProfileUpdate update) async {
    final response = await _client.put('profil', update.toJson());
    return ProfileModel.fromJson(response['data'] as Map<String, dynamic>);
  }
}
