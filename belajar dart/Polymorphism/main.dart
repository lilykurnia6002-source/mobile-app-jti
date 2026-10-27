import 'bangun_datar.dart';
import 'persegi.dart';
import 'segitiga.dart';
import 'lingkaran.dart';

void main(List<String> args) {
  print("TUGAS POLYMORPHISM");
  print("==================");

  BangunDatar persegi = Persegi(17.0);
  print("Luas Persegi: ${persegi.luas()}");
  print("Keliling Persegi: ${persegi.keliling()}");
  print("");

  BangunDatar segitiga = Segitiga(20.0, 10.0, 15.0, 15.0, 15.0);
  print("Luas Segitiga: ${segitiga.luas()}");
  print("Keliling Segitiga: ${segitiga.keliling()}");
  print("");

  BangunDatar lingkaran = Lingkaran(10.0);
  print("Luas Lingkaran: ${lingkaran.luas()}");
  print("Keliling Lingkaran: ${lingkaran.keliling()}");
}