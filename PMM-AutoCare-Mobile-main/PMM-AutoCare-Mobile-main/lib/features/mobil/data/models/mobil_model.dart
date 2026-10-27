/// Mobil milik pelanggan (`MobilResource` — konsep_api.md § 3).
///
/// Fields mirror `tb_mobil` exactly: no `warna`/`no_rangka`/`no_mesin`
/// columns exist on the backend, only `plat_nomor`, `merk`, `tipe`,
/// `tahun`. This is intentionally a separate model from
/// `features/pesanan/data/models/pesanan_model.dart`'s read-only
/// `Kendaraan` class even though the fields are currently identical 1:1 —
/// `MobilModel` owns the write path (`toJson` via `MobilInput`) that the
/// booking picker's `Kendaraan` never needs, and keeping the two features'
/// models independent avoids cross-feature coupling for what is, from the
/// Pesanan feature's point of view, just a dropdown input.
class MobilModel {
  const MobilModel({
    required this.id,
    required this.platNomor,
    required this.merk,
    required this.tipe,
    required this.tahun,
  });

  factory MobilModel.fromJson(Map<String, dynamic> json) => MobilModel(
    id: json['id'] as int,
    platNomor: json['plat_nomor'] as String,
    merk: json['merk'] as String,
    tipe: json['tipe'] as String,
    tahun: json['tahun'] as int,
  );

  final int id;
  final String platNomor;
  final String merk;
  final String tipe;
  final int tahun;

  String get nama => '$merk $tipe';
}

/// Payload for `POST /mobil` and `PUT /mobil/{id}`. Both endpoints require
/// every field (no partial update support server-side — see
/// `UpdateMobilRequest::rules()`, all four fields are `required`).
class MobilInput {
  const MobilInput({
    required this.platNomor,
    required this.merk,
    required this.tipe,
    required this.tahun,
  });

  final String platNomor;
  final String merk;
  final String tipe;
  final int tahun;

  Map<String, dynamic> toJson() => {
    'plat_nomor': platNomor,
    'merk': merk,
    'tipe': tipe,
    'tahun': tahun,
  };
}
