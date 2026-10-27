class PesananSummary {
  const PesananSummary({
    required this.id,
    required this.kodePesanan,
    required this.status,
    required this.tanggalSlot,
    required this.jamMulai,
    required this.jamSelesai,
    required this.totalBiaya,
    required this.waktuPesan,
  });

  factory PesananSummary.fromJson(Map<String, dynamic> json) => PesananSummary(
    id: json['id_pesanan'] as int,
    kodePesanan: json['kode_pesanan'] as String,
    status: json['status'] as String,
    tanggalSlot: json['tanggal_slot'] as String?,
    jamMulai: json['jam_mulai'] as String?,
    jamSelesai: json['jam_selesai'] as String?,
    totalBiaya: (json['total_biaya'] as num).toDouble(),
    waktuPesan: json['waktu_pesan'] as String?,
  );

  final int id;
  final String kodePesanan;
  final String status;
  final String? tanggalSlot;
  final String? jamMulai;
  final String? jamSelesai;
  final double totalBiaya;
  final String? waktuPesan;
}

class PesananPage {
  const PesananPage({
    required this.items,
    required this.currentPage,
    required this.lastPage,
    required this.total,
    this.isLoadingMore = false,
    this.loadMoreError,
  });

  final List<PesananSummary> items;
  final int currentPage;
  final int lastPage;
  final int total;
  final bool isLoadingMore;
  final String? loadMoreError;

  bool get hasNextPage => currentPage < lastPage;

  PesananPage copyWith({
    List<PesananSummary>? items,
    int? currentPage,
    int? lastPage,
    int? total,
    bool? isLoadingMore,
    String? loadMoreError,
  }) => PesananPage(
    items: items ?? this.items,
    currentPage: currentPage ?? this.currentPage,
    lastPage: lastPage ?? this.lastPage,
    total: total ?? this.total,
    isLoadingMore: isLoadingMore ?? this.isLoadingMore,
    loadMoreError: loadMoreError,
  );
}

class PesananDetail {
  const PesananDetail({
    required this.id,
    required this.kodePesanan,
    required this.status,
    required this.keluhan,
    required this.mobil,
    required this.slot,
    required this.layanan,
    required this.totalBiaya,
    required this.dapatDibatalkan,
  });

  factory PesananDetail.fromJson(Map<String, dynamic> json) => PesananDetail(
    id: json['id_pesanan'] as int,
    kodePesanan: json['kode_pesanan'] as String,
    status: json['status'] as String,
    keluhan: json['keluhan'] as String,
    mobil: json['mobil'] == null
        ? null
        : Kendaraan.fromJson(json['mobil'] as Map<String, dynamic>),
    slot: json['slot'] == null
        ? null
        : BookingSlot.fromJson(json['slot'] as Map<String, dynamic>),
    layanan: (json['detail_layanan'] as List<dynamic>)
        .map((item) => DetailLayanan.fromJson(item as Map<String, dynamic>))
        .toList(),
    totalBiaya: (json['total_biaya'] as num).toDouble(),
    dapatDibatalkan: json['dapat_dibatalkan'] as bool,
  );

  final int id;
  final String kodePesanan;
  final String status;
  final String keluhan;
  final Kendaraan? mobil;
  final BookingSlot? slot;
  final List<DetailLayanan> layanan;
  final double totalBiaya;
  final bool dapatDibatalkan;
}

class DetailLayanan {
  const DetailLayanan({
    required this.id,
    this.nama,
    required this.jumlah,
    required this.hargaJasa,
    required this.subtotal,
  });

  factory DetailLayanan.fromJson(Map<String, dynamic> json) => DetailLayanan(
    id: json['id_layanan'] as int,
    nama: json['nama_layanan'] as String?,
    jumlah: json['jumlah'] as int,
    hargaJasa: (json['harga_jasa'] as num).toDouble(),
    subtotal: (json['subtotal'] as num).toDouble(),
  );

  final int id;
  final String? nama;
  final int jumlah;
  final double hargaJasa;
  final double subtotal;
}

class RiwayatPesananStatus {
  const RiwayatPesananStatus({
    required this.status,
    required this.waktu,
    this.keterangan,
    this.mekanik,
  });

  factory RiwayatPesananStatus.fromJson(Map<String, dynamic> json) =>
      RiwayatPesananStatus(
        status: json['status'] as String,
        waktu: json['waktu'] as String?,
        keterangan: json['keterangan'] as String?,
        mekanik: json['mekanik'] as String?,
      );

  final String status;
  final String? waktu;
  final String? keterangan;
  final String? mekanik;
}

class Kendaraan {
  const Kendaraan({
    required this.id,
    required this.platNomor,
    required this.merk,
    required this.tipe,
    required this.tahun,
  });

  factory Kendaraan.fromJson(Map<String, dynamic> json) => Kendaraan(
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

class Layanan {
  const Layanan({
    required this.id,
    required this.nama,
    this.kategori,
    required this.hargaJasa,
    required this.estimasiMenit,
  });

  factory Layanan.fromJson(Map<String, dynamic> json) => Layanan(
    id: json['id_layanan'] as int,
    nama: json['nama_layanan'] as String,
    kategori: json['kategori'] as String?,
    hargaJasa: (json['harga_jasa'] as num).toDouble(),
    estimasiMenit: json['estimasi_menit'] as int,
  );

  final int id;
  final String nama;
  final String? kategori;
  final double hargaJasa;
  final int estimasiMenit;
}

class BookingSlot {
  const BookingSlot({
    required this.id,
    required this.tanggal,
    required this.jamMulai,
    required this.jamSelesai,
    this.sisaKuota,
  });

  /// `sisa_kuota` is present on the full `SlotResource` (GET /slot,
  /// GET /slot/{id}) used to pick a slot, but absent on the trimmed
  /// `SlotRingkasResource` embedded inside pesanan responses (GET
  /// /pesanan/{id}, POST /pesanan) — hence nullable here.
  factory BookingSlot.fromJson(Map<String, dynamic> json) => BookingSlot(
    id: json['id_slot'] as int,
    tanggal: json['tanggal'] as String,
    jamMulai: json['jam_mulai'] as String,
    jamSelesai: json['jam_selesai'] as String,
    sisaKuota: json['sisa_kuota'] as int?,
  );

  final int id;
  final String tanggal;
  final String jamMulai;
  final String jamSelesai;
  final int? sisaKuota;
}

class BookingInputs {
  const BookingInputs({
    required this.kendaraan,
    required this.layanan,
    required this.tanggalTersedia,
  });

  final List<Kendaraan> kendaraan;
  final List<Layanan> layanan;
  final List<String> tanggalTersedia;
}

class PesananDibuat {
  const PesananDibuat({
    required this.id,
    required this.kodePesanan,
    required this.status,
  });

  factory PesananDibuat.fromJson(Map<String, dynamic> json) => PesananDibuat(
    id: json['id_pesanan'] as int,
    kodePesanan: json['kode_pesanan'] as String,
    status: json['status'] as String,
  );

  final int id;
  final String kodePesanan;
  final String status;
}
