class ProfileModel {
  const ProfileModel({
    required this.id,
    required this.nama,
    required this.email,
    required this.noHp,
    required this.alamat,
    this.jumlahMobil,
  });

  factory ProfileModel.fromJson(Map<String, dynamic> json) => ProfileModel(
    id: json['id'] as int,
    nama: json['nama'] as String,
    email: json['email'] as String,
    noHp: _normalizePhone(json['no_hp'] as String),
    alamat: json['alamat'] as String?,
    jumlahMobil: json['jumlah_mobil'] as int?,
  );

  static String _normalizePhone(String value) =>
      value.replaceAll(RegExp(r'[^0-9]'), '');

  final int id;
  final String nama;
  final String email;
  final String noHp;

  ProfileModel withJumlahMobil(int? value) => ProfileModel(
    id: id,
    nama: nama,
    email: email,
    noHp: noHp,
    alamat: alamat,
    jumlahMobil: value,
  );
  final String? alamat;
  final int? jumlahMobil;
}

class ProfileUpdate {
  const ProfileUpdate({
    required this.nama,
    required this.email,
    required this.noHp,
    required this.alamat,
    this.password,
  });

  final String nama;
  final String email;
  final String noHp;
  final String? alamat;
  final String? password;

  Map<String, dynamic> toJson() {
    final body = <String, dynamic>{
      'nama': nama,
      'email': email,
      'no_hp': noHp,
      'alamat': alamat,
    };
    if (password != null && password!.isNotEmpty) body['password'] = password;
    return body;
  }
}
