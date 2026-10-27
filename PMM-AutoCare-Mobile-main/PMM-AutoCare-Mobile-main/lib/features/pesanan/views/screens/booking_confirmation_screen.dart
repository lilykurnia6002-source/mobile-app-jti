import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';

import '../../../../common/network/api_client.dart';
import '../../../../common/theme/app_theme.dart';
import '../../../dashboard/data/providers/dashboard_provider.dart';
import '../../data/models/pesanan_model.dart';
import '../../data/providers/pesanan_provider.dart';

class BookingConfirmationData {
  BookingConfirmationData({
    required this.vehicle,
    required this.date,
    required this.slot,
    required List<Layanan> services,
    required this.complaint,
  }) : services = List.unmodifiable(services);

  final Kendaraan vehicle;
  final String date;
  final BookingSlot slot;
  final List<Layanan> services;
  final String complaint;
}

class BookingConfirmationScreen extends ConsumerStatefulWidget {
  static const routePath = '/pesan-servis/konfirmasi';

  const BookingConfirmationScreen({required this.data, super.key});

  final BookingConfirmationData data;

  @override
  ConsumerState<BookingConfirmationScreen> createState() =>
      _BookingConfirmationScreenState();
}

class _BookingConfirmationScreenState
    extends ConsumerState<BookingConfirmationScreen> {
  bool _submitting = false;

  String? _selectionError() {
    final data = widget.data;
    if (data.vehicle.id <= 0) return 'Kendaraan tidak valid.';
    if (data.date.trim().isEmpty) return 'Tanggal servis belum dipilih.';
    if (data.slot.id <= 0 || data.slot.tanggal != data.date) {
      return 'Jadwal servis tidak valid. Pilih jadwal kembali.';
    }
    if (data.services.isEmpty ||
        data.services.any((service) => service.id <= 0)) {
      return 'Pilih minimal satu layanan.';
    }
    if (data.complaint.trim().isEmpty) return 'Keluhan wajib diisi.';
    return null;
  }

  @override
  Widget build(BuildContext context) {
    final data = widget.data;
    final error = _selectionError();
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: AppColors.surface,
        elevation: 0,
        leading: IconButton(
          tooltip: 'Kembali ke formulir',
          icon: const Icon(
            Icons.arrow_back_rounded,
            color: AppColors.primaryContainer,
          ),
          onPressed: _submitting ? null : () => context.pop(),
        ),
        title: const Text(
          'Konfirmasi Pesanan',
          style: TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.w700,
            color: AppColors.primaryContainer,
          ),
        ),
      ),
      body: ListView(
        padding: const EdgeInsets.fromLTRB(16, 16, 16, 32),
        children: [
          Text(
            'Periksa kembali detail booking sebelum dikirim ke bengkel.',
            style: Theme.of(
              context,
            ).textTheme.bodyMedium?.copyWith(color: AppColors.onSurfaceVariant),
          ),
          const SizedBox(height: 16),
          _vehicleSection(data.vehicle),
          const SizedBox(height: 12),
          _scheduleSection(data),
          const SizedBox(height: 12),
          _servicesSection(data.services),
          const SizedBox(height: 12),
          _complaintSection(data.complaint),
          if (error != null) ...[
            const SizedBox(height: 12),
            _errorNotice(error),
          ],
          const SizedBox(height: 24),
          FilledButton.icon(
            onPressed: _submitting || error != null ? null : _confirmBooking,
            style: FilledButton.styleFrom(
              backgroundColor: AppColors.primaryContainer,
              padding: const EdgeInsets.symmetric(vertical: 15),
            ),
            icon: _submitting
                ? const SizedBox.square(
                    dimension: 18,
                    child: CircularProgressIndicator(
                      color: Colors.white,
                      strokeWidth: 2,
                    ),
                  )
                : const Icon(Icons.check_circle_rounded),
            label: Text(_submitting ? 'Mengirim...' : 'Konfirmasi Pesanan'),
          ),
        ],
      ),
    );
  }

  Widget _vehicleSection(Kendaraan vehicle) => _section(
    title: 'Kendaraan',
    icon: Icons.directions_car_filled_rounded,
    child: Semantics(
      label: 'Kendaraan ${vehicle.nama}, ${vehicle.platNomor}',
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            '${vehicle.nama} — ${vehicle.platNomor}',
            style: const TextStyle(fontWeight: FontWeight.w700),
          ),
          const SizedBox(height: 4),
          Text(
            '${vehicle.tahun} • ${vehicle.tipe}',
            style: const TextStyle(
              fontSize: 12,
              color: AppColors.onSurfaceVariant,
            ),
          ),
        ],
      ),
    ),
  );

  Widget _scheduleSection(BookingConfirmationData data) {
    final parsedDate = DateTime.tryParse(data.date);
    final dateLabel = parsedDate == null
        ? data.date
        : DateFormat('EEEE, d MMMM y', 'id_ID').format(parsedDate);
    return _section(
      title: 'Jadwal servis',
      icon: Icons.calendar_month_rounded,
      child: Semantics(
        label:
            'Jadwal $dateLabel, ${data.slot.jamMulai} sampai ${data.slot.jamSelesai} WIB',
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              dateLabel,
              style: const TextStyle(fontWeight: FontWeight.w700),
            ),
            const SizedBox(height: 4),
            Text(
              '${data.slot.jamMulai}–${data.slot.jamSelesai} WIB',
              style: const TextStyle(color: AppColors.onSurfaceVariant),
            ),
          ],
        ),
      ),
    );
  }

  Widget _servicesSection(List<Layanan> services) {
    final currency = NumberFormat.currency(
      locale: 'id_ID',
      symbol: 'Rp',
      decimalDigits: 0,
    );
    return _section(
      title: 'Layanan dipilih',
      icon: Icons.build_circle_rounded,
      child: Column(
        children: [
          for (var index = 0; index < services.length; index++) ...[
            if (index > 0)
              const Divider(height: 20, color: AppColors.borderSubtle),
            Semantics(
              label:
                  '${services[index].nama}, ${currency.format(services[index].hargaJasa)}, estimasi ${services[index].estimasiMenit} menit',
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Expanded(
                    child: Text(
                      services[index].nama,
                      style: const TextStyle(fontWeight: FontWeight.w700),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      Text(
                        currency.format(services[index].hargaJasa),
                        style: const TextStyle(fontWeight: FontWeight.w700),
                      ),
                      const SizedBox(height: 3),
                      Text(
                        'Estimasi ${services[index].estimasiMenit} menit',
                        style: const TextStyle(
                          fontSize: 12,
                          color: AppColors.onSurfaceVariant,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ],
      ),
    );
  }

  Widget _complaintSection(String complaint) => _section(
    title: 'Keluhan',
    icon: Icons.description_outlined,
    child: Semantics(label: 'Keluhan: $complaint', child: Text(complaint)),
  );

  Widget _section({
    required String title,
    required IconData icon,
    required Widget child,
  }) => Container(
    padding: const EdgeInsets.all(14),
    decoration: BoxDecoration(
      color: AppColors.surface,
      borderRadius: BorderRadius.circular(AppRadii.card),
      border: Border.all(color: AppColors.borderSubtle),
    ),
    child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Icon(icon, size: 20, color: AppColors.primaryContainer),
            const SizedBox(width: 8),
            Text(title, style: const TextStyle(fontWeight: FontWeight.w700)),
          ],
        ),
        const SizedBox(height: 12),
        child,
      ],
    ),
  );

  Widget _errorNotice(String message) => Semantics(
    liveRegion: true,
    label: message,
    child: Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: AppColors.statusFailedBg,
        borderRadius: BorderRadius.circular(AppRadii.md),
        border: Border.all(color: AppColors.statusFailedBorder),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Icon(
            Icons.error_outline_rounded,
            size: 20,
            color: AppColors.statusFailedText,
          ),
          const SizedBox(width: 8),
          Expanded(
            child: Text(
              message,
              style: const TextStyle(color: AppColors.statusFailedText),
            ),
          ),
        ],
      ),
    ),
  );

  Future<void> _confirmBooking() async {
    if (_submitting) return;
    final error = _selectionError();
    if (error != null) {
      ScaffoldMessenger.of(
        context,
      ).showSnackBar(SnackBar(content: Text(error)));
      return;
    }

    setState(() => _submitting = true);
    final data = widget.data;
    try {
      final created = await ref
          .read(pesananRepositoryProvider)
          .buatPesanan(
            idMobil: data.vehicle.id,
            idSlot: data.slot.id,
            keluhan: data.complaint.trim(),
            layananIds: data.services.map((service) => service.id),
          );
      ref.invalidate(pesananListProvider);
      ref.invalidate(bookingInputsProvider);
      ref.invalidate(bookingSlotsProvider);
      ref.invalidate(dashboardProvider);
      if (!mounted) return;
      await showDialog<void>(
        context: context,
        barrierDismissible: false,
        builder: (dialogContext) => AlertDialog(
          title: const Text('Booking Berhasil'),
          content: Text(
            'Pesanan ${created.kodePesanan} tercatat dengan status ${created.status}.',
          ),
          actions: [
            FilledButton(
              onPressed: () => Navigator.pop(dialogContext),
              child: const Text('Lihat Pesanan Saya'),
            ),
          ],
        ),
      );
      if (mounted) context.go('/pesanan');
    } on ApiException catch (error) {
      if (error.statusCode == 422) {
        ref.invalidate(bookingSlotsProvider(data.date));
        ref.invalidate(bookingInputsProvider);
        if (mounted) {
          context.pop<String>(
            '${error.message} Pilihan jadwal dan booking diperbarui; pilih kembali sebelum mengirim.',
          );
        }
        return;
      }
      if (mounted) {
        ScaffoldMessenger.of(
          context,
        ).showSnackBar(SnackBar(content: Text(error.message)));
      }
    } finally {
      if (mounted) setState(() => _submitting = false);
    }
  }
}
