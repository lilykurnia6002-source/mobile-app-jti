import 'ArmorTitan.dart';
import 'AttackTitan.dart';
import 'BeastTitan.dart';
import 'Human.dart';

void main(List<String> args) {
  print("TUGAS INHERITANCE");
  print("=================");

  ArmorTitan armor = ArmorTitan();
  armor.powerPoint = 3.0; // Nilai < 5 otomatis jadi 5.0[cite: 2]
  print("Armor Titan Power Point: ${armor.powerPoint}");
  print(armor.terjang());
  print("");

  AttackTitan attack = AttackTitan();
  attack.powerPoint = 7.0;
  print("Attack Titan Power Point: ${attack.powerPoint}");
  print(attack.punch());
  print("");

  BeastTitan beast = BeastTitan();
  beast.powerPoint = 4.0; // Nilai < 5 otomatis jadi 5.0[cite: 2]
  print("Beast Titan Power Point: ${beast.powerPoint}");
  print(beast.lempar());
  print("");

  Human human = Human();
  human.powerPoint = 10.0;
  print("Human Power Point: ${human.powerPoint}");
  print(human.killAlltitan());
}