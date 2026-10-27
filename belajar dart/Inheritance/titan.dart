class Titan {
  double _powerPoint = 0;

  set powerPoint(double value) {
    _powerPoint = value;
  }

  double get powerPoint {
    if (_powerPoint < 5) {
      return 5.0; // Jika di bawah 5 maka dicetak 5
    }
    return _powerPoint;
  }
}