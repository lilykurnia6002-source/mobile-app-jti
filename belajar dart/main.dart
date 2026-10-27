import 'dart:io';

void main() {
  // =========================
  // 1. STRING
  // =========================

  String nama = "Liliy";

  print("Nama saya: $nama");

  // Mengambil huruf berdasarkan posisi
  print("Huruf pertama: ${nama[0]}");

  // =========================
  // 2. NUMBER
  // =========================

  int umur = 20;
  double tinggi = 160.5;

  print("Umur: $umur tahun");
  print("Tinggi: $tinggi cm");

  // =========================
  // 3. BOOLEAN
  // =========================

  bool mahasiswa = true;

  print("Apakah mahasiswa? $mahasiswa");

  // =========================
  // 4. OPERATOR MATEMATIKA
  // =========================

  int angka1 = 10;
  int angka2 = 5;

  print("Penjumlahan: ${angka1 + angka2}");
  print("Pengurangan: ${angka1 - angka2}");
  print("Perkalian: ${angka1 * angka2}");
  print("Pembagian: ${angka1 / angka2}");
  print("Sisa bagi: ${angka1 % angka2}");

  // =========================
  // 5. OPERATOR PERBANDINGAN
  // =========================

  print("Apakah angka1 sama dengan angka2? ${angka1 == angka2}");
  print("Apakah angka1 tidak sama dengan angka2? ${angka1 != angka2}");
  print("Apakah angka1 lebih besar? ${angka1 > angka2}");
  print("Apakah angka1 lebih kecil? ${angka1 < angka2}");

  // =========================
  // 6. OPERATOR AND (&&)
  // =========================

  bool punyaKartu = true;
  bool punyaUang = true;

  print("Boleh membeli? ${punyaKartu && punyaUang}");

  // =========================
  // 7. OPERATOR OR (||)
  // =========================

  bool hariLibur = false;
  bool hariMinggu = true;

  print("Bisa libur? ${hariLibur || hariMinggu}");

  // =========================
  // 8. INPUT DARI KEYBOARD
  // =========================

  print("Masukkan nama kamu:");

  String? namaInput = stdin.readLineSync();

  print("Halo, $namaInput!");

  print("Masukkan umur kamu:");

  String? umurInput = stdin.readLineSync();

  int umurInputAngka = int.parse(umurInput!);

  print("Umur kamu adalah $umurInputAngka tahun");
}