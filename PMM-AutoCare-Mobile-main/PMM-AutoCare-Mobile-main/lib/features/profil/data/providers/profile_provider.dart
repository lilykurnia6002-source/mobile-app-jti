import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../../common/network/api_client.dart';
import '../../../auth/data/providers/auth_provider.dart';
import '../models/profile_model.dart';
import '../repositories/profile_repository.dart';

final profileRepositoryProvider = Provider<ProfileRepository>(
  (ref) => ProfileRepository(ref.read(apiClientProvider)),
);

class ProfileController extends AsyncNotifier<ProfileModel> {
  @override
  Future<ProfileModel> build() => _getProfile();

  Future<ProfileModel> _getProfile() async {
    try {
      return await ref.read(profileRepositoryProvider).getProfile();
    } on ApiException catch (error) {
      if (error.statusCode == 401) {
        await ref.read(authControllerProvider.notifier).logout();
      }
      rethrow;
    }
  }

  Future<ProfileModel> saveProfile(ProfileUpdate update) async {
    try {
      final current = state.valueOrNull;
      final updated = await ref
          .read(profileRepositoryProvider)
          .updateProfile(update);
      final profile = current == null
          ? updated
          : updated.withJumlahMobil(current.jumlahMobil);
      state = AsyncData(profile);
      return profile;
    } on ApiException catch (error) {
      if (error.statusCode == 401) {
        await ref.read(authControllerProvider.notifier).logout();
      }
      rethrow;
    }
  }
}

final profileProvider = AsyncNotifierProvider<ProfileController, ProfileModel>(
  ProfileController.new,
);
