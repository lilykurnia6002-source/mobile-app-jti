class NotifikasiModel {
  const NotifikasiModel({
    required this.idNotifikasi,
    required this.judul,
    required this.pesan,
    required this.waktuKirim,
    required this.dibaca,
    required this.idPesanan,
  });

  factory NotifikasiModel.fromJson(Map<String, dynamic> json) =>
      NotifikasiModel(
        idNotifikasi: json['id_notifikasi'] as int,
        judul: json['judul'] as String,
        pesan: json['pesan'] as String,
        waktuKirim: json['waktu_kirim'] as String?,
        dibaca: json['dibaca'] as bool,
        idPesanan: json['id_pesanan'] as int?,
      );

  final int idNotifikasi;
  final String judul;
  final String pesan;
  final String? waktuKirim;
  final bool dibaca;
  final int? idPesanan;

  NotifikasiModel markAsRead() => NotifikasiModel(
    idNotifikasi: idNotifikasi,
    judul: judul,
    pesan: pesan,
    waktuKirim: waktuKirim,
    dibaca: true,
    idPesanan: idPesanan,
  );
}

class NotifikasiPage {
  const NotifikasiPage({
    required this.items,
    required this.total,
    required this.currentPage,
    required this.lastPage,
    this.isLoadingMore = false,
  });

  final List<NotifikasiModel> items;
  final int total;
  final int currentPage;
  final int lastPage;
  final bool isLoadingMore;

  bool get hasNextPage => currentPage < lastPage;

  NotifikasiPage copyWith({
    List<NotifikasiModel>? items,
    int? total,
    int? currentPage,
    int? lastPage,
    bool? isLoadingMore,
  }) => NotifikasiPage(
    items: items ?? this.items,
    total: total ?? this.total,
    currentPage: currentPage ?? this.currentPage,
    lastPage: lastPage ?? this.lastPage,
    isLoadingMore: isLoadingMore ?? this.isLoadingMore,
  );
}
