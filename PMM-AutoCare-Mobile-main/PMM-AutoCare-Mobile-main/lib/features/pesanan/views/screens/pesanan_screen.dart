import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';

import '../../../../common/theme/app_theme.dart';
import '../../data/models/pesanan_model.dart';
import '../../data/providers/pesanan_provider.dart';

class PesananScreen extends ConsumerStatefulWidget {
  static const routePath = '/pesanan';

  const PesananScreen({super.key});

  @override
  ConsumerState<PesananScreen> createState() => _PesananScreenState();
}

class _PesananScreenState extends ConsumerState<PesananScreen> {
  String _selectedFilter = 'all';

  @override
  Widget build(BuildContext context) {
    final pesanan = ref.watch(pesananListProvider);

    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        bottom: false,
        child: Column(
          children: [
            _buildTopBar(context),
            Expanded(
              child: pesanan.when(
                loading: _buildLoadingState,
                error: (error, _) => _buildErrorState(error),
                data: _buildContent,
              ),
            ),
          ],
        ),
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => context.push('/pesan-servis'),
        backgroundColor: AppColors.primaryContainer,
        foregroundColor: AppColors.onPrimary,
        elevation: 2,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppRadii.md),
        ),
        icon: const Icon(Icons.add_rounded),
        label: const Text(
          'Pesan Servis',
          style: TextStyle(fontWeight: FontWeight.w700),
        ),
      ),
    );
  }

  Widget _buildLoadingState() => const Center(
    child: Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        CircularProgressIndicator(),
        SizedBox(height: AppSpacing.md),
        Text(
          'Memuat riwayat pesanan...',
          style: TextStyle(color: AppColors.onSurfaceVariant),
        ),
      ],
    ),
  );

  Widget _buildContent(PesananPage page) {
    final allPesanan = page.items;
    final filtered = allPesanan.where(_matchesFilter).toList();
    final hasActiveOrders = allPesanan.any((item) => _isActive(item.status));

    return RefreshIndicator(
      color: AppColors.primaryContainer,
      backgroundColor: AppColors.surface,
      onRefresh: () {
        ref.invalidate(pesananListProvider);
        return ref.read(pesananListProvider.future);
      },
      child: ListView(
        physics: const AlwaysScrollableScrollPhysics(),
        padding: const EdgeInsets.fromLTRB(
          AppSpacing.lg,
          AppSpacing.md,
          AppSpacing.lg,
          112,
        ),
        children: [
          _buildIntro(context),
          const SizedBox(height: AppSpacing.lg),
          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.symmetric(vertical: AppSpacing.xs),
            child: Row(
              children: [
                _buildFilterChip('all', 'Semua'),
                const SizedBox(width: AppSpacing.sm),
                _buildFilterChip(
                  'active',
                  'Berjalan',
                  showActiveDot: hasActiveOrders,
                ),
                const SizedBox(width: AppSpacing.sm),
                _buildFilterChip('completed', 'Selesai'),
                const SizedBox(width: AppSpacing.sm),
                _buildFilterChip('cancelled', 'Dibatalkan'),
              ],
            ),
          ),
          const SizedBox(height: AppSpacing.md),
          Text(
            'Menampilkan ${allPesanan.length} dari ${page.total} pesanan',
            style: Theme.of(
              context,
            ).textTheme.bodySmall?.copyWith(color: AppColors.onSurfaceVariant),
          ),
          const SizedBox(height: AppSpacing.md),
          if (filtered.isEmpty)
            _buildEmptyState(hasLoadedOrders: allPesanan.isNotEmpty)
          else
            for (final item in filtered) ...[
              _buildPesananCard(item),
              const SizedBox(height: AppSpacing.md),
            ],
          if (page.hasNextPage) ...[
            const SizedBox(height: AppSpacing.xs),
            if (page.isLoadingMore)
              const Padding(
                padding: EdgeInsets.all(AppSpacing.md),
                child: Center(child: CircularProgressIndicator()),
              )
            else
              OutlinedButton.icon(
                onPressed: () =>
                    ref.read(pesananListProvider.notifier).loadMore(),
                icon: const Icon(Icons.expand_more_rounded),
                label: Text(
                  'Muat pesanan lainnya (${page.currentPage + 1}/${page.lastPage})',
                ),
              ),
          ],
          if (page.loadMoreError != null) ...[
            const SizedBox(height: AppSpacing.sm),
            Text(
              page.loadMoreError!,
              textAlign: TextAlign.center,
              style: const TextStyle(color: AppColors.statusFailedText),
            ),
            TextButton.icon(
              onPressed: () =>
                  ref.read(pesananListProvider.notifier).loadMore(),
              icon: const Icon(Icons.refresh_rounded),
              label: const Text('Coba muat lagi'),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildIntro(BuildContext context) {
    final textTheme = Theme.of(context).textTheme;
    return Row(
      crossAxisAlignment: CrossAxisAlignment.center,
      children: [
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'Pesanan Saya',
                style: textTheme.headlineMedium?.copyWith(
                  color: AppColors.primaryContainer,
                ),
              ),
              const SizedBox(height: AppSpacing.xs),
              Text(
                'Kelola jadwal dan riwayat servis berkala',
                style: textTheme.bodySmall?.copyWith(
                  color: AppColors.onSurfaceVariant,
                ),
              ),
            ],
          ),
        ),
        const SizedBox(width: AppSpacing.md),
        Container(
          width: 44,
          height: 44,
          decoration: BoxDecoration(
            color: AppColors.surfaceContainerLow,
            shape: BoxShape.circle,
          ),
          child: const Icon(
            Icons.directions_car_rounded,
            color: AppColors.primaryContainer,
            size: 22,
          ),
        ),
      ],
    );
  }

  bool _matchesFilter(PesananSummary item) {
    switch (_selectedFilter) {
      case 'active':
        return _isActive(item.status);
      case 'completed':
        return item.status == 'selesai';
      case 'cancelled':
        return item.status == 'ditolak' || item.status == 'dibatalkan';
      default:
        return true;
    }
  }

  bool _isActive(String status) =>
      status == 'menunggu' || status == 'diterima' || status == 'dikerjakan';

  Widget _buildTopBar(BuildContext context) {
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
                'Riwayat Pesanan',
                style: textTheme.titleMedium?.copyWith(
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
                  size: 19,
                  color: AppColors.primaryContainer,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildFilterChip(
    String key,
    String label, {
    bool showActiveDot = false,
  }) {
    final selected = _selectedFilter == key;
    return Semantics(
      button: true,
      selected: selected,
      label: 'Filter $label',
      child: Material(
        color: selected ? AppColors.primaryContainer : AppColors.surface,
        borderRadius: BorderRadius.circular(AppRadii.pill),
        elevation: selected ? 1 : 0,
        child: InkWell(
          onTap: () => setState(() => _selectedFilter = key),
          borderRadius: BorderRadius.circular(AppRadii.pill),
          child: ConstrainedBox(
            constraints: const BoxConstraints(minHeight: 48),
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: AppSpacing.lg),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  if (key == 'active' && showActiveDot) ...[
                    Container(
                      width: 8,
                      height: 8,
                      decoration: const BoxDecoration(
                        color: AppColors.secondaryContainer,
                        shape: BoxShape.circle,
                      ),
                    ),
                    const SizedBox(width: AppSpacing.xs),
                  ],
                  Text(
                    label,
                    style: Theme.of(context).textTheme.labelMedium?.copyWith(
                      color: selected
                          ? AppColors.onPrimary
                          : AppColors.onSurfaceVariant,
                      fontWeight: selected ? FontWeight.w700 : FontWeight.w600,
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildPesananCard(PesananSummary item) {
    final currency = NumberFormat.currency(
      locale: 'id_ID',
      symbol: 'Rp',
      decimalDigits: 0,
    );
    final jadwal = _formatJadwal(
      item.tanggalSlot,
      item.jamMulai,
      item.jamSelesai,
    );
    final presentation = _statusPresentation(item.status);

    return Semantics(
      button: true,
      label:
          'Buka detail pesanan ${item.kodePesanan}. Status ${presentation.label}. $jadwal. Total biaya ${currency.format(item.totalBiaya)}.',
      child: Material(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(AppRadii.card),
        clipBehavior: Clip.antiAlias,
        child: InkWell(
          onTap: () => context.push('/pesanan-detail/${item.id}'),
          child: Padding(
            padding: const EdgeInsets.all(AppSpacing.md),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    const Icon(
                      Icons.qr_code_2_rounded,
                      size: 17,
                      color: AppColors.primaryContainer,
                    ),
                    const SizedBox(width: AppSpacing.xs),
                    Expanded(
                      child: Text(
                        item.kodePesanan,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(
                          fontSize: 12,
                          fontFamily: 'monospace',
                          fontWeight: FontWeight.w700,
                          letterSpacing: 0.3,
                          color: AppColors.primaryContainer,
                        ),
                      ),
                    ),
                    const SizedBox(width: AppSpacing.sm),
                    _statusPill(item.status),
                  ],
                ),
                const SizedBox(height: AppSpacing.md),
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(AppSpacing.sm),
                  decoration: BoxDecoration(
                    color: AppColors.surfaceContainerLow,
                    borderRadius: BorderRadius.circular(AppRadii.md),
                  ),
                  child: Row(
                    children: [
                      Container(
                        width: 44,
                        height: 44,
                        decoration: BoxDecoration(
                          color: AppColors.surface,
                          borderRadius: BorderRadius.circular(AppRadii.sm),
                        ),
                        child: Icon(
                          presentation.icon,
                          color: presentation.foreground,
                          size: 23,
                        ),
                      ),
                      const SizedBox(width: AppSpacing.sm),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'Jadwal servis',
                              style: TextStyle(
                                fontSize: 12,
                                color: AppColors.onSurfaceVariant,
                              ),
                            ),
                            const SizedBox(height: AppSpacing.xs),
                            Text(
                              jadwal,
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                              style: const TextStyle(
                                fontSize: 14,
                                fontWeight: FontWeight.w600,
                                color: AppColors.onSurface,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: AppSpacing.md),
                const Divider(height: 1),
                const SizedBox(height: AppSpacing.sm),
                Row(
                  crossAxisAlignment: CrossAxisAlignment.center,
                  children: [
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Total Biaya',
                            style: TextStyle(
                              fontSize: 12,
                              color: AppColors.onSurfaceVariant,
                            ),
                          ),
                          const SizedBox(height: AppSpacing.xs),
                          Text(
                            currency.format(item.totalBiaya),
                            style: const TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.w700,
                              color: AppColors.primaryContainer,
                            ),
                          ),
                        ],
                      ),
                    ),
                    ExcludeSemantics(
                      child: Container(
                        constraints: const BoxConstraints(minHeight: 48),
                        padding: const EdgeInsets.symmetric(
                          horizontal: AppSpacing.md,
                          vertical: AppSpacing.sm,
                        ),
                        decoration: BoxDecoration(
                          color: AppColors.primaryContainer,
                          borderRadius: BorderRadius.circular(AppRadii.md),
                        ),
                        child: const Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Text(
                              'Lihat detail',
                              style: TextStyle(
                                color: AppColors.onPrimary,
                                fontSize: 12,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                            SizedBox(width: AppSpacing.xs),
                            Icon(
                              Icons.arrow_forward_rounded,
                              color: AppColors.secondaryContainer,
                              size: 17,
                            ),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  String _formatJadwal(String? tanggal, String? mulai, String? selesai) {
    final parsedDate = tanggal == null ? null : DateTime.tryParse(tanggal);
    final formattedDate = parsedDate == null
        ? 'Jadwal belum tersedia'
        : DateFormat('d MMMM y', 'id_ID').format(parsedDate);
    if (mulai == null || selesai == null) return formattedDate;
    return '$formattedDate, $mulai–$selesai WIB';
  }

  ({Color background, Color foreground, IconData icon, String label})
  _statusPresentation(String status) {
    return switch (status) {
      'menunggu' => (
        background: AppColors.statusPendingBg,
        foreground: AppColors.statusPendingText,
        icon: Icons.pending_actions_rounded,
        label: 'Menunggu verifikasi',
      ),
      'diterima' => (
        background: AppColors.statusProgressBg,
        foreground: AppColors.statusProgressText,
        icon: Icons.assignment_turned_in_rounded,
        label: 'Pesanan diterima',
      ),
      'dikerjakan' => (
        background: AppColors.statusProgressBg,
        foreground: AppColors.statusProgressText,
        icon: Icons.build_circle_rounded,
        label: 'Sedang dikerjakan',
      ),
      'selesai' => (
        background: AppColors.statusCompletedBg,
        foreground: AppColors.statusCompletedText,
        icon: Icons.task_alt_rounded,
        label: 'Selesai',
      ),
      'ditolak' => (
        background: AppColors.statusFailedBg,
        foreground: AppColors.statusFailedText,
        icon: Icons.cancel_rounded,
        label: 'Ditolak',
      ),
      'dibatalkan' => (
        background: AppColors.statusFailedBg,
        foreground: AppColors.statusFailedText,
        icon: Icons.cancel_rounded,
        label: 'Dibatalkan',
      ),
      _ => (
        background: AppColors.surfaceContainer,
        foreground: AppColors.onSurfaceVariant,
        icon: Icons.help_outline_rounded,
        label: status.isEmpty ? 'Status tidak diketahui' : status,
      ),
    };
  }

  Widget _statusPill(String status) {
    final presentation = _statusPresentation(status);
    return Container(
      constraints: const BoxConstraints(minHeight: 32),
      padding: const EdgeInsets.symmetric(
        horizontal: AppSpacing.sm,
        vertical: AppSpacing.xs,
      ),
      decoration: BoxDecoration(
        color: presentation.background,
        borderRadius: BorderRadius.circular(AppRadii.pill),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(presentation.icon, size: 14, color: presentation.foreground),
          const SizedBox(width: AppSpacing.xs),
          Text(
            presentation.label,
            style: TextStyle(
              color: presentation.foreground,
              fontSize: 11,
              fontWeight: FontWeight.w700,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildEmptyState({required bool hasLoadedOrders}) => Padding(
    padding: const EdgeInsets.symmetric(
      horizontal: AppSpacing.lg,
      vertical: AppSpacing.xxl,
    ),
    child: Column(
      children: [
        Container(
          width: 64,
          height: 64,
          decoration: const BoxDecoration(
            color: AppColors.surfaceContainerLow,
            shape: BoxShape.circle,
          ),
          child: const Icon(
            Icons.receipt_long_rounded,
            size: 30,
            color: AppColors.primaryContainer,
          ),
        ),
        const SizedBox(height: AppSpacing.md),
        Text(
          hasLoadedOrders
              ? 'Tidak Ada Pesanan di Filter Ini'
              : 'Belum Ada Pesanan',
          textAlign: TextAlign.center,
          style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700),
        ),
        const SizedBox(height: AppSpacing.sm),
        Text(
          hasLoadedOrders
              ? 'Coba pilih filter lain untuk melihat pesanan yang sudah dimuat.'
              : 'Pesanan servis Anda akan muncul di sini setelah booking dibuat.',
          textAlign: TextAlign.center,
          style: const TextStyle(
            fontSize: 12.5,
            color: AppColors.onSurfaceVariant,
          ),
        ),
        const SizedBox(height: AppSpacing.md),
        if (hasLoadedOrders)
          OutlinedButton.icon(
            onPressed: () => setState(() => _selectedFilter = 'all'),
            icon: const Icon(Icons.filter_alt_off_rounded),
            label: const Text('Tampilkan semua'),
          )
        else
          FilledButton.icon(
            onPressed: () => context.push('/pesan-servis'),
            icon: const Icon(Icons.add_rounded),
            label: const Text('Pesan servis'),
          ),
      ],
    ),
  );

  Widget _buildErrorState(Object error) => Center(
    child: Padding(
      padding: const EdgeInsets.all(AppSpacing.xl),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          const Icon(
            Icons.error_outline_rounded,
            color: AppColors.statusFailedText,
            size: 40,
          ),
          const SizedBox(height: AppSpacing.md),
          const Text(
            'Riwayat pesanan belum dapat dimuat.',
            textAlign: TextAlign.center,
            style: TextStyle(fontWeight: FontWeight.w700),
          ),
          const SizedBox(height: AppSpacing.xs),
          Text(
            error.toString(),
            textAlign: TextAlign.center,
            style: const TextStyle(
              fontSize: 12,
              color: AppColors.onSurfaceVariant,
            ),
          ),
          const SizedBox(height: AppSpacing.md),
          FilledButton.icon(
            onPressed: () => ref.invalidate(pesananListProvider),
            icon: const Icon(Icons.refresh_rounded),
            label: const Text('Coba lagi'),
          ),
        ],
      ),
    ),
  );
}
