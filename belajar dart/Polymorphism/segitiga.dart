import 'bangun_datar.dart';

class Segitiga extends BangunDatar {
  double alas, tinggi, a, b, c;
  Segitiga(this.alas, this.tinggi, this.a, this.b, this.c);

  @override
  double luas() => 0.5 * alas * tinggi;

  @override
  double keliling() => a + b + c;
}