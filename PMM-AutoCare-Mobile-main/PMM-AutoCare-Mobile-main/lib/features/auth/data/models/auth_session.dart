class AuthSession {
  const AuthSession({required this.role, this.name});

  final String role;
  final String? name;

  bool get isOwner => role.toLowerCase() == 'pemilik';
  bool get isCustomer => !isOwner;
}
