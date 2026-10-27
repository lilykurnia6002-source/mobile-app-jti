import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../../../common/theme/app_theme.dart';
import '../../data/models/dashboard_model.dart';
import '../../data/providers/dashboard_provider.dart';
import '../widgets/quick_action_cards.dart';
import '../widgets/riwayat_card.dart';
import '../widgets/servis_aktif_card.dart';

class DashboardScreen extends ConsumerWidget {
  static const routePath = '/beranda';

  const DashboardScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final dashboard = ref.watch(dashboardProvider);

    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        bottom: false,
        child: Column(
          children: [
            // Sticky Top Header bar
            const _StickyHeader(),
            // Konten Beranda scrollable
            Expanded(
              child: RefreshIndicator(
                color: AppColors.primaryContainer,
                onRefresh: () => ref.refresh(dashboardProvider.future),
                child: dashboard.when(
                  loading: () =>
                      const Center(child: CircularProgressIndicator()),
                  error: (error, _) => ListView(
                    padding: const EdgeInsets.all(AppSpacing.xl),
                    children: [
                      const SizedBox(height: AppSpacing.xxl),
                      Icon(
                        Icons.cloud_off_rounded,
                        size: 40,
                        color: AppColors.outline,
                        semanticLabel: 'Gagal memuat dashboard',
                      ),
                      const SizedBox(height: AppSpacing.md),
                      Text(
                        'Gagal memuat dashboard',
                        textAlign: TextAlign.center,
                        style: Theme.of(context).textTheme.titleMedium,
                      ),
                      const SizedBox(height: AppSpacing.xs),
                      Text(
                        '$error',
                        textAlign: TextAlign.center,
                        style: Theme.of(context).textTheme.bodySmall,
                      ),
                    ],
                  ),
                  data: (data) => _DashboardBody(data: data),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

/// Header bar di bagian paling atas dengan logo PMM AUTO dan avatar profil
class _StickyHeader extends StatelessWidget {
  const _StickyHeader();

  @override
  Widget build(BuildContext context) {
    final textTheme = Theme.of(context).textTheme;

    return Container(
      height: 60,
      padding: const EdgeInsets.symmetric(horizontal: AppSpacing.lg),
      decoration: const BoxDecoration(
        color: AppColors.surface,
        border: Border(
          bottom: BorderSide(color: AppColors.borderSubtle, width: 1),
        ),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          // Brand Logo Lockup
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

          // Judul Beranda & Foto Profil kecil
          Row(
            children: [
              Text(
                'Beranda',
                style: textTheme.labelLarge?.copyWith(
                  color: AppColors.textPrimary,
                ),
              ),
              const SizedBox(width: AppSpacing.sm),
              Container(
                width: 34,
                height: 34,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: AppColors.surfaceContainer,
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

class _DashboardBody extends StatelessWidget {
  const _DashboardBody({required this.data});
  final DashboardModel data;

  @override
  Widget build(BuildContext context) {
    return ListView(
      padding: EdgeInsets.zero,
      children: [
        // 1. Hero Banner Atas
        _HeroBanner(nama: data.nama),

        // 2. Overlapping Card Area
        Transform.translate(
          offset: const Offset(0, -40),
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: AppSpacing.lg),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Kartu Servis Berjalan Aktif
                if (data.servisAktif != null)
                  ServisAktifCard(
                    servisAktif: data.servisAktif!,
                    onDetailTap: () => context.push(
                      '/pesanan-detail/${data.servisAktif!.idPesanan}',
                    ),
                  )
                else
                  _buildEmptyActiveService(context),

                const SizedBox(height: AppSpacing.lg),

                // Quick Action Cards (Pesan Servis & Mobil Saya)
                QuickActionCards(
                  jumlahMobil: data.jumlahMobil,
                  onPesanServisTap: () => context.push('/pesan-servis'),
                  onMobilSayaTap: () => context.push('/mobil'),
                ),

                const SizedBox(height: AppSpacing.xl),

                // Section Riwayat Terakhir
                _buildRiwayatHeader(context),
                const SizedBox(height: AppSpacing.sm),

                if (data.riwayat.isEmpty)
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(AppSpacing.xl),
                    decoration: BoxDecoration(
                      color: AppColors.surface,
                      borderRadius: BorderRadius.circular(AppRadii.card),
                      border: Border.all(color: AppColors.borderSubtle),
                    ),
                    child: Text(
                      'Belum ada riwayat servis.',
                      textAlign: TextAlign.center,
                      style: Theme.of(context).textTheme.bodyMedium,
                    ),
                  )
                else
                  for (final item in data.riwayat)
                    Padding(
                      padding: const EdgeInsets.only(bottom: AppSpacing.sm),
                      child: RiwayatCard(
                        item: item,
                        onTap: () =>
                            context.push('/pesanan-detail/${item.idPesanan}'),
                      ),
                    ),

                const SizedBox(height: AppSpacing.xl),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildEmptyActiveService(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(AppSpacing.xl),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(AppRadii.card),
        border: Border.all(color: AppColors.borderSubtle),
      ),
      child: Text(
        'Tidak ada servis yang sedang berjalan.',
        textAlign: TextAlign.center,
        style: Theme.of(context).textTheme.bodyMedium,
      ),
    );
  }

  Widget _buildRiwayatHeader(BuildContext context) {
    final textTheme = Theme.of(context).textTheme;

    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          'Riwayat terakhir',
          style: textTheme.titleLarge?.copyWith(
            fontSize: 17,
            color: AppColors.textPrimary,
          ),
        ),
        Semantics(
          button: true,
          label: 'Lihat semua pesanan',
          child: InkWell(
            onTap: () => context.go('/pesanan'),
            borderRadius: BorderRadius.circular(AppRadii.sm),
            child: Padding(
              padding: const EdgeInsets.symmetric(
                horizontal: AppSpacing.xs,
                vertical: AppSpacing.sm,
              ),
              child: Text(
                'Lihat semua',
                style: textTheme.labelMedium?.copyWith(
                  color: AppColors.primaryContainer,
                ),
              ),
            ),
          ),
        ),
      ],
    );
  }
}

/// Hero Banner Navy dengan sapaan dan hiasan visual
class _HeroBanner extends StatelessWidget {
  const _HeroBanner({required this.nama});
  final String nama;

  @override
  Widget build(BuildContext context) {
    final textTheme = Theme.of(context).textTheme;

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.fromLTRB(
        AppSpacing.lg,
        AppSpacing.lg,
        AppSpacing.lg,
        56,
      ),
      decoration: const BoxDecoration(
        color: AppColors.primaryContainer,
        borderRadius: BorderRadius.only(
          bottomLeft: Radius.circular(AppRadii.hero),
          bottomRight: Radius.circular(AppRadii.hero),
        ),
      ),
      child: Stack(
        clipBehavior: Clip.none,
        children: [
          // Lingkaran dekoratif di belakang
          Positioned(
            right: -25,
            top: -25,
            child: IgnorePointer(
              child: Container(
                width: 120,
                height: 120,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: AppColors.onPrimary.withValues(alpha: 0.05),
                ),
              ),
            ),
          ),
          Positioned(
            right: 40,
            bottom: -10,
            child: IgnorePointer(
              child: Container(
                width: 80,
                height: 80,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: AppColors.secondaryContainer.withValues(alpha: 0.08),
                ),
              ),
            ),
          ),

          // Konten Sapaan & Profil
          Row(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Flexible(
                          child: Text(
                            'Halo, $nama',
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: textTheme.headlineMedium?.copyWith(
                              color: AppColors.onPrimary,
                              fontSize: 22,
                            ),
                          ),
                        ),
                        const SizedBox(width: AppSpacing.sm),
                        const Text('👋', style: TextStyle(fontSize: 20)),
                      ],
                    ),
                    const SizedBox(height: AppSpacing.xs),
                    Text(
                      'Mau servis mobil apa hari ini?',
                      style: textTheme.bodySmall?.copyWith(
                        color: AppColors.onPrimary.withValues(alpha: 0.8),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(width: AppSpacing.md),

              // Avatar pengguna
              Container(
                width: 48,
                height: 48,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: AppColors.surfaceContainer,
                  border: Border.all(
                    color: AppColors.secondaryContainer.withValues(alpha: 0.5),
                    width: 2,
                  ),
                ),
                child: Center(
                  child: nama.isNotEmpty
                      ? Text(
                          nama[0].toUpperCase(),
                          style: textTheme.headlineSmall?.copyWith(
                            color: AppColors.primaryContainer,
                            fontWeight: FontWeight.w800,
                          ),
                        )
                      : const Icon(
                          Icons.person_rounded,
                          size: 20,
                          color: AppColors.primaryContainer,
                        ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
