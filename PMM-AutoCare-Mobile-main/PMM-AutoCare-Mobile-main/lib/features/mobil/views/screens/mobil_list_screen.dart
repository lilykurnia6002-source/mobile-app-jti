import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../../../common/network/api_client.dart';
import '../../../../common/theme/app_theme.dart';
import '../../data/models/mobil_model.dart';
import '../../data/providers/mobil_provider.dart';

/// "Mobil Saya" — list of the logged-in pelanggan's real vehicles
/// (`GET /mobil`), with add/edit wired to `POST`/`PUT /mobil`. There is no
/// delete endpoint on the backend (`MobilController` only exposes
/// index/store/update — see routes/api.php), so no delete UI exists here.
class MobilListScreen extends ConsumerWidget {
  static const routePath = '/mobil';

  const MobilListScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final mobil = ref.watch(mobilListProvider);

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: AppColors.surface,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(
            Icons.arrow_back_rounded,
            color: AppColors.primaryContainer,
          ),
          onPressed: () => context.pop(),
        ),
        title: const Text(
          'Mobil Saya',
          style: TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.w700,
            color: AppColors.primaryContainer,
          ),
        ),
      ),
      body: RefreshIndicator(
        color: AppColors.primaryContainer,
        onRefresh: () => ref.read(mobilListProvider.notifier).refresh(),
        child: mobil.when(
          loading: () => const Center(child: CircularProgressIndicator()),
          error: (error, _) => _errorState(ref, error),
          data: (items) => _content(context, items),
        ),
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => _openForm(context),
        backgroundColor: AppColors.primaryContainer,
        foregroundColor: Colors.white,
        icon: const Icon(Icons.add_rounded),
        label: const Text(
          'Tambah Mobil',
          style: TextStyle(fontWeight: FontWeight.w700),
        ),
      ),
    );
  }

  Widget _content(BuildContext context, List<MobilModel> items) {
    if (items.isEmpty) return _emptyState(context);
    return ListView.separated(
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 96),
      itemCount: items.length,
      separatorBuilder: (_, _) => const SizedBox(height: 12),
      itemBuilder: (context, index) => _vehicleCard(context, items[index]),
    );
  }

  Widget _vehicleCard(BuildContext context, MobilModel item) {
    return InkWell(
      onTap: () => _openForm(context, existing: item),
      borderRadius: BorderRadius.circular(16),
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: AppColors.borderSubtle),
        ),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: AppColors.surfaceContainer,
                borderRadius: BorderRadius.circular(12),
              ),
              child: const Icon(
                Icons.directions_car_rounded,
                color: AppColors.primaryContainer,
              ),
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    item.nama,
                    style: const TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.w700,
                      color: AppColors.textPrimary,
                    ),
                  ),
                  const SizedBox(height: 3),
                  Text(
                    '${item.platNomor} • ${item.tahun}',
                    style: const TextStyle(
                      fontSize: 13,
                      color: AppColors.onSurfaceVariant,
                    ),
                  ),
                ],
              ),
            ),
            const Icon(Icons.chevron_right_rounded, color: AppColors.outline),
          ],
        ),
      ),
    );
  }

  Widget _emptyState(BuildContext context) => ListView(
    padding: const EdgeInsets.all(32),
    children: [
      const SizedBox(height: 64),
      const Icon(
        Icons.directions_car_outlined,
        size: 42,
        color: AppColors.outline,
      ),
      const SizedBox(height: 12),
      const Text(
        'Belum Ada Mobil',
        textAlign: TextAlign.center,
        style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700),
      ),
      const SizedBox(height: 6),
      const Text(
        'Akun Anda belum memiliki mobil. Tambahkan mobil untuk mulai booking servis.',
        textAlign: TextAlign.center,
        style: TextStyle(fontSize: 12.5, color: AppColors.onSurfaceVariant),
      ),
      const SizedBox(height: 20),
      Center(
        child: FilledButton.icon(
          onPressed: () => _openForm(context),
          style: FilledButton.styleFrom(
            backgroundColor: AppColors.primaryContainer,
          ),
          icon: const Icon(Icons.add_rounded),
          label: const Text('Tambah Mobil'),
        ),
      ),
    ],
  );

  Widget _errorState(WidgetRef ref, Object error) => Center(
    child: Padding(
      padding: const EdgeInsets.all(24),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          const Icon(
            Icons.error_outline_rounded,
            color: AppColors.statusFailedText,
            size: 36,
          ),
          const SizedBox(height: 12),
          Text(error.toString(), textAlign: TextAlign.center),
          const SizedBox(height: 12),
          OutlinedButton(
            onPressed: () => ref.invalidate(mobilListProvider),
            child: const Text('Coba lagi'),
          ),
        ],
      ),
    ),
  );

  void _openForm(BuildContext context, {MobilModel? existing}) {
    showModalBottomSheet<void>(
      context: context,
      isScrollControlled: true,
      builder: (_) => _MobilFormSheet(existing: existing),
    );
  }
}

class _MobilFormSheet extends ConsumerStatefulWidget {
  const _MobilFormSheet({this.existing});

  final MobilModel? existing;

  @override
  ConsumerState<_MobilFormSheet> createState() => _MobilFormSheetState();
}

class _MobilFormSheetState extends ConsumerState<_MobilFormSheet> {
  final _formKey = GlobalKey<FormState>();
  late final TextEditingController _platNomor;
  late final TextEditingController _merk;
  late final TextEditingController _tipe;
  late final TextEditingController _tahun;
  bool _saving = false;
  String? _error;

  // Pesan error per-field dari respons 422 backend
  // (`StoreMobilRequest`/`UpdateMobilRequest::rules()`), keyed dengan nama
  // field backend.
  Map<String, List<String>>? _fieldErrors;

  bool get _isEdit => widget.existing != null;

  @override
  void initState() {
    super.initState();
    final existing = widget.existing;
    _platNomor = TextEditingController(text: existing?.platNomor ?? '');
    _merk = TextEditingController(text: existing?.merk ?? '');
    _tipe = TextEditingController(text: existing?.tipe ?? '');
    _tahun = TextEditingController(text: existing?.tahun.toString() ?? '');
  }

  @override
  void dispose() {
    _platNomor.dispose();
    _merk.dispose();
    _tipe.dispose();
    _tahun.dispose();
    super.dispose();
  }

  String? _fieldError(String field) => _fieldErrors?[field]?.first;

  Future<void> _save() async {
    if (!(_formKey.currentState?.validate() ?? false)) return;
    setState(() {
      _saving = true;
      _error = null;
      _fieldErrors = null;
    });
    try {
      final input = MobilInput(
        platNomor: _platNomor.text.trim(),
        merk: _merk.text.trim(),
        tipe: _tipe.text.trim(),
        tahun: int.parse(_tahun.text.trim()),
      );
      final notifier = ref.read(mobilListProvider.notifier);
      if (_isEdit) {
        await notifier.editMobil(widget.existing!.id, input);
      } else {
        await notifier.addMobil(input);
      }
      if (!mounted) return;
      Navigator.of(context).pop();
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(_isEdit ? 'Mobil diperbarui' : 'Mobil ditambahkan'),
        ),
      );
    } on ApiException catch (error) {
      setState(() {
        _fieldErrors = error.fieldErrors;
        _error = error.fieldErrors == null ? error.message : null;
      });
    } finally {
      if (mounted) setState(() => _saving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final bottomInset = MediaQuery.viewInsetsOf(context).bottom;
    final maxTahun = DateTime.now().year + 1;
    return Padding(
      padding: EdgeInsets.fromLTRB(20, 20, 20, bottomInset + 20),
      child: Form(
        key: _formKey,
        child: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                _isEdit ? 'Ubah mobil' : 'Tambah mobil',
                style: Theme.of(context).textTheme.headlineSmall,
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: _platNomor,
                decoration: InputDecoration(
                  labelText: 'Plat nomor',
                  errorText: _fieldError('plat_nomor'),
                ),
                textCapitalization: TextCapitalization.characters,
                validator: (value) {
                  final v = value?.trim() ?? '';
                  if (v.isEmpty) return 'Plat nomor wajib diisi.';
                  if (v.length > 20) {
                    return 'Plat nomor maksimal 20 karakter.';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _merk,
                decoration: InputDecoration(
                  labelText: 'Merk',
                  errorText: _fieldError('merk'),
                ),
                textCapitalization: TextCapitalization.words,
                validator: (value) {
                  final v = value?.trim() ?? '';
                  if (v.isEmpty) return 'Merk wajib diisi.';
                  if (v.length > 50) return 'Merk maksimal 50 karakter.';
                  return null;
                },
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _tipe,
                decoration: InputDecoration(
                  labelText: 'Tipe',
                  errorText: _fieldError('tipe'),
                ),
                textCapitalization: TextCapitalization.words,
                validator: (value) {
                  final v = value?.trim() ?? '';
                  if (v.isEmpty) return 'Tipe wajib diisi.';
                  if (v.length > 50) return 'Tipe maksimal 50 karakter.';
                  return null;
                },
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _tahun,
                decoration: InputDecoration(
                  labelText: 'Tahun',
                  errorText: _fieldError('tahun'),
                ),
                keyboardType: TextInputType.number,
                validator: (value) {
                  final v = value?.trim() ?? '';
                  if (v.isEmpty) return 'Tahun wajib diisi.';
                  if (!RegExp(r'^\d{4}$').hasMatch(v)) {
                    return 'Tahun harus 4 digit angka.';
                  }
                  final year = int.parse(v);
                  if (year < 1900 || year > maxTahun) {
                    return 'Tahun harus antara 1900–$maxTahun.';
                  }
                  return null;
                },
              ),
              if (_error != null) ...[
                const SizedBox(height: 12),
                Text(_error!, style: const TextStyle(color: Colors.red)),
              ],
              const SizedBox(height: 20),
              SizedBox(
                width: double.infinity,
                child: FilledButton(
                  onPressed: _saving ? null : _save,
                  style: FilledButton.styleFrom(
                    backgroundColor: AppColors.primaryContainer,
                  ),
                  child: _saving
                      ? const SizedBox(
                          height: 20,
                          width: 20,
                          child: CircularProgressIndicator(
                            color: AppColors.onPrimary,
                            strokeWidth: 2,
                          ),
                        )
                      : Text(_isEdit ? 'Simpan perubahan' : 'Tambah mobil'),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
