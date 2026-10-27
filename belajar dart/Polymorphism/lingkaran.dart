import 'bangun_datar.dart';

class Lingkaran extends BangunDatar {
  double r;
  Lingkaran(this.r);

  @override
  double luas() => 3.14 * r * r;

  @override
  double keliling() => 2 * 3.14 * r;
}