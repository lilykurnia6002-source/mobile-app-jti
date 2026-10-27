import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../../../common/theme/app_theme.dart';
import '../../../auth/data/providers/auth_provider.dart';
import '../../data/models/profile_model.dart';
import '../../data/providers/profile_provider.dart';

class ProfilScreen extends ConsumerWidget {
  static const routePath = '/profil';

  const ProfilScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final profile = ref.watch(profileProvider);

    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        bottom: false,
        child: Column(
          children: [
            const _ProfileHeader(),
            Expanded(
              child: RefreshIndicator(
                color: AppColors.primaryContainer,
                onRefresh: () => ref.refresh(profileProvider.future),
                child: profile.when(
                  loading: () =>
                      const Center(child: CircularProgressIndicator()),
                  error: (error, _) => _ProfileLoadError(
                    error: error,
                    onRetry: () => ref.invalidate(profileProvider),
                  ),
                  data: (profile) => _ProfileBody(profile: profile),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _ProfileHeader extends StatelessWidget {
  const _ProfileHeader();

  @override
  Widget build(BuildContext context) {
    final textTheme = Theme.of(context).textTheme;

    return Container(
      height: 60,
      padding: const EdgeInsets.symmetric(horizontal: AppSpacing.lg),
      decoration: const BoxDecoration(
        color: AppColors.surface,
        border: Border(bottom: BorderSide(color: AppColors.borderSubtle)),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: AppSpacing.sm,
                  vertical: AppSpacing.xs,
                ),
                decoration: BoxDecoration(
                  color: AppColors.primaryContainer,
                  borderRadius: BorderRadius.circular(AppRadii.sm),
                ),
                child: Text(
                  'PMM',
                  style: textTheme.labelMedium?.copyWith(
                    color: AppColors.onPrimary,
                    fontWeight: FontWeight.w800,
                    letterSpacing: 0.5,
                  ),
                ),
              ),
              const SizedBox(width: AppSpacing.sm),
              Text(
                'AUTO',
                style: textTheme.titleSmall?.copyWith(
                  color: AppColors.primaryContainer,
                  fontWeight: FontWeight.w800,
                  letterSpacing: 0.8,
                ),
              ),
            ],
          ),
          Row(
            children: [
              Text(
                'Profil',
                style: textTheme.labelLarge?.copyWith(
                  color: AppColors.textPrimary,
                ),
              ),
              const SizedBox(width: AppSpacing.sm),
              Container(
                width: 34,
                height: 34,
                decoration: BoxDecoration(
                  color: AppColors.surfaceContainer,
                  shape: BoxShape.circle,
                  border: Border.all(
                    color: AppColors.primaryContainer.withValues(alpha: 0.25),
                    width: 1.5,
                  ),
                ),
                child: const Icon(
                  Icons.person_rounded,
                  size: 20,
                  color: AppColors.primaryContainer,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}

class _ProfileBody extends ConsumerWidget {
  const _ProfileBody({required this.profile});

  final ProfileModel profile;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return ListView(
      padding: const EdgeInsets.only(bottom: AppSpacing.xxl),
      children: [
        _ProfileHero(profile: profile),
        Transform.translate(
          offset: const Offset(0, -28),
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: AppSpacing.lg),
            child: _ProfileStats(profile: profile),
          ),
        ),
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: AppSpacing.lg),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const _SectionLabel('AKUN & KENDARAAN'),
              const SizedBox(height: AppSpacing.sm),
              _ProfileDetails(profile: profile),
              const SizedBox(height: AppSpacing.sm),
              _ActionCard(
                icon: Icons.edit_rounded,
                title: 'Ubah profil',
                subtitle: 'Kelola data diri & kontak',
                onTap: () => showModalBottomSheet<void>(
                  context: context,
                  isScrollControlled: true,
                  useSafeArea: true,
                  backgroundColor: Colors.transparent,
                  builder: (_) => _ProfileEditSheet(profile: profile),
                ),
              ),
              const SizedBox(height: AppSpacing.sm),
              _ActionCard(
                icon: Icons.directions_car_rounded,
                title: 'Mobil saya',
                subtitle: 'Lihat kendaraan yang terdaftar',
                onTap: () => context.push('/mobil'),
              ),
              const SizedBox(height: AppSpacing.sm),
              _ActionCard(
                icon: Icons.receipt_long_rounded,
                title: 'Riwayat servis',
                subtitle: 'Lihat pesanan dan riwayat servis',
                onTap: () => context.go('/pesanan'),
              ),
              const SizedBox(height: AppSpacing.xl),
              const _SectionLabel('PREFERENSI & SESI'),
              const SizedBox(height: AppSpacing.sm),
              _ActionCard(
                icon: Icons.notifications_active_outlined,
                title: 'Notifikasi',
                subtitle: 'Lihat pembaruan pesanan dan servis',
                onTap: () => context.go('/notifikasi'),
              ),
              const SizedBox(height: AppSpacing.sm),
              _ActionCard(
                icon: Icons.logout_rounded,
                title: 'Keluar',
                subtitle: 'Akhiri sesi perangkat saat ini',
                iconBackground: AppColors.statusFailedBg,
                iconColor: AppColors.statusFailedText,
                titleColor: AppColors.statusFailedText,
                onTap: () => _confirmLogout(context, ref),
              ),
              const SizedBox(height: AppSpacing.xl),
              Center(
                child: Text(
                  'PMM AUTO Mobile · Solusi Servis Bengkel Terpercaya',
                  textAlign: TextAlign.center,
                  style: Theme.of(
                    context,
                  ).textTheme.labelSmall?.copyWith(color: AppColors.outline),
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Future<void> _confirmLogout(BuildContext context, WidgetRef ref) async {
    final shouldLogout = await showModalBottomSheet<bool>(
      context: context,
      useSafeArea: true,
      backgroundColor: Colors.transparent,
      builder: (sheetContext) => const _LogoutSheet(),
    );
    if (shouldLogout != true) return;

    try {
      await ref.read(authControllerProvider.notifier).logout();
      if (context.mounted) context.go('/login');
    } catch (error) {
      if (!context.mounted) return;
      ScaffoldMessenger.of(
        context,
      ).showSnackBar(SnackBar(content: Text('Gagal keluar: $error')));
    }
  }
}

class _ProfileHero extends StatelessWidget {
  const _ProfileHero({required this.profile});

  final ProfileModel profile;

  @override
  Widget build(BuildContext context) {
    final initials = _initials(profile.nama);
    final textTheme = Theme.of(context).textTheme;

    return Container(
      padding: const EdgeInsets.fromLTRB(
        AppSpacing.lg,
        AppSpacing.lg,
        AppSpacing.lg,
        52,
      ),
      decoration: const BoxDecoration(
        color: AppColors.primaryContainer,
        borderRadius: BorderRadius.vertical(
          bottom: Radius.circular(AppRadii.hero),
        ),
      ),
      child: Column(
        children: [
          Container(
            width: 82,
            height: 82,
            alignment: Alignment.center,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: AppColors.surfaceContainer,
              border: Border.all(color: AppColors.secondaryContainer, width: 3),
            ),
            child: Text(
              initials,
              semanticsLabel: 'Inisial profil ${profile.nama}',
              style: textTheme.headlineMedium?.copyWith(
                color: AppColors.primaryContainer,
                fontWeight: FontWeight.w800,
              ),
            ),
          ),
          const SizedBox(height: AppSpacing.sm),
          Text(
            profile.nama,
            textAlign: TextAlign.center,
            style: textTheme.headlineMedium?.copyWith(
              color: AppColors.onPrimary,
            ),
          ),
          const SizedBox(height: AppSpacing.xs),
          Text(
            '${profile.email} · ${profile.noHp}',
            textAlign: TextAlign.center,
            maxLines: 2,
            overflow: TextOverflow.ellipsis,
            style: textTheme.bodySmall?.copyWith(
              color: AppColors.onPrimaryContainer,
            ),
          ),
        ],
      ),
    );
  }

  String _initials(String name) {
    final words = name.trim().split(RegExp(r'\s+'));
    if (words.isEmpty || words.first.isEmpty) return '?';
    if (words.length == 1) {
      return words.first.substring(0, 1).toUpperCase();
    }
    return '${words.first.substring(0, 1)}${words.last.substring(0, 1)}'
        .toUpperCase();
  }
}

class _ProfileStats extends StatelessWidget {
  const _ProfileStats({required this.profile});

  final ProfileModel profile;

  @override
  Widget build(BuildContext context) {
    final count = profile.jumlahMobil;
    return Container(
      padding: const EdgeInsets.symmetric(vertical: AppSpacing.md),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(AppRadii.card),
        border: Border.all(color: AppColors.borderSubtle),
      ),
      child: _StatItem(
        icon: Icons.directions_car_rounded,
        value: count == null ? '—' : '$count',
        label: 'Mobil terdaftar',
      ),
    );
  }
}

class _StatItem extends StatelessWidget {
  const _StatItem({
    required this.icon,
    required this.value,
    required this.label,
  });

  final IconData icon;
  final String value;
  final String label;

  @override
  Widget build(BuildContext context) {
    final textTheme = Theme.of(context).textTheme;
    return Column(
      children: [
        Container(
          width: 36,
          height: 36,
          alignment: Alignment.center,
          decoration: const BoxDecoration(
            color: AppColors.surfaceContainer,
            shape: BoxShape.circle,
          ),
          child: Icon(icon, size: 20, color: AppColors.primaryContainer),
        ),
        const SizedBox(height: AppSpacing.xs),
        Text(
          value,
          style: textTheme.titleSmall?.copyWith(
            color: AppColors.textPrimary,
            fontWeight: FontWeight.w700,
          ),
        ),
        Text(label, style: textTheme.labelSmall, textAlign: TextAlign.center),
      ],
    );
  }
}

class _SectionLabel extends StatelessWidget {
  const _SectionLabel(this.label);

  final String label;

  @override
  Widget build(BuildContext context) {
    return Text(
      label,
      style: Theme.of(context).textTheme.labelMedium?.copyWith(
        color: AppColors.onSurfaceVariant,
        letterSpacing: 0.8,
      ),
    );
  }
}

class _ProfileDetails extends StatelessWidget {
  const _ProfileDetails({required this.profile});

  final ProfileModel profile;

  @override
  Widget build(BuildContext context) {
    return _SurfaceCard(
      children: [
        _DetailRow(
          icon: Icons.person_outline_rounded,
          label: 'Nama lengkap',
          value: profile.nama,
        ),
        const _CardDivider(),
        _DetailRow(
          icon: Icons.email_outlined,
          label: 'Email',
          value: profile.email,
        ),
        const _CardDivider(),
        _DetailRow(
          icon: Icons.phone_outlined,
          label: 'Nomor HP',
          value: profile.noHp,
        ),
        if (profile.alamat?.isNotEmpty == true) ...[
          const _CardDivider(),
          _DetailRow(
            icon: Icons.location_on_outlined,
            label: 'Alamat',
            value: profile.alamat!,
          ),
        ],
        if (profile.jumlahMobil != null) ...[
          const _CardDivider(),
          _DetailRow(
            icon: Icons.directions_car_outlined,
            label: 'Jumlah mobil',
            value: '${profile.jumlahMobil}',
          ),
        ],
      ],
    );
  }
}

class _SurfaceCard extends StatelessWidget {
  const _SurfaceCard({required this.children});

  final List<Widget> children;

  @override
  Widget build(BuildContext context) {
    return Container(
      clipBehavior: Clip.antiAlias,
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(AppRadii.card),
        border: Border.all(color: AppColors.borderSubtle),
      ),
      child: Column(children: children),
    );
  }
}

class _DetailRow extends StatelessWidget {
  const _DetailRow({
    required this.icon,
    required this.label,
    required this.value,
  });

  final IconData icon;
  final String label;
  final String value;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.all(AppSpacing.md),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 36,
            height: 36,
            alignment: Alignment.center,
            decoration: BoxDecoration(
              color: AppColors.surfaceContainerLow,
              borderRadius: BorderRadius.circular(AppRadii.sm),
            ),
            child: Icon(icon, size: 20, color: AppColors.primaryContainer),
          ),
          const SizedBox(width: AppSpacing.sm),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(label, style: Theme.of(context).textTheme.labelSmall),
                const SizedBox(height: AppSpacing.xs),
                Text(
                  value,
                  style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                    color: AppColors.onSurface,
                    fontWeight: FontWeight.w500,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _CardDivider extends StatelessWidget {
  const _CardDivider();

  @override
  Widget build(BuildContext context) => const Divider(
    height: 1,
    thickness: 1,
    indent: 60,
    color: AppColors.borderSubtle,
  );
}

class _ActionCard extends StatelessWidget {
  const _ActionCard({
    required this.icon,
    required this.title,
    required this.subtitle,
    required this.onTap,
    this.iconBackground = AppColors.surfaceContainerLow,
    this.iconColor = AppColors.primaryContainer,
    this.titleColor = AppColors.onSurface,
  });

  final IconData icon;
  final String title;
  final String subtitle;
  final VoidCallback onTap;
  final Color iconBackground;
  final Color iconColor;
  final Color titleColor;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: AppColors.surface,
      borderRadius: BorderRadius.circular(AppRadii.card),
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: onTap,
        child: Container(
          constraints: const BoxConstraints(minHeight: 72),
          padding: const EdgeInsets.all(AppSpacing.md),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(AppRadii.card),
            border: Border.all(color: AppColors.borderSubtle),
          ),
          child: Row(
            children: [
              Container(
                width: 36,
                height: 36,
                alignment: Alignment.center,
                decoration: BoxDecoration(
                  color: iconBackground,
                  borderRadius: BorderRadius.circular(AppRadii.sm),
                ),
                child: Icon(icon, size: 20, color: iconColor),
              ),
              const SizedBox(width: AppSpacing.sm),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title,
                      style: Theme.of(
                        context,
                      ).textTheme.titleSmall?.copyWith(color: titleColor),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      subtitle,
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      style: Theme.of(context).textTheme.labelSmall,
                    ),
                  ],
                ),
              ),
              const SizedBox(width: AppSpacing.sm),
              Icon(
                Icons.chevron_right_rounded,
                color: titleColor == AppColors.statusFailedText
                    ? AppColors.statusFailedText
                    : AppColors.outline,
                size: 22,
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _LogoutSheet extends StatelessWidget {
  const _LogoutSheet();

  @override
  Widget build(BuildContext context) {
    return Material(
      color: AppColors.surface,
      borderRadius: const BorderRadius.vertical(
        top: Radius.circular(AppRadii.hero),
      ),
      clipBehavior: Clip.antiAlias,
      child: Padding(
        padding: const EdgeInsets.fromLTRB(
          AppSpacing.xl,
          AppSpacing.md,
          AppSpacing.xl,
          AppSpacing.xl,
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Center(
              child: Container(
                width: 44,
                height: 4,
                decoration: BoxDecoration(
                  color: AppColors.borderSubtle,
                  borderRadius: BorderRadius.circular(AppRadii.pill),
                ),
              ),
            ),
            const SizedBox(height: AppSpacing.lg),
            Row(
              children: [
                Container(
                  width: 44,
                  height: 44,
                  alignment: Alignment.center,
                  decoration: const BoxDecoration(
                    color: AppColors.statusFailedBg,
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(
                    Icons.logout_rounded,
                    color: AppColors.statusFailedText,
                  ),
                ),
                const SizedBox(width: AppSpacing.md),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Konfirmasi keluar',
                        style: Theme.of(context).textTheme.titleLarge,
                      ),
                      const SizedBox(height: AppSpacing.xs),
                      Text(
                        'Anda yakin ingin keluar dari akun PMM AUTO?',
                        style: Theme.of(context).textTheme.bodyMedium,
                      ),
                    ],
                  ),
                ),
              ],
            ),
            const SizedBox(height: AppSpacing.lg),
            Row(
              children: [
                Expanded(
                  child: OutlinedButton(
                    onPressed: () => Navigator.of(context).pop(false),
                    child: const Text('Batal'),
                  ),
                ),
                const SizedBox(width: AppSpacing.sm),
                Expanded(
                  child: FilledButton(
                    style: FilledButton.styleFrom(
                      backgroundColor: AppColors.statusFailedText,
                    ),
                    onPressed: () => Navigator.of(context).pop(true),
                    child: const Text('Keluar'),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

class _ProfileEditSheet extends ConsumerStatefulWidget {
  const _ProfileEditSheet({required this.profile});

  final ProfileModel profile;

  @override
  ConsumerState<_ProfileEditSheet> createState() => _ProfileEditSheetState();
}

class _ProfileEditSheetState extends ConsumerState<_ProfileEditSheet> {
  final _formKey = GlobalKey<FormState>();
  late final TextEditingController _nama;
  late final TextEditingController _email;
  late final TextEditingController _noHp;
  late final TextEditingController _alamat;
  final _password = TextEditingController();
  bool _saving = false;

  @override
  void initState() {
    super.initState();
    _nama = TextEditingController(text: widget.profile.nama);
    _email = TextEditingController(text: widget.profile.email);
    _noHp = TextEditingController(text: widget.profile.noHp);
    _alamat = TextEditingController(text: widget.profile.alamat ?? '');
  }

  @override
  void dispose() {
    _nama.dispose();
    _email.dispose();
    _noHp.dispose();
    _alamat.dispose();
    _password.dispose();
    super.dispose();
  }

  Future<void> _save() async {
    if (!(_formKey.currentState?.validate() ?? false)) return;
    setState(() => _saving = true);
    try {
      await ref
          .read(profileProvider.notifier)
          .saveProfile(
            ProfileUpdate(
              nama: _nama.text.trim(),
              email: _email.text.trim(),
              noHp: _noHp.text.trim(),
              alamat: _alamat.text.trim().isEmpty ? null : _alamat.text.trim(),
              password: _password.text,
            ),
          );
      if (!mounted) return;
      Navigator.of(context).pop();
      ScaffoldMessenger.of(
        context,
      ).showSnackBar(const SnackBar(content: Text('Profil diperbarui')));
    } catch (error) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Gagal memperbarui profil: $error')),
        );
      }
    } finally {
      if (mounted) setState(() => _saving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final bottomInset = MediaQuery.viewInsetsOf(context).bottom;
    return Material(
      color: AppColors.surface,
      borderRadius: const BorderRadius.vertical(
        top: Radius.circular(AppRadii.hero),
      ),
      clipBehavior: Clip.antiAlias,
      child: Padding(
        padding: EdgeInsets.fromLTRB(
          AppSpacing.lg,
          AppSpacing.md,
          AppSpacing.lg,
          bottomInset + AppSpacing.lg,
        ),
        child: Form(
          key: _formKey,
          child: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Center(
                  child: Container(
                    width: 44,
                    height: 4,
                    decoration: BoxDecoration(
                      color: AppColors.borderSubtle,
                      borderRadius: BorderRadius.circular(AppRadii.pill),
                    ),
                  ),
                ),
                const SizedBox(height: AppSpacing.lg),
                Text(
                  'Ubah profil',
                  style: Theme.of(context).textTheme.headlineSmall,
                ),
                const SizedBox(height: AppSpacing.xs),
                Text(
                  'Perbarui data yang terhubung ke akun Anda.',
                  style: Theme.of(context).textTheme.bodyMedium,
                ),
                const SizedBox(height: AppSpacing.lg),
                _ProfileField(
                  controller: _nama,
                  label: 'Nama',
                  enabled: !_saving,
                  validator: (value) => value == null || value.trim().isEmpty
                      ? 'Nama wajib diisi.'
                      : null,
                ),
                const SizedBox(height: AppSpacing.md),
                _ProfileField(
                  controller: _email,
                  label: 'Email',
                  enabled: !_saving,
                  keyboardType: TextInputType.emailAddress,
                  validator: (value) {
                    final email = value?.trim() ?? '';
                    return email.contains('@')
                        ? null
                        : 'Masukkan email yang valid.';
                  },
                ),
                const SizedBox(height: AppSpacing.md),
                _ProfileField(
                  controller: _noHp,
                  label: 'Nomor HP',
                  enabled: !_saving,
                  keyboardType: TextInputType.phone,
                  inputFormatters: [FilteringTextInputFormatter.digitsOnly],
                  maxLength: 20,
                  validator: (value) {
                    final phone = value?.trim() ?? '';
                    if (phone.isEmpty) return 'Nomor HP wajib diisi.';
                    if (!RegExp(r'^[0-9]+$').hasMatch(phone)) {
                      return 'Nomor HP hanya boleh berisi angka.';
                    }
                    if (phone.length > 20) {
                      return 'Nomor HP maksimal 20 karakter.';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: AppSpacing.md),
                _ProfileField(
                  controller: _alamat,
                  label: 'Alamat',
                  enabled: !_saving,
                  maxLines: 3,
                ),
                const SizedBox(height: AppSpacing.md),
                _ProfileField(
                  controller: _password,
                  label: 'Password baru (opsional)',
                  enabled: !_saving,
                  obscureText: true,
                  validator: (value) {
                    if (value == null || value.isEmpty || value.length >= 8) {
                      return null;
                    }
                    return 'Password minimal 8 karakter.';
                  },
                ),
                const SizedBox(height: AppSpacing.lg),
                SizedBox(
                  width: double.infinity,
                  child: FilledButton(
                    onPressed: _saving ? null : _save,
                    child: _saving
                        ? const SizedBox(
                            height: 20,
                            width: 20,
                            child: CircularProgressIndicator(
                              color: AppColors.onPrimary,
                              strokeWidth: 2,
                            ),
                          )
                        : const Text('Simpan perubahan'),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class _ProfileField extends StatelessWidget {
  const _ProfileField({
    required this.controller,
    required this.label,
    this.validator,
    this.keyboardType,
    this.inputFormatters,
    this.maxLength,
    this.maxLines = 1,
    this.obscureText = false,
    this.enabled = true,
  });

  final TextEditingController controller;
  final String label;
  final String? Function(String?)? validator;
  final TextInputType? keyboardType;
  final List<TextInputFormatter>? inputFormatters;
  final int? maxLength;
  final int maxLines;
  final bool obscureText;
  final bool enabled;

  @override
  Widget build(BuildContext context) {
    return TextFormField(
      controller: controller,
      enabled: enabled,
      validator: validator,
      keyboardType: keyboardType,
      inputFormatters: inputFormatters,
      maxLength: maxLength,
      maxLines: maxLines,
      obscureText: obscureText,
      textInputAction: maxLines > 1
          ? TextInputAction.newline
          : TextInputAction.next,
      decoration: InputDecoration(labelText: label),
    );
  }
}

class _ProfileLoadError extends StatelessWidget {
  const _ProfileLoadError({required this.error, required this.onRetry});

  final Object error;
  final VoidCallback onRetry;

  @override
  Widget build(BuildContext context) {
    return ListView(
      padding: const EdgeInsets.all(AppSpacing.xl),
      children: [
        const SizedBox(height: AppSpacing.xxl),
        const Icon(
          Icons.cloud_off_rounded,
          size: 40,
          color: AppColors.outline,
          semanticLabel: 'Gagal memuat profil',
        ),
        const SizedBox(height: AppSpacing.md),
        Text(
          'Gagal memuat profil',
          textAlign: TextAlign.center,
          style: Theme.of(context).textTheme.titleMedium,
        ),
        const SizedBox(height: AppSpacing.xs),
        Text(
          '$error',
          textAlign: TextAlign.center,
          style: Theme.of(context).textTheme.bodySmall,
        ),
        const SizedBox(height: AppSpacing.md),
        Center(
          child: OutlinedButton(
            onPressed: onRetry,
            child: const Text('Coba lagi'),
          ),
        ),
      ],
    );
  }
}
