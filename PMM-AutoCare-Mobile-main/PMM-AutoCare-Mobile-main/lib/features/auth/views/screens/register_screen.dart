import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../../../common/network/api_client.dart';
import '../../data/providers/auth_provider.dart';
import 'login_screen.dart';

/// Registrasi akun pelanggan baru lewat `POST /auth/register`. Backend
/// membuat user + profil pelanggan sekaligus dan langsung mengembalikan
/// token (auto-login) — lihat `AuthController::register` di backend — jadi
/// alur ini mendaftar lalu langsung masuk ke `/beranda`, sama seperti login.
class RegisterScreen extends ConsumerStatefulWidget {
  static const routePath = '/registrasi';

  const RegisterScreen({super.key});

  @override
  ConsumerState<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends ConsumerState<RegisterScreen> {
  final _formKey = GlobalKey<FormState>();
  final _namaController = TextEditingController();
  final _emailController = TextEditingController();
  final _noHpController = TextEditingController();
  final _alamatController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _loading = false;
  String? _error;

  // Pesan error per-field dari respons 422 backend
  // (`RegisterRequest::rules()`), keyed dengan nama field backend.
  Map<String, List<String>>? _fieldErrors;

  @override
  void dispose() {
    _namaController.dispose();
    _emailController.dispose();
    _noHpController.dispose();
    _alamatController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  String? _fieldError(String field) => _fieldErrors?[field]?.first;

  Future<void> _register() async {
    if (!(_formKey.currentState?.validate() ?? false)) return;

    setState(() {
      _loading = true;
      _error = null;
      _fieldErrors = null;
    });

    try {
      await ref
          .read(authControllerProvider.notifier)
          .register(
            nama: _namaController.text.trim(),
            email: _emailController.text.trim(),
            password: _passwordController.text,
            noHp: _noHpController.text.trim(),
            alamat: _alamatController.text.trim(),
          );
      if (!mounted) return;
      context.go('/beranda');
    } on ApiException catch (e) {
      setState(() {
        _fieldErrors = e.fieldErrors;
        _error = e.fieldErrors == null ? e.message : null;
      });
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Daftar Akun — PMM AutoCare')),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24),
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                TextFormField(
                  controller: _namaController,
                  decoration: InputDecoration(
                    labelText: 'Nama lengkap',
                    errorText: _fieldError('nama'),
                  ),
                  textCapitalization: TextCapitalization.words,
                  validator: (value) {
                    final v = value?.trim() ?? '';
                    if (v.isEmpty) return 'Nama wajib diisi';
                    if (v.length > 100) return 'Nama maksimal 100 karakter';
                    return null;
                  },
                ),
                const SizedBox(height: 12),
                TextFormField(
                  controller: _emailController,
                  decoration: InputDecoration(
                    labelText: 'Email',
                    errorText: _fieldError('email'),
                  ),
                  keyboardType: TextInputType.emailAddress,
                  validator: (value) {
                    final v = value?.trim() ?? '';
                    if (v.isEmpty) return 'Email wajib diisi';
                    if (v.length > 150) return 'Email maksimal 150 karakter';
                    if (!RegExp(r'^[^@\s]+@[^@\s]+\.[^@\s]+$').hasMatch(v)) {
                      return 'Format email tidak valid';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 12),
                TextFormField(
                  controller: _noHpController,
                  decoration: InputDecoration(
                    labelText: 'Nomor HP',
                    errorText: _fieldError('no_hp'),
                  ),
                  keyboardType: TextInputType.phone,
                  inputFormatters: [FilteringTextInputFormatter.digitsOnly],
                  maxLength: 20,
                  validator: (value) {
                    final v = value?.trim() ?? '';
                    if (v.isEmpty) return 'Nomor HP wajib diisi';
                    if (!RegExp(r'^[0-9]+$').hasMatch(v)) {
                      return 'Nomor HP hanya boleh berisi angka';
                    }
                    if (v.length > 20) return 'Nomor HP maksimal 20 karakter';
                    return null;
                  },
                ),
                const SizedBox(height: 12),
                TextFormField(
                  controller: _alamatController,
                  decoration: InputDecoration(
                    labelText: 'Alamat (opsional)',
                    errorText: _fieldError('alamat'),
                  ),
                  maxLines: 2,
                  validator: (value) {
                    final v = value?.trim() ?? '';
                    if (v.length > 500) return 'Alamat maksimal 500 karakter';
                    return null;
                  },
                ),
                const SizedBox(height: 12),
                TextFormField(
                  controller: _passwordController,
                  decoration: InputDecoration(
                    labelText: 'Password',
                    errorText: _fieldError('password'),
                  ),
                  obscureText: true,
                  validator: (value) {
                    final v = value ?? '';
                    if (v.isEmpty) return 'Password wajib diisi';
                    if (v.length < 8) return 'Password minimal 8 karakter';
                    return null;
                  },
                ),
                const SizedBox(height: 24),
                if (_error != null) ...[
                  Text(_error!, style: const TextStyle(color: Colors.red)),
                  const SizedBox(height: 12),
                ],
                FilledButton(
                  onPressed: _loading ? null : _register,
                  child: _loading
                      ? const SizedBox(
                          height: 20,
                          width: 20,
                          child: CircularProgressIndicator(
                            strokeWidth: 2,
                            color: Colors.white,
                          ),
                        )
                      : const Text('Daftar'),
                ),
                const SizedBox(height: 12),
                TextButton(
                  onPressed: _loading
                      ? null
                      : () => context.go(LoginScreen.routePath),
                  child: const Text('Sudah punya akun? Masuk'),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
