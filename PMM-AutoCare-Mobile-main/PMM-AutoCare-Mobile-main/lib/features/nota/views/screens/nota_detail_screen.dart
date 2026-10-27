import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';

import '../../../../common/network/api_client.dart';
import '../../../../common/theme/app_theme.dart';
import '../../data/models/nota_model.dart';
import '../../data/providers/nota_provider.dart';

class NotaDetailScreen extends ConsumerWidget {
  static const routePath = '/nota';

  const NotaDetailScreen({required this.orderId, super.key});

  final String orderId;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final id = int.tryParse(orderId);
    if (id == null) return _notFound(context);

    final nota = ref.watch(notaProvider(id));
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
        title: nota.maybeWhen(
          data: (value) => Text(
            value.kodePesanan,
            style: const TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.w700,
              fontFamily: 'monospace',
              color: AppColors.primaryContainer,
            ),
          ),
          orElse: () => const Text('Nota Digital'),
        ),
      ),
      body: nota.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (error, _) => _errorState(context, ref, id, error),
        data: (value) => RefreshIndicator(
          color: AppColors.primaryContainer,
          onRefresh: () async {
            ref.invalidate(notaProvider(id));
            await ref.read(notaProvider(id).future);
          },
          child: ListView(
            padding: const EdgeInsets.fromLTRB(16, 16, 16, 32),
            children: [
              _summaryCard(value),
              const SizedBox(height: 14),
              _vehicleCard(value),
              const SizedBox(height: 14),
              _serviceCard(value),
              const SizedBox(height: 14),
              _sparepartCard(value),
              const SizedBox(height: 14),
              _totalCard(value),
            ],
          ),
        ),
      ),
    );
  }

  Widget _summaryCard(NotaModel nota) => _card(
    child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            const Expanded(
              child: Text(
                'Nota Digital',
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800),
              ),
            ),
            _statusPill(nota.status),
          ],
        ),
        const SizedBox(height: 12),
        _infoRow(Icons.receipt_long_rounded, 'Kode pesanan', nota.kodePesanan),
        const SizedBox(height: 8),
        _infoRow(
          Icons.calendar_today_rounded,
          'Tanggal servis',
          _formatTanggal(nota.tanggalSlot),
        ),
      ],
    ),
  );

  Widget _vehicleCard(NotaModel nota) => _card(
    child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Kendaraan',
          style: TextStyle(fontSize: 14.5, fontWeight: FontWeight.w700),
        ),
        const SizedBox(height: 10),
        if (nota.mobil == null)
          const _EmptyLineState(message: 'Data kendaraan tidak tersedia.')
        else ...[
          _infoRow(Icons.directions_car_rounded, 'Mobil', nota.mobil!.nama),
          const SizedBox(height: 8),
          _infoRow(Icons.pin_rounded, 'Plat nomor', nota.mobil!.platNomor),
        ],
      ],
    ),
  );

  Widget _serviceCard(NotaModel nota) {
    final currency = _currency;
    return _card(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Jasa',
            style: TextStyle(fontSize: 14.5, fontWeight: FontWeight.w700),
          ),
          const SizedBox(height: 10),
          if (nota.detailLayanan.isEmpty)
            const _EmptyLineState(message: 'Tidak ada jasa pada nota ini.')
          else
            for (final item in nota.detailLayanan)
              _lineItem(
                icon: Icons.build_circle_outlined,
                title: item.namaLayanan,
                quantity: '${item.jumlah} × ${currency.format(item.hargaJasa)}',
                amount: currency.format(item.subtotal),
              ),
          const Divider(height: 24, color: AppColors.borderSubtle),
          _subtotalRow('Subtotal jasa', currency.format(nota.subtotalJasa)),
        ],
      ),
    );
  }

  Widget _sparepartCard(NotaModel nota) {
    final currency = _currency;
    return _card(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Sparepart',
            style: TextStyle(fontSize: 14.5, fontWeight: FontWeight.w700),
          ),
          const SizedBox(height: 10),
          if (nota.detailSparepart.isEmpty)
            const _EmptyLineState(message: 'Tidak ada sparepart pada nota ini.')
          else
            for (final item in nota.detailSparepart)
              _lineItem(
                icon: Icons.settings_outlined,
                title: item.nama,
                quantity:
                    '${item.jumlah} ${item.satuan} × ${currency.format(item.harga)}',
                amount: currency.format(item.subtotal),
              ),
          const Divider(height: 24, color: AppColors.borderSubtle),
          _subtotalRow(
            'Subtotal sparepart',
            currency.format(nota.subtotalSparepart),
          ),
        ],
      ),
    );
  }

  Widget _totalCard(NotaModel nota) => _card(
    child: Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Expanded(
          child: Text(
            'Total Biaya',
            style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800),
          ),
        ),
        const SizedBox(width: 12),
        Text(
          _currency.format(nota.totalBiaya),
          textAlign: TextAlign.end,
          style: const TextStyle(
            fontSize: 19,
            fontWeight: FontWeight.w800,
            color: AppColors.primaryContainer,
          ),
        ),
      ],
    ),
  );

  Widget _lineItem({
    required IconData icon,
    required String title,
    required String quantity,
    required String amount,
  }) => Padding(
    padding: const EdgeInsets.symmetric(vertical: 5),
    child: Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(icon, size: 18, color: AppColors.primaryContainer),
        const SizedBox(width: 8),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(title, style: const TextStyle(fontWeight: FontWeight.w600)),
              const SizedBox(height: 2),
              Text(
                quantity,
                style: const TextStyle(
                  fontSize: 12,
                  color: AppColors.onSurfaceVariant,
                ),
              ),
            ],
          ),
        ),
        const SizedBox(width: 8),
        Text(
          amount,
          textAlign: TextAlign.end,
          style: const TextStyle(fontWeight: FontWeight.w600),
        ),
      ],
    ),
  );

  Widget _subtotalRow(String label, String amount) => Row(
    mainAxisAlignment: MainAxisAlignment.spaceBetween,
    children: [
      Text(
        label,
        style: const TextStyle(fontSize: 13, color: AppColors.onSurfaceVariant),
      ),
      Text(
        amount,
        style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w700),
      ),
    ],
  );

  Widget _card({required Widget child}) => Container(
    padding: const EdgeInsets.all(16),
    decoration: BoxDecoration(
      color: AppColors.surface,
      borderRadius: BorderRadius.circular(16),
      border: Border.all(color: AppColors.borderSubtle),
    ),
    child: child,
  );

  Widget _infoRow(IconData icon, String label, String value) => Row(
    children: [
      Icon(icon, size: 16, color: AppColors.outline),
      const SizedBox(width: 8),
      Text(
        '$label: ',
        style: const TextStyle(fontSize: 12, color: AppColors.outline),
      ),
      Expanded(
        child: Text(
          value,
          style: const TextStyle(fontSize: 12.5, fontWeight: FontWeight.w600),
        ),
      ),
    ],
  );

  Widget _statusPill(String status) {
    final (background, text) = switch (status) {
      'menunggu' => (AppColors.statusPendingBg, AppColors.statusPendingText),
      'diterima' || 'dikerjakan' => (
        AppColors.statusProgressBg,
        AppColors.statusProgressText,
      ),
      'selesai' => (AppColors.statusCompletedBg, AppColors.statusCompletedText),
      'ditolak' ||
      'dibatalkan' => (AppColors.statusFailedBg, AppColors.statusFailedText),
      _ => (AppColors.surfaceContainer, AppColors.onSurfaceVariant),
    };
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: background,
        borderRadius: BorderRadius.circular(999),
      ),
      child: Text(
        status.toUpperCase(),
        style: TextStyle(
          color: text,
          fontSize: 10.5,
          fontWeight: FontWeight.w700,
        ),
      ),
    );
  }

  Widget _notFound(BuildContext context) => Scaffold(
    appBar: AppBar(leading: BackButton(onPressed: () => context.pop())),
    body: const Center(child: Text('Nota tidak ditemukan.')),
  );

  Widget _errorState(
    BuildContext context,
    WidgetRef ref,
    int id,
    Object error,
  ) => Center(
    child: Padding(
      padding: const EdgeInsets.all(24),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          const Icon(
            Icons.error_outline_rounded,
            size: 36,
            color: AppColors.statusFailedText,
          ),
          const SizedBox(height: 12),
          Text(_errorMessage(error), textAlign: TextAlign.center),
          const SizedBox(height: 12),
          OutlinedButton(
            onPressed: () => ref.invalidate(notaProvider(id)),
            child: const Text('Coba lagi'),
          ),
        ],
      ),
    ),
  );

  String _errorMessage(Object error) {
    if (error is ApiException) {
      return switch (error.statusCode) {
        403 => 'Nota ini tidak dapat diakses.',
        404 => 'Nota tidak ditemukan.',
        _ => error.message,
      };
    }
    return error.toString();
  }

  String _formatTanggal(String? date) {
    if (date == null || date.isEmpty) return 'Belum tersedia';
    final parsed = DateTime.tryParse(date.replaceFirst(' ', 'T'));
    if (parsed == null) return date;
    return DateFormat('d MMMM y', 'id_ID').format(parsed);
  }

  static final _currency = NumberFormat.currency(
    locale: 'id_ID',
    symbol: 'Rp',
    decimalDigits: 0,
  );
}

class _EmptyLineState extends StatelessWidget {
  const _EmptyLineState({required this.message});

  final String message;

  @override
  Widget build(BuildContext context) => Padding(
    padding: const EdgeInsets.symmetric(vertical: 4),
    child: Row(
      children: [
        const Icon(
          Icons.remove_circle_outline_rounded,
          size: 18,
          color: AppColors.outline,
        ),
        const SizedBox(width: 8),
        Expanded(
          child: Text(
            message,
            style: const TextStyle(
              fontSize: 12.5,
              color: AppColors.onSurfaceVariant,
            ),
          ),
        ),
      ],
    ),
  );
}
