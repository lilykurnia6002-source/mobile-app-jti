import 'bangun_datar.dart';

class Persegi extends BangunDatar {
  double sisi;
  Persegi(this.sisi);

  @override
  double luas() => sisi * sisi;

  @override
  double keliling() => 4 * sisi;
}