import 'dart:io';

void main() {
  stdout.write("Apakah anda ingin menginstall aplikasi? (Y/T): ");
  String input = stdin.readLineSync() ?? "";

  var result = (input.toUpperCase() == "Y")
      ? "anda akan menginstall aplikasi dart"
      : "aborted";

  print(result);
}