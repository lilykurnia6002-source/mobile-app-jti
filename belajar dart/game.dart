import 'dart:io';

void main() {
  stdout.write("Masukkan Nama: ");
  String nama = stdin.readLineSync() ?? "";
  stdout.write("Masukkan Peran (Penyihir/Guard/Werewolf): ");
  String peran = stdin.readLineSync() ?? "";

  if (nama.isEmpty && peran.isEmpty) {
    print("Nama harus diisi!");
  } else if (peran.isEmpty) {
    print("Halo $nama, Pilih peranmu untuk memulai game!");
  } else {
    print("Selamat datang di Dunia Werewolf, $nama");
    if (peran.toLowerCase() == "penyihir") {
      print("Halo Penyihir $nama, kamu dapat melihat siapa yang menjadi werewolf!");
    } else if (peran.toLowerCase() == "guard") {
      print("Halo Guard $nama, kamu akan membantu melindungi temanmu dari serangan werewolf.");
    } else if (peran.toLowerCase() == "werewolf") {
      print("Halo Werewolf $nama, Kamu akan memakan mangsa setiap malam!");
    }
  }
}