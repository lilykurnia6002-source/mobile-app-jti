import 'employee.dart';

void main(List<String> args) {
  Employee emp = Employee(
    id: "1",
    name: "Lily",
    department: "Teknologi Informasi",
  );

  print("ID: ${emp.id}");
  print("Nama: ${emp.name}");
  print("Departement: ${emp.department}");
}