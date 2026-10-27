import 'lingkaran.dart';

void main(List<String> args) {
  Lingkaran lingkaran = Lingkaran();

  print("| TUGAS ENKAPSULASI |");
  print("====================");

  lingkaran.jariJari = 10.0;
  print("Jari-jari: ${lingkaran.jariJari}");
  print("Luas lingkaran: ${lingkaran.luas.toStringAsFixed(1)}");

  lingkaran.jariJari = -20.0; // Otomatis jadi positif 20.0
  print("Jari-jari setelah diubah: ${lingkaran.jariJari}");
  print("Luas lingkaran: ${lingkaran.luas.toStringAsFixed(1)}");
}