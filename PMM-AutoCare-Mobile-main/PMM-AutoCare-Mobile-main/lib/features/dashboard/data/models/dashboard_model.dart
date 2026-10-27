class MobilBrief {
  MobilBrief({required this.merk, required this.tipe, required this.platNomor});

  factory MobilBrief.fromJson(Map<String, dynamic> json) => MobilBrief(
    merk: json['merk'] as String,
    tipe: json['tipe'] as String,
    platNomor: json['plat_nomor'] as String,
  );

  final String merk;
  final String tipe;
  final String platNomor;
}

class RiwayatStatusEntry {
  RiwayatStatusEntry({
    required this.status,
    required this.keterangan,
    required this.waktu,
  });

  factory RiwayatStatusEntry.fromJson(Map<String, dynamic> json) =>
      RiwayatStatusEntry(
        status: json['status'] as String,
        keterangan: json['keterangan'] as String?,
        waktu: json['waktu'] as String?,
      );

  final String status;
  final String? keterangan;
  final String? waktu;
}

class ServisAktif {
  ServisAktif({
    required this.idPesanan,
    required this.kodePesanan,
    required this.status,
    required this.mobil,
    required this.riwayatStatus,
  });

  factory ServisAktif.fromJson(Map<String, dynamic> json) => ServisAktif(
    idPesanan: json['id_pesanan'] as int,
    kodePesanan: json['kode_pesanan'] as String,
    status: json['status'] as String,
    mobil: MobilBrief.fromJson(json['mobil'] as Map<String, dynamic>),
    riwayatStatus: (json['riwayat_status'] as List)
        .cast<Map<String, dynamic>>()
        .map(RiwayatStatusEntry.fromJson)
        .toList(),
  );

  final int idPesanan;
  final String kodePesanan;
  final String status;
  final MobilBrief mobil;
  final List<RiwayatStatusEntry> riwayatStatus;
}

class RiwayatItem {
  RiwayatItem({
    required this.idPesanan,
    required this.kodePesanan,
    required this.status,
    required this.mobil,
    required this.totalBiaya,
    required this.waktuPesan,
  });

  factory RiwayatItem.fromJson(Map<String, dynamic> json) => RiwayatItem(
    idPesanan: json['id_pesanan'] as int,
    kodePesanan: json['kode_pesanan'] as String,
    status: json['status'] as String,
    mobil: MobilBrief.fromJson(json['mobil'] as Map<String, dynamic>),
    totalBiaya: (json['total_biaya'] as num).toDouble(),
    waktuPesan: json['waktu_pesan'] as String?,
  );

  final int idPesanan;
  final String kodePesanan;
  final String status;
  final MobilBrief mobil;
  final double totalBiaya;
  final String? waktuPesan;
}

class DashboardModel {
  DashboardModel({
    required this.nama,
    required this.jumlahMobil,
    required this.servisAktif,
    required this.riwayat,
  });

  factory DashboardModel.fromJson(Map<String, dynamic> json) => DashboardModel(
    nama: json['nama'] as String,
    jumlahMobil: json['jumlah_mobil'] as int,
    servisAktif: json['servis_aktif'] == null
        ? null
        : ServisAktif.fromJson(json['servis_aktif'] as Map<String, dynamic>),
    riwayat: (json['riwayat'] as List)
        .cast<Map<String, dynamic>>()
        .map(RiwayatItem.fromJson)
        .toList(),
  );

  final String nama;
  final int jumlahMobil;
  final ServisAktif? servisAktif;
  final List<RiwayatItem> riwayat;
}
