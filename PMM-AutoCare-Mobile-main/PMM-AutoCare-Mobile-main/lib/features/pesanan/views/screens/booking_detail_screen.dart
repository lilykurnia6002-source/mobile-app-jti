import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import 'package:timelines_plus/timelines_plus.dart';

import '../../../../common/network/api_client.dart';
import '../../../../common/theme/app_theme.dart';
import '../../../dashboard/data/providers/dashboard_provider.dart';
import '../../../nota/views/screens/nota_detail_screen.dart';
import '../../data/models/pesanan_model.dart';
import '../../data/providers/pesanan_provider.dart';

class BookingDetailScreen extends ConsumerWidget {
  static const routePath = '/pesanan-detail';

  const BookingDetailScreen({required this.orderId, super.key});

  final String orderId;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final id = int.tryParse(orderId);
    if (id == null) return _notFound(context);

    final detail = ref.watch(pesananDetailProvider(id));
    final history = ref.watch(riwayatPesananStatusProvider(id));

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: AppColors.surface,
        surfaceTintColor: Colors.transparent,
        elevation: 0,
        titleSpacing: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_rounded),
          color: AppColors.primaryContainer,
          tooltip: 'Kembali',
          onPressed: () => context.pop(),
        ),
        title: detail.maybeWhen(
          data: (order) => Text(
            order.kodePesanan,
            overflow: TextOverflow.ellipsis,
            style: Theme.of(context).textTheme.titleMedium?.copyWith(
              color: AppColors.primaryContainer,
              fontWeight: FontWeight.w700,
              letterSpacing: 0.2,
            ),
          ),
          orElse: () => const Text('Detail pesanan'),
        ),
        actions: [
          detail.maybeWhen(
            data: (order) => order.dapatDibatalkan
                ? IconButton(
                    icon: const Icon(Icons.cancel_outlined),
                    color: AppColors.statusFailedText,
                    tooltip: 'Batalkan pesanan',
                    onPressed: () => _confirmCancel(context, ref, order),
                  )
                : const SizedBox.shrink(),
            orElse: () => const SizedBox.shrink(),
          ),
          const SizedBox(width: AppSpacing.xs),
        ],
      ),
      body: detail.when(
        loading: () => const _LoadingState(),
        error: (error, _) => _ErrorState(
          error: error,
          onRetry: () {
            ref.invalidate(pesananDetailProvider(id));
            ref.invalidate(riwayatPesananStatusProvider(id));
          },
        ),
        data: (order) => RefreshIndicator(
          color: AppColors.primaryContainer,
          onRefresh: () async {
            await Future.wait([
              ref.refresh(pesananDetailProvider(id).future),
              ref.refresh(riwayatPesananStatusProvider(id).future),
            ]);
          },
          child: ListView(
            physics: const AlwaysScrollableScrollPhysics(),
            padding: const EdgeInsets.fromLTRB(
              AppSpacing.lg,
              AppSpacing.md,
              AppSpacing.lg,
              AppSpacing.xxl,
            ),
            children: [
              _StatusOverview(order: order),
              if (_isActiveServiceStatus(order.status)) ...[
                const SizedBox(height: AppSpacing.lg),
                const _WorkEstimatePendingCard(),
              ],
              const SizedBox(height: AppSpacing.lg),
              _ProgressCard(history: history),
              const SizedBox(height: AppSpacing.lg),
              _ScheduleCard(order: order),
              const SizedBox(height: AppSpacing.lg),
              _ServicesCard(order: order),
              const SizedBox(height: AppSpacing.xl),
              _DigitalNotaButton(orderId: order.id),
              if (order.dapatDibatalkan) ...[
                const SizedBox(height: AppSpacing.sm),
                _CancelButton(
                  onPressed: () => _confirmCancel(context, ref, order),
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }

  Future<void> _confirmCancel(
    BuildContext context,
    WidgetRef ref,
    PesananDetail order,
  ) async {
    final confirmed = await showModalBottomSheet<bool>(
      context: context,
      useSafeArea: true,
      backgroundColor: Colors.transparent,
      builder: (sheetContext) => _CancelSheet(order: order),
    );
    if (confirmed != true || !context.mounted) return;

    try {
      await ref.read(pesananRepositoryProvider).batalkanPesanan(order.id);
      ref.invalidate(pesananListProvider);
      ref.invalidate(pesananDetailProvider(order.id));
      ref.invalidate(riwayatPesananStatusProvider(order.id));
      ref.invalidate(dashboardProvider);
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Pesanan ${order.kodePesanan} dibatalkan.')),
        );
      }
    } on ApiException catch (error) {
      if (context.mounted) {
        ScaffoldMessenger.of(
          context,
        ).showSnackBar(SnackBar(content: Text(error.message)));
      }
    } catch (_) {
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Pesanan gagal dibatalkan. Coba lagi.')),
        );
      }
    }
  }

  Widget _notFound(BuildContext context) => Scaffold(
    appBar: AppBar(
      leading: IconButton(
        icon: const Icon(Icons.arrow_back_rounded),
        tooltip: 'Kembali',
        onPressed: () => context.pop(),
      ),
    ),
    body: const _EmptyState(
      icon: Icons.receipt_long_outlined,
      title: 'Pesanan tidak ditemukan',
      message: 'Tautan pesanan tidak valid atau sudah tidak tersedia.',
    ),
  );
}

class _StatusOverview extends StatelessWidget {
  const _StatusOverview({required this.order});

  final PesananDetail order;

  @override
  Widget build(BuildContext context) {
    final textTheme = Theme.of(context).textTheme;
    final vehicle = order.mobil;
    final slot = order.slot;

    return _SurfaceCard(
      padding: const EdgeInsets.all(AppSpacing.md),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'KODE BOOKING',
                      style: textTheme.labelSmall?.copyWith(
                        color: AppColors.textMuted,
                        fontWeight: FontWeight.w700,
                        letterSpacing: 0.7,
                      ),
                    ),
                    const SizedBox(height: AppSpacing.xs),
                    Text(
                      order.kodePesanan,
                      style: textTheme.titleMedium?.copyWith(
                        color: AppColors.primaryContainer,
                        fontWeight: FontWeight.w800,
                        letterSpacing: 0.2,
                      ),
                    ),
                  ],
                ),
              ),
              _StatusPill(status: order.status),
            ],
          ),
          const SizedBox(height: AppSpacing.md),
          Container(
            padding: const EdgeInsets.all(AppSpacing.sm),
            decoration: BoxDecoration(
              color: AppColors.surfaceContainerLow,
              borderRadius: BorderRadius.circular(AppRadii.md),
            ),
            child: Row(
              children: [
                Container(
                  width: 42,
                  height: 42,
                  decoration: BoxDecoration(
                    color: AppColors.surfaceContainerHighest,
                    borderRadius: BorderRadius.circular(AppRadii.md),
                  ),
                  child: const Icon(
                    Icons.directions_car_rounded,
                    color: AppColors.primaryContainer,
                  ),
                ),
                const SizedBox(width: AppSpacing.sm),
                Expanded(
                  child: vehicle == null
                      ? const Text(
                          'Data kendaraan belum tersedia',
                          style: TextStyle(color: AppColors.textMuted),
                        )
                      : Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              vehicle.nama,
                              overflow: TextOverflow.ellipsis,
                              style: textTheme.titleSmall?.copyWith(
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                            const SizedBox(height: AppSpacing.xs),
                            Text(
                              '${vehicle.platNomor} · ${vehicle.tahun}',
                              style: textTheme.labelMedium?.copyWith(
                                color: AppColors.onSurfaceVariant,
                              ),
                            ),
                            const SizedBox(height: AppSpacing.xs),
                            Row(
                              children: [
                                const Icon(
                                  Icons.schedule_rounded,
                                  size: 15,
                                  color: AppColors.textMuted,
                                ),
                                const SizedBox(width: AppSpacing.xs),
                                Expanded(
                                  child: Text(
                                    '${_formatTanggal(slot?.tanggal)} · ${_formatJam(slot)}',
                                    overflow: TextOverflow.ellipsis,
                                    style: textTheme.labelSmall?.copyWith(
                                      color: AppColors.textMuted,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ],
                        ),
                ),
                const Icon(
                  Icons.build_circle_outlined,
                  color: AppColors.primaryContainer,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _ProgressCard extends StatelessWidget {
  const _ProgressCard({required this.history});

  final AsyncValue<List<RiwayatPesananStatus>> history;

  @override
  Widget build(BuildContext context) {
    final textTheme = Theme.of(context).textTheme;
    return _SurfaceCard(
      padding: const EdgeInsets.fromLTRB(
        AppSpacing.md,
        AppSpacing.md,
        AppSpacing.md,
        AppSpacing.sm,
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(
                child: Text(
                  'Progres servis',
                  style: textTheme.titleMedium?.copyWith(
                    color: AppColors.primaryContainer,
                    fontWeight: FontWeight.w800,
                  ),
                ),
              ),
              history.maybeWhen(
                data: (entries) => _ProgressCount(count: entries.length),
                orElse: () => const SizedBox.shrink(),
              ),
            ],
          ),
          const SizedBox(height: AppSpacing.md),
          history.when(
            loading: () => const Padding(
              padding: EdgeInsets.symmetric(vertical: AppSpacing.lg),
              child: Center(child: CircularProgressIndicator()),
            ),
            error: (error, _) => _InlineError(message: _errorMessage(error)),
            data: (entries) => entries.isEmpty
                ? const _EmptyState(
                    icon: Icons.timeline_outlined,
                    title: 'Belum ada pembaruan',
                    message: 'Riwayat status servis akan muncul di sini.',
                    compact: true,
                  )
                : _RiwayatStatusTimeline(entries: entries),
          ),
        ],
      ),
    );
  }
}

class _WorkEstimatePendingCard extends StatelessWidget {
  const _WorkEstimatePendingCard();

  @override
  Widget build(BuildContext context) {
    final textTheme = Theme.of(context).textTheme;

    return Semantics(
      container: true,
      label:
          'Estimasi waktu pengerjaan belum tersedia. Menunggu konfirmasi bengkel.',
      child: _SurfaceCard(
        padding: const EdgeInsets.all(AppSpacing.md),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              width: 44,
              height: 44,
              decoration: BoxDecoration(
                color: AppColors.statusPendingBg,
                borderRadius: BorderRadius.circular(AppRadii.md),
              ),
              child: const Icon(
                Icons.hourglass_top_rounded,
                color: AppColors.statusPendingText,
              ),
            ),
            const SizedBox(width: AppSpacing.md),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Estimasi waktu pengerjaan',
                    style: textTheme.titleSmall?.copyWith(
                      color: AppColors.primaryContainer,
                      fontWeight: FontWeight.w800,
                    ),
                  ),
                  const SizedBox(height: AppSpacing.xs),
                  Text(
                    'Belum tersedia',
                    style: textTheme.labelMedium?.copyWith(
                      color: AppColors.statusPendingText,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                  const SizedBox(height: AppSpacing.sm),
                  Text(
                    'Informasi estimasi akan tampil setelah bengkel '
                    'mengonfirmasinya. Hitung mundur belum dapat ditampilkan.',
                    style: textTheme.bodySmall?.copyWith(
                      color: AppColors.onSurfaceVariant,
                      height: 1.35,
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _ScheduleCard extends StatelessWidget {
  const _ScheduleCard({required this.order});

  final PesananDetail order;

  @override
  Widget build(BuildContext context) {
    return _SurfaceCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const _SectionHeading(
            icon: Icons.event_available_rounded,
            title: 'Jadwal kedatangan',
          ),
          const SizedBox(height: AppSpacing.md),
          _InfoRow(
            icon: Icons.calendar_today_rounded,
            label: 'Tanggal',
            value: _formatTanggal(order.slot?.tanggal),
          ),
          const SizedBox(height: AppSpacing.md),
          _InfoRow(
            icon: Icons.schedule_rounded,
            label: 'Waktu',
            value: _formatJam(order.slot),
          ),
          if (_isActiveServiceStatus(order.status) && order.slot != null) ...[
            const Divider(
              height: AppSpacing.xxl,
              color: AppColors.borderSubtle,
            ),
            const _ReschedulePendingInfo(),
          ],
        ],
      ),
    );
  }
}

class _ReschedulePendingInfo extends StatelessWidget {
  const _ReschedulePendingInfo();

  @override
  Widget build(BuildContext context) {
    final textTheme = Theme.of(context).textTheme;

    return Semantics(
      container: true,
      label:
          'Ubah jadwal belum tersedia. Perubahan jadwal belum dapat dilakukan di aplikasi.',
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Icon(
            Icons.event_repeat_outlined,
            size: 20,
            color: AppColors.statusPendingText,
          ),
          const SizedBox(width: AppSpacing.sm),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Ubah jadwal belum tersedia',
                  style: textTheme.labelLarge?.copyWith(
                    color: AppColors.onSurface,
                    fontWeight: FontWeight.w700,
                  ),
                ),
                const SizedBox(height: AppSpacing.xs),
                Text(
                  'Perubahan jadwal belum dapat dilakukan di aplikasi saat ini.',
                  style: textTheme.bodySmall?.copyWith(
                    color: AppColors.onSurfaceVariant,
                    height: 1.35,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(width: AppSpacing.sm),
          Container(
            padding: const EdgeInsets.symmetric(
              horizontal: AppSpacing.sm,
              vertical: AppSpacing.xs,
            ),
            decoration: BoxDecoration(
              color: AppColors.statusPendingBg,
              borderRadius: BorderRadius.circular(AppRadii.pill),
            ),
            child: const Text(
              'Belum tersedia',
              style: TextStyle(
                color: AppColors.statusPendingText,
                fontSize: 11,
                fontWeight: FontWeight.w700,
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _ServicesCard extends StatelessWidget {
  const _ServicesCard({required this.order});

  final PesananDetail order;

  @override
  Widget build(BuildContext context) {
    final currency = NumberFormat.currency(
      locale: 'id_ID',
      symbol: 'Rp',
      decimalDigits: 0,
    );
    return _SurfaceCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const _SectionHeading(
            icon: Icons.build_circle_outlined,
            title: 'Rincian layanan',
          ),
          const SizedBox(height: AppSpacing.sm),
          if (order.layanan.isEmpty)
            const _EmptyLineState(
              message: 'Belum ada layanan pada pesanan ini.',
            )
          else
            for (final layanan in order.layanan)
              Padding(
                padding: const EdgeInsets.symmetric(vertical: AppSpacing.sm),
                child: Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Icon(
                      Icons.check_circle_outline_rounded,
                      size: 18,
                      color: AppColors.primaryContainer,
                    ),
                    const SizedBox(width: AppSpacing.sm),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            layanan.nama ?? 'Layanan servis',
                            style: const TextStyle(fontWeight: FontWeight.w600),
                          ),
                          const SizedBox(height: AppSpacing.xs),
                          Text(
                            '${layanan.jumlah} × ${currency.format(layanan.hargaJasa)}',
                            style: const TextStyle(
                              fontSize: 12,
                              color: AppColors.onSurfaceVariant,
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(width: AppSpacing.sm),
                    Text(
                      currency.format(layanan.subtotal),
                      textAlign: TextAlign.end,
                      style: const TextStyle(fontWeight: FontWeight.w700),
                    ),
                  ],
                ),
              ),
          if (order.keluhan.isNotEmpty) ...[
            const Divider(
              height: AppSpacing.xxl,
              color: AppColors.borderSubtle,
            ),
            Text(
              'Keluhan',
              style: Theme.of(context).textTheme.labelMedium?.copyWith(
                color: AppColors.textMuted,
                fontWeight: FontWeight.w700,
              ),
            ),
            const SizedBox(height: AppSpacing.xs),
            Text(order.keluhan),
          ],
          const Divider(height: AppSpacing.xxl, color: AppColors.borderSubtle),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Total biaya',
                style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800),
              ),
              Text(
                currency.format(order.totalBiaya),
                style: const TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.w800,
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

class _DigitalNotaButton extends StatelessWidget {
  const _DigitalNotaButton({required this.orderId});

  final int orderId;

  @override
  Widget build(BuildContext context) {
    return Semantics(
      button: true,
      label: 'Lihat nota digital',
      child: SizedBox(
        height: 52,
        child: FilledButton.icon(
          onPressed: () =>
              context.push('${NotaDetailScreen.routePath}/$orderId'),
          icon: const Icon(Icons.receipt_long_rounded),
          label: const Text('Lihat Nota Digital'),
          style: FilledButton.styleFrom(
            backgroundColor: AppColors.primaryContainer,
            foregroundColor: AppColors.onPrimary,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(AppRadii.md),
            ),
          ),
        ),
      ),
    );
  }
}

class _CancelButton extends StatelessWidget {
  const _CancelButton({required this.onPressed});

  final VoidCallback onPressed;

  @override
  Widget build(BuildContext context) {
    return Semantics(
      button: true,
      label: 'Batalkan booking servis',
      child: SizedBox(
        height: 52,
        child: OutlinedButton.icon(
          onPressed: onPressed,
          icon: const Icon(Icons.cancel_outlined),
          label: const Text('Batalkan booking servis'),
          style: OutlinedButton.styleFrom(
            foregroundColor: AppColors.statusFailedText,
            side: const BorderSide(color: AppColors.statusFailedBorder),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(AppRadii.md),
            ),
          ),
        ),
      ),
    );
  }
}

class _CancelSheet extends StatelessWidget {
  const _CancelSheet({required this.order});

  final PesananDetail order;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: AppColors.surface,
      borderRadius: const BorderRadius.vertical(
        top: Radius.circular(AppRadii.hero),
      ),
      child: SafeArea(
        top: false,
        child: Padding(
          padding: const EdgeInsets.fromLTRB(
            AppSpacing.xl,
            AppSpacing.sm,
            AppSpacing.xl,
            AppSpacing.lg,
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Center(
                child: Container(
                  width: 36,
                  height: 4,
                  decoration: BoxDecoration(
                    color: AppColors.outlineVariant,
                    borderRadius: BorderRadius.circular(AppRadii.pill),
                  ),
                ),
              ),
              const SizedBox(height: AppSpacing.lg),
              const Text(
                'Batalkan booking?',
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.w800),
              ),
              const SizedBox(height: AppSpacing.sm),
              Text(
                'Pesanan ${order.kodePesanan} akan dibatalkan dan statusnya diperbarui.',
                style: const TextStyle(color: AppColors.onSurfaceVariant),
              ),
              const SizedBox(height: AppSpacing.lg),
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton(
                      onPressed: () => Navigator.pop(context, false),
                      child: const Text('Tidak sekarang'),
                    ),
                  ),
                  const SizedBox(width: AppSpacing.sm),
                  Expanded(
                    child: FilledButton(
                      onPressed: () => Navigator.pop(context, true),
                      style: FilledButton.styleFrom(
                        backgroundColor: AppColors.statusFailedText,
                      ),
                      child: const Text('Ya, batalkan'),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _RiwayatStatusTimeline extends StatelessWidget {
  const _RiwayatStatusTimeline({required this.entries});

  final List<RiwayatPesananStatus> entries;

  @override
  Widget build(BuildContext context) {
    final activeIndex = entries.length - 1;
    return TimelineTheme(
      data: TimelineThemeData(
        direction: Axis.vertical,
        nodePosition: 0,
        indicatorPosition: 0.18,
        indicatorTheme: const IndicatorThemeData(size: 14),
        connectorTheme: const ConnectorThemeData(
          color: AppColors.borderSubtle,
          thickness: 2,
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          for (var index = 0; index < entries.length; index++)
            TimelineTile(
              node: TimelineNode(
                indicator: _TimelineIndicator(active: index == activeIndex),
                endConnector: index == activeIndex
                    ? null
                    : const SolidLineConnector(),
              ),
              contents: Padding(
                padding: const EdgeInsets.only(left: AppSpacing.md, bottom: 12),
                child: _RiwayatStatusContents(
                  entry: entries[index],
                  active: index == activeIndex,
                ),
              ),
            ),
        ],
      ),
    );
  }
}

class _TimelineIndicator extends StatelessWidget {
  const _TimelineIndicator({required this.active});

  final bool active;

  @override
  Widget build(BuildContext context) {
    return DotIndicator(
      size: active ? 14 : 10,
      color: active ? AppColors.primaryContainer : AppColors.secondaryContainer,
    );
  }
}

class _RiwayatStatusContents extends StatelessWidget {
  const _RiwayatStatusContents({required this.entry, required this.active});

  final RiwayatPesananStatus entry;
  final bool active;

  @override
  Widget build(BuildContext context) {
    final textTheme = Theme.of(context).textTheme;
    final content = Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Expanded(
              child: Text(
                _humanizeStatus(entry.status),
                style: textTheme.titleSmall?.copyWith(
                  color: active
                      ? AppColors.primaryContainer
                      : AppColors.onSurface,
                  fontWeight: FontWeight.w700,
                ),
              ),
            ),
            if (entry.waktu != null)
              Text(
                _formatWaktu(entry.waktu!),
                textAlign: TextAlign.end,
                style: textTheme.labelSmall?.copyWith(
                  color: AppColors.textMuted,
                ),
              ),
          ],
        ),
        if (entry.keterangan != null && entry.keterangan!.isNotEmpty) ...[
          const SizedBox(height: AppSpacing.xs),
          Text(
            entry.keterangan!,
            style: textTheme.bodySmall?.copyWith(
              color: AppColors.onSurfaceVariant,
              height: 1.35,
            ),
          ),
        ],
        if (entry.mekanik != null && entry.mekanik!.isNotEmpty) ...[
          const SizedBox(height: AppSpacing.xs),
          Row(
            children: [
              const Icon(
                Icons.person_outline_rounded,
                size: 14,
                color: AppColors.textMuted,
              ),
              const SizedBox(width: AppSpacing.xs),
              Text(
                'Mekanik: ${entry.mekanik}',
                style: textTheme.labelSmall?.copyWith(
                  color: AppColors.textMuted,
                ),
              ),
            ],
          ),
        ],
      ],
    );

    return Semantics(
      container: true,
      label:
          '${_humanizeStatus(entry.status)}${entry.waktu == null ? '' : ', ${_formatWaktu(entry.waktu!)}'}',
      child: active
          ? Container(
              padding: const EdgeInsets.all(AppSpacing.sm),
              decoration: BoxDecoration(
                color: AppColors.surfaceContainerLow,
                borderRadius: BorderRadius.circular(AppRadii.md),
              ),
              child: content,
            )
          : Padding(
              padding: const EdgeInsets.symmetric(vertical: AppSpacing.sm),
              child: content,
            ),
    );
  }
}

class _SurfaceCard extends StatelessWidget {
  const _SurfaceCard({
    required this.child,
    this.padding = const EdgeInsets.all(AppSpacing.md),
  });

  final Widget child;
  final EdgeInsetsGeometry padding;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: padding,
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(AppRadii.card),
        border: Border.all(color: AppColors.borderSubtle),
      ),
      child: child,
    );
  }
}

class _SectionHeading extends StatelessWidget {
  const _SectionHeading({required this.icon, required this.title});

  final IconData icon;
  final String title;

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Icon(icon, size: 20, color: AppColors.primaryContainer),
        const SizedBox(width: AppSpacing.sm),
        Text(
          title,
          style: Theme.of(context).textTheme.titleMedium?.copyWith(
            color: AppColors.primaryContainer,
            fontWeight: FontWeight.w800,
          ),
        ),
      ],
    );
  }
}

class _InfoRow extends StatelessWidget {
  const _InfoRow({
    required this.icon,
    required this.label,
    required this.value,
  });

  final IconData icon;
  final String label;
  final String value;

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Icon(icon, size: 18, color: AppColors.textMuted),
        const SizedBox(width: AppSpacing.sm),
        Text('$label ', style: const TextStyle(color: AppColors.textMuted)),
        Expanded(
          child: Text(
            value,
            textAlign: TextAlign.end,
            style: const TextStyle(fontWeight: FontWeight.w700),
          ),
        ),
      ],
    );
  }
}

class _StatusPill extends StatelessWidget {
  const _StatusPill({required this.status});

  final String status;

  @override
  Widget build(BuildContext context) {
    final colors =
        statusColors[status] ??
        (AppColors.surfaceContainer, AppColors.onSurfaceVariant);
    return Semantics(
      label: 'Status ${_humanizeStatus(status)}',
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
        decoration: BoxDecoration(
          color: colors.$1,
          borderRadius: BorderRadius.circular(AppRadii.pill),
          border: Border.all(
            color: statusBorderColors[status] ?? AppColors.outlineVariant,
          ),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 7,
              height: 7,
              decoration: BoxDecoration(
                color: colors.$2,
                shape: BoxShape.circle,
              ),
            ),
            const SizedBox(width: AppSpacing.xs),
            Text(
              _humanizeStatus(status),
              style: TextStyle(
                color: colors.$2,
                fontSize: 11,
                fontWeight: FontWeight.w700,
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _ProgressCount extends StatelessWidget {
  const _ProgressCount({required this.count});

  final int count;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(
        horizontal: AppSpacing.sm,
        vertical: AppSpacing.xs,
      ),
      decoration: BoxDecoration(
        color: AppColors.secondaryFixed,
        borderRadius: BorderRadius.circular(AppRadii.pill),
      ),
      child: Text(
        '$count pembaruan',
        style: const TextStyle(
          color: AppColors.onSecondaryContainer,
          fontSize: 11,
          fontWeight: FontWeight.w700,
        ),
      ),
    );
  }
}

class _LoadingState extends StatelessWidget {
  const _LoadingState();

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Semantics(
        label: 'Memuat detail pesanan',
        child: const CircularProgressIndicator(),
      ),
    );
  }
}

class _ErrorState extends StatelessWidget {
  const _ErrorState({required this.error, required this.onRetry});

  final Object error;
  final VoidCallback onRetry;

  @override
  Widget build(BuildContext context) {
    return _EmptyState(
      icon: Icons.cloud_off_rounded,
      title: 'Detail pesanan belum tersedia',
      message: _errorMessage(error),
      action: OutlinedButton.icon(
        onPressed: onRetry,
        icon: const Icon(Icons.refresh_rounded),
        label: const Text('Coba lagi'),
      ),
    );
  }
}

class _InlineError extends StatelessWidget {
  const _InlineError({required this.message});

  final String message;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(AppSpacing.sm),
      decoration: BoxDecoration(
        color: AppColors.statusFailedBg,
        borderRadius: BorderRadius.circular(AppRadii.md),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Icon(
            Icons.error_outline_rounded,
            color: AppColors.statusFailedText,
          ),
          const SizedBox(width: AppSpacing.sm),
          Expanded(
            child: Text(
              'Riwayat status gagal dimuat. $message',
              style: const TextStyle(color: AppColors.statusFailedText),
            ),
          ),
        ],
      ),
    );
  }
}

class _EmptyState extends StatelessWidget {
  const _EmptyState({
    required this.icon,
    required this.title,
    required this.message,
    this.action,
    this.compact = false,
  });

  final IconData icon;
  final String title;
  final String message;
  final Widget? action;
  final bool compact;

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: EdgeInsets.all(compact ? AppSpacing.sm : AppSpacing.xxl),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(icon, size: compact ? 28 : 40, color: AppColors.textMuted),
            const SizedBox(height: AppSpacing.sm),
            Text(
              title,
              textAlign: TextAlign.center,
              style: const TextStyle(fontWeight: FontWeight.w700),
            ),
            const SizedBox(height: AppSpacing.xs),
            Text(
              message,
              textAlign: TextAlign.center,
              style: const TextStyle(color: AppColors.textMuted),
            ),
            if (action != null) ...[
              const SizedBox(height: AppSpacing.md),
              action!,
            ],
          ],
        ),
      ),
    );
  }
}

class _EmptyLineState extends StatelessWidget {
  const _EmptyLineState({required this.message});

  final String message;

  @override
  Widget build(BuildContext context) {
    return Text(message, style: const TextStyle(color: AppColors.textMuted));
  }
}

bool _isActiveServiceStatus(String status) =>
    status == 'diterima' || status == 'dikerjakan';

String _humanizeStatus(String status) {
  if (status.isEmpty) return 'Status belum tersedia';
  return status
      .replaceAll('_', ' ')
      .split(' ')
      .where((word) => word.isNotEmpty)
      .map((word) => '${word[0].toUpperCase()}${word.substring(1)}')
      .join(' ');
}

String _formatTanggal(String? value) {
  if (value == null || value.isEmpty) return 'Belum tersedia';
  final date = DateTime.tryParse(value);
  return date == null ? value : DateFormat('d MMMM y', 'id_ID').format(date);
}

String _formatJam(BookingSlot? slot) {
  if (slot == null) return 'Belum tersedia';
  return '${slot.jamMulai}–${slot.jamSelesai} WIB';
}

String _formatWaktu(String value) {
  final date = DateTime.tryParse(value);
  return date == null
      ? value
      : DateFormat('d MMM y, HH:mm', 'id_ID').format(date);
}

String _errorMessage(Object error) {
  if (error is ApiException) return error.message;
  return 'Periksa koneksi lalu coba lagi.';
}
