import 'dart:math';

class Lingkaran {
  double _jariJari = 0;

  // Setter dengan validasi nilai negatif dikali -1
  set jariJari(double value) {
    if (value < 0) {
      _jariJari = value * -1;
    } else {
      _jariJari = value;
    }
  }

  double get jariJari => _jariJari;

  // Getter luas
  double get luas => pi * _jariJari * _jariJari;
}