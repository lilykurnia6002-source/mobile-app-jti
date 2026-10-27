class NotaModel {
  const NotaModel({
    required this.idPesanan,
    required this.kodePesanan,
    required this.status,
    required this.tanggalSlot,
    required this.mobil,
    required this.detailLayanan,
    required this.detailSparepart,
    required this.subtotalJasa,
    required this.subtotalSparepart,
    required this.totalBiaya,
  });

  factory NotaModel.fromJson(Map<String, dynamic> json) => NotaModel(
    idPesanan: _asInt(json['id_pesanan']),
    kodePesanan: json['kode_pesanan'] as String? ?? '—',
    status: json['status'] as String? ?? '—',
    tanggalSlot: json['tanggal_slot'] as String?,
    mobil: json['mobil'] is Map<String, dynamic>
        ? NotaMobil.fromJson(json['mobil'] as Map<String, dynamic>)
        : null,
    detailLayanan: _asList(
      json['detail_layanan'],
    ).map(NotaLayanan.fromJson).toList(),
    detailSparepart: _asList(
      json['detail_sparepart'],
    ).map(NotaSparepart.fromJson).toList(),
    subtotalJasa: _asDouble(json['subtotal_jasa']),
    subtotalSparepart: _asDouble(json['subtotal_sparepart']),
    totalBiaya: _asDouble(json['total_biaya']),
  );

  final int idPesanan;
  final String kodePesanan;
  final String status;
  final String? tanggalSlot;
  final NotaMobil? mobil;
  final List<NotaLayanan> detailLayanan;
  final List<NotaSparepart> detailSparepart;
  final double subtotalJasa;
  final double subtotalSparepart;
  final double totalBiaya;

  static int _asInt(Object? value) => (value as num?)?.toInt() ?? 0;

  static double _asDouble(Object? value) => (value as num?)?.toDouble() ?? 0;

  static List<Map<String, dynamic>> _asList(Object? value) {
    if (value is! List) return const [];
    return [
      for (final item in value)
        if (item is Map<String, dynamic>) item,
    ];
  }
}

class NotaMobil {
  const NotaMobil({
    required this.platNomor,
    required this.merk,
    required this.tipe,
  });

  factory NotaMobil.fromJson(Map<String, dynamic> json) => NotaMobil(
    platNomor: json['plat_nomor'] as String? ?? '—',
    merk: json['merk'] as String? ?? '—',
    tipe: json['tipe'] as String? ?? '—',
  );

  final String platNomor;
  final String merk;
  final String tipe;

  String get nama => '$merk $tipe';
}

class NotaLayanan {
  const NotaLayanan({
    required this.namaLayanan,
    required this.jumlah,
    required this.hargaJasa,
    required this.subtotal,
  });

  factory NotaLayanan.fromJson(Map<String, dynamic> json) => NotaLayanan(
    namaLayanan: json['nama_layanan'] as String? ?? 'Layanan',
    jumlah: (json['jumlah'] as num?)?.toInt() ?? 0,
    hargaJasa: (json['harga_jasa'] as num?)?.toDouble() ?? 0,
    subtotal: (json['subtotal'] as num?)?.toDouble() ?? 0,
  );

  final String namaLayanan;
  final int jumlah;
  final double hargaJasa;
  final double subtotal;
}

class NotaSparepart {
  const NotaSparepart({
    required this.nama,
    required this.satuan,
    required this.jumlah,
    required this.harga,
    required this.subtotal,
  });

  factory NotaSparepart.fromJson(Map<String, dynamic> json) => NotaSparepart(
    nama: json['nama'] as String? ?? 'Sparepart',
    satuan: json['satuan'] as String? ?? 'unit',
    jumlah: (json['jumlah'] as num?)?.toInt() ?? 0,
    harga: (json['harga'] as num?)?.toDouble() ?? 0,
    subtotal: (json['subtotal'] as num?)?.toDouble() ?? 0,
  );

  final String nama;
  final String satuan;
  final int jumlah;
  final double harga;
  final double subtotal;
}
