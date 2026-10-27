import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';

import '../../../../common/theme/app_theme.dart';
import '../../data/models/pesanan_model.dart';
import '../../data/providers/pesanan_provider.dart';
import 'booking_confirmation_screen.dart';

class BookingFormScreen extends ConsumerStatefulWidget {
  static const routePath = '/pesan-servis';

  const BookingFormScreen({super.key});

  @override
  ConsumerState<BookingFormScreen> createState() => _BookingFormScreenState();
}

class _BookingFormScreenState extends ConsumerState<BookingFormScreen>
    with WidgetsBindingObserver {
  final _formKey = GlobalKey<FormState>();
  int? _selectedVehicleId;
  int? _selectedSlotId;
  String? _selectedDate;
  final _selectedServiceIds = <int>{};
  late final TextEditingController _complaintController;
  Timer? _slotRefreshTimer;
  Timer? _slotBoundaryTimer;
  DateTime? _scheduledSlotBoundary;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
    _complaintController = TextEditingController();
    _slotRefreshTimer = Timer.periodic(
      const Duration(seconds: 30),
      (_) => _refreshCurrentSlots(),
    );
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    _slotRefreshTimer?.cancel();
    _slotBoundaryTimer?.cancel();
    _complaintController.dispose();
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.resumed) {
      _refreshCurrentSlots();
    }
  }

  void _refreshCurrentSlots() {
    if (!mounted) return;
    final date = _selectedDate;
    if (date != null) {
      ref.invalidate(bookingSlotsProvider(date));
    }
  }

  @override
  Widget build(BuildContext context) {
    final inputs = ref.watch(bookingInputsProvider);
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
          'Pesan Servis',
          style: TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.w700,
            color: AppColors.primaryContainer,
          ),
        ),
      ),
      body: inputs.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (error, _) => _errorState(error),
        data: _buildForm,
      ),
    );
  }

  Widget _buildForm(BookingInputs inputs) {
    final nextDate = inputs.tanggalTersedia.isEmpty
        ? null
        : inputs.tanggalTersedia.contains(_selectedDate)
        ? _selectedDate
        : inputs.tanggalTersedia.first;
    if (_selectedDate != nextDate ||
        (_selectedSlotId != null && nextDate == null)) {
      WidgetsBinding.instance.addPostFrameCallback((_) {
        if (!mounted) return;
        setState(() {
          _selectedDate = nextDate;
          _selectedSlotId = null;
        });
      });
    }
    final date = nextDate;
    final vehicle = _selectedVehicle(inputs.kendaraan);
    final slots = date == null ? null : ref.watch(bookingSlotsProvider(date));
    return Form(
      key: _formKey,
      child: ListView(
        padding: const EdgeInsets.fromLTRB(16, 16, 16, 32),
        children: [
          _vehicleCard(inputs.kendaraan, vehicle),
          const SizedBox(height: 14),
          _scheduleCard(inputs.tanggalTersedia, date, slots),
          const SizedBox(height: 14),
          _serviceCard(inputs.layanan),
          const SizedBox(height: 14),
          _complaintCard(),
          const SizedBox(height: 24),
          FilledButton.icon(
            onPressed:
                date == null ||
                    vehicle == null ||
                    _selectedServiceIds.isEmpty ||
                    slots?.valueOrNull == null
                ? null
                : () {
                    final selectedDate = date;
                    final selectedVehicle = vehicle;
                    final selectedSlot = _selectedSlot(
                      _currentlyValidSlots(slots!.valueOrNull!),
                    );
                    final selectedServices = inputs.layanan
                        .where(
                          (service) => _selectedServiceIds.contains(service.id),
                        )
                        .toList(growable: false);
                    if (selectedSlot == null || selectedServices.isEmpty) {
                      return;
                    }
                    _openConfirmation(
                      vehicle: selectedVehicle,
                      date: selectedDate,
                      slot: selectedSlot,
                      services: selectedServices,
                    );
                  },
            style: FilledButton.styleFrom(
              backgroundColor: AppColors.primaryContainer,
              padding: const EdgeInsets.symmetric(vertical: 15),
            ),
            icon: const Icon(Icons.arrow_forward_rounded),
            label: const Text('Lanjutkan ke Konfirmasi'),
          ),
        ],
      ),
    );
  }

  Kendaraan? _selectedVehicle(List<Kendaraan> vehicles) {
    if (vehicles.isEmpty) return null;
    final selected = _selectedVehicleId;
    if (selected == null) return vehicles.first;
    for (final vehicle in vehicles) {
      if (vehicle.id == selected) return vehicle;
    }
    return null;
  }

  BookingSlot? _selectedSlot(List<BookingSlot> slots) {
    final selectedId = _selectedSlotId;
    if (selectedId == null) return null;
    for (final slot in slots) {
      if (slot.id == selectedId) return slot;
    }
    return null;
  }

  Future<void> _openConfirmation({
    required Kendaraan vehicle,
    required String date,
    required BookingSlot slot,
    required List<Layanan> services,
  }) async {
    if (!(_formKey.currentState?.validate() ?? false)) {
      ScaffoldMessenger.of(
        context,
      ).showSnackBar(const SnackBar(content: Text('Keluhan wajib diisi.')));
      return;
    }
    final complaint = _complaintController.text.trim();
    if (complaint.isEmpty) return;

    final failure = await context.push<String>(
      BookingConfirmationScreen.routePath,
      extra: BookingConfirmationData(
        vehicle: vehicle,
        date: date,
        slot: slot,
        services: services,
        complaint: complaint,
      ),
    );
    if (!mounted || failure == null) return;
    _refreshBookingChoices(date);
    ScaffoldMessenger.of(
      context,
    ).showSnackBar(SnackBar(content: Text(failure)));
  }

  Future<void> _showVehiclePicker(
    List<Kendaraan> vehicles,
    Kendaraan? selected,
  ) async {
    final chosen = await showModalBottomSheet<Kendaraan>(
      context: context,
      backgroundColor: AppColors.surface,
      showDragHandle: true,
      builder: (context) => SafeArea(
        child: ListView(
          shrinkWrap: true,
          padding: const EdgeInsets.fromLTRB(16, 0, 16, 16),
          children: [
            Text(
              'Pilih kendaraan',
              style: Theme.of(context).textTheme.titleLarge,
            ),
            const SizedBox(height: 8),
            for (final vehicle in vehicles)
              Semantics(
                button: true,
                selected: vehicle.id == selected?.id,
                label: '${vehicle.nama}, ${vehicle.platNomor}',
                child: Material(
                  color: Colors.transparent,
                  child: InkWell(
                    borderRadius: BorderRadius.circular(AppRadii.md),
                    onTap: () => Navigator.pop(context, vehicle),
                    child: Padding(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 12,
                        vertical: 12,
                      ),
                      child: Row(
                        children: [
                          const Icon(
                            Icons.directions_car_filled_rounded,
                            color: AppColors.primaryContainer,
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  '${vehicle.nama} — ${vehicle.platNomor}',
                                  style: const TextStyle(
                                    fontWeight: FontWeight.w700,
                                  ),
                                ),
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
                          if (vehicle.id == selected?.id)
                            const Icon(
                              Icons.check_rounded,
                              color: AppColors.primaryContainer,
                            ),
                        ],
                      ),
                    ),
                  ),
                ),
              ),
          ],
        ),
      ),
    );
    if (!mounted || chosen == null) return;
    setState(() => _selectedVehicleId = chosen.id);
  }

  Widget _selectionField({
    required String label,
    required String hint,
    required String? value,
    required IconData icon,
    required VoidCallback onTap,
  }) => Semantics(
    button: true,
    label: label,
    value: value ?? hint,
    child: Material(
      color: Colors.transparent,
      borderRadius: BorderRadius.circular(AppRadii.md),
      child: Ink(
        decoration: BoxDecoration(
          color: AppColors.surfaceContainerLow,
          borderRadius: BorderRadius.circular(AppRadii.md),
          border: Border.all(color: AppColors.outlineVariant),
        ),
        child: InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(AppRadii.md),
          child: ConstrainedBox(
            constraints: const BoxConstraints(minHeight: 56),
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
              child: Row(
                children: [
                  Icon(icon, color: AppColors.primaryContainer),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Text(
                      value ?? hint,
                      style: TextStyle(
                        color: value == null
                            ? AppColors.onSurfaceVariant
                            : AppColors.onSurface,
                        fontWeight: value == null
                            ? FontWeight.w400
                            : FontWeight.w600,
                      ),
                    ),
                  ),
                  const Icon(
                    Icons.keyboard_arrow_down_rounded,
                    color: AppColors.onSurfaceVariant,
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    ),
  );

  Widget _vehicleCard(List<Kendaraan> vehicles, Kendaraan? selected) => _card(
    child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'KENDARAAN SERVIS',
          style: TextStyle(
            fontSize: 11,
            fontWeight: FontWeight.w700,
            color: AppColors.onSurfaceVariant,
          ),
        ),
        const SizedBox(height: 10),
        if (vehicles.isEmpty) ...[
          const Text(
            'Akun Anda belum memiliki kendaraan yang dapat dipilih untuk booking.',
            style: TextStyle(color: AppColors.onSurfaceVariant),
          ),
          const SizedBox(height: 10),
          OutlinedButton.icon(
            onPressed: () async {
              await context.push('/mobil');
              ref.invalidate(bookingInputsProvider);
            },
            icon: const Icon(Icons.add_rounded),
            label: const Text('Tambah kendaraan'),
          ),
        ] else ...[
          _selectionField(
            label: 'Pilih kendaraan',
            value: selected == null
                ? null
                : '${selected.nama} — ${selected.platNomor}',
            hint: 'Pilih kendaraan',
            icon: Icons.directions_car_filled_rounded,
            onTap: () => _showVehiclePicker(vehicles, selected),
          ),
          const SizedBox(height: 10),
          if (selected == null)
            const Text(
              'Kendaraan yang sebelumnya dipilih tidak tersedia. Pilih kendaraan dari daftar.',
              style: TextStyle(color: AppColors.onSurfaceVariant),
            )
          else
            Text(
              '${selected.tahun} • ${selected.tipe}',
              style: const TextStyle(
                fontSize: 12,
                color: AppColors.onSurfaceVariant,
              ),
            ),
        ],
      ],
    ),
  );

  Widget _dateChoice(String date, DateTime value, bool selected) {
    final day = DateFormat('EEE', 'id_ID').format(value);
    final shortDate = DateFormat('d MMM', 'id_ID').format(value);
    return Semantics(
      button: true,
      selected: selected,
      label: 'Tanggal $day $shortDate',
      child: Material(
        color: Colors.transparent,
        borderRadius: BorderRadius.circular(AppRadii.md),
        child: Ink(
          decoration: BoxDecoration(
            color: selected
                ? AppColors.primaryContainer
                : AppColors.surfaceContainerLow,
            borderRadius: BorderRadius.circular(AppRadii.md),
            border: Border.all(
              color: selected
                  ? AppColors.primaryContainer
                  : AppColors.outlineVariant,
            ),
          ),
          child: InkWell(
            borderRadius: BorderRadius.circular(AppRadii.md),
            onTap: () => setState(() {
              _selectedDate = date;
              _selectedSlotId = null;
            }),
            child: SizedBox(
              width: 88,
              height: 64,
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Text(
                    day,
                    style: TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                      color: selected
                          ? AppColors.onPrimary
                          : AppColors.onSurfaceVariant,
                    ),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    shortDate,
                    style: TextStyle(
                      fontWeight: FontWeight.w700,
                      color: selected
                          ? AppColors.onPrimary
                          : AppColors.onSurface,
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

  Widget _slotChoice(BookingSlot slot, bool selected) {
    final label = slot.sisaKuota == null
        ? '${slot.jamMulai}–${slot.jamSelesai} WIB'
        : '${slot.jamMulai}–${slot.jamSelesai} WIB, sisa ${slot.sisaKuota} slot';
    return Semantics(
      button: true,
      selected: selected,
      label: label,
      child: Material(
        color: Colors.transparent,
        borderRadius: BorderRadius.circular(AppRadii.md),
        child: Ink(
          decoration: BoxDecoration(
            color: selected
                ? AppColors.primaryContainer
                : AppColors.surfaceContainerLow,
            borderRadius: BorderRadius.circular(AppRadii.md),
            border: Border.all(
              color: selected
                  ? AppColors.primaryContainer
                  : AppColors.outlineVariant,
            ),
          ),
          child: InkWell(
            borderRadius: BorderRadius.circular(AppRadii.md),
            onTap: () => setState(() => _selectedSlotId = slot.id),
            child: ConstrainedBox(
              constraints: const BoxConstraints(minWidth: 136, minHeight: 56),
              child: Padding(
                padding: const EdgeInsets.symmetric(
                  horizontal: 12,
                  vertical: 8,
                ),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Text(
                      '${slot.jamMulai}–${slot.jamSelesai} WIB',
                      style: TextStyle(
                        fontWeight: FontWeight.w700,
                        color: selected
                            ? AppColors.onPrimary
                            : AppColors.onSurface,
                      ),
                    ),
                    if (slot.sisaKuota != null)
                      Text(
                        'Sisa ${slot.sisaKuota} slot',
                        style: TextStyle(
                          fontSize: 11,
                          color: selected
                              ? AppColors.onPrimary
                              : AppColors.onSurfaceVariant,
                        ),
                      ),
                  ],
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _scheduleCard(
    List<String> dates,
    String? selectedDate,
    AsyncValue<List<BookingSlot>>? slots,
  ) => _card(
    child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Pilih Tanggal & Jam',
          style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700),
        ),
        const SizedBox(height: 12),
        if (dates.isEmpty)
          const Text(
            'Belum ada jadwal yang tersedia.',
            style: TextStyle(color: AppColors.onSurfaceVariant),
          )
        else ...[
          SizedBox(
            height: 64,
            child: ListView.separated(
              scrollDirection: Axis.horizontal,
              itemCount: dates.length,
              separatorBuilder: (_, _) => const SizedBox(width: 8),
              itemBuilder: (context, index) {
                final date = dates[index];
                return _dateChoice(
                  date,
                  DateTime.parse(date),
                  date == selectedDate,
                );
              },
            ),
          ),
          const SizedBox(height: 14),
          const Text(
            'Pilih Jam Kedatangan',
            style: TextStyle(fontSize: 12, color: AppColors.onSurfaceVariant),
          ),
          const SizedBox(height: 8),
          slots!.when(
            loading: () => const Padding(
              padding: EdgeInsets.all(12),
              child: Center(child: CircularProgressIndicator()),
            ),
            error: (error, _) => Text(
              error.toString(),
              style: const TextStyle(color: AppColors.statusFailedText),
            ),
            data: (items) {
              _scheduleSlotBoundaryRefresh(items);
              final validItems = _currentlyValidSlots(items);
              _clearSelectedSlotIfMissing(validItems);
              if (validItems.isEmpty) {
                return const Text(
                  'Tidak ada slot tersedia pada tanggal ini.',
                  style: TextStyle(color: AppColors.onSurfaceVariant),
                );
              }
              return Wrap(
                spacing: 8,
                runSpacing: 8,
                children: [
                  for (final slot in validItems)
                    _slotChoice(slot, _selectedSlotId == slot.id),
                ],
              );
            },
          ),
        ],
      ],
    ),
  );
  List<BookingSlot> _currentlyValidSlots(List<BookingSlot> slots) {
    final now = _jakartaNow();
    return [
      for (final slot in slots)
        if (_slotStartInJakarta(slot)?.isAfter(now) ?? true) slot,
    ];
  }

  void _scheduleSlotBoundaryRefresh(List<BookingSlot> slots) {
    final now = _jakartaNow();
    DateTime? nextBoundary;
    for (final slot in slots) {
      final start = _slotStartInJakarta(slot);
      if (start == null || !start.isAfter(now)) continue;
      if (nextBoundary == null || start.isBefore(nextBoundary)) {
        nextBoundary = start;
      }
    }
    if (nextBoundary == _scheduledSlotBoundary) return;

    _slotBoundaryTimer?.cancel();
    _scheduledSlotBoundary = nextBoundary;
    if (nextBoundary == null) return;

    _slotBoundaryTimer = Timer(
      nextBoundary.difference(now) + const Duration(milliseconds: 1),
      () {
        if (!mounted) return;
        _scheduledSlotBoundary = null;
        setState(() {});
      },
    );
  }

  void _clearSelectedSlotIfMissing(List<BookingSlot> slots) {
    final selectedSlotId = _selectedSlotId;
    if (selectedSlotId == null ||
        slots.any((slot) => slot.id == selectedSlotId)) {
      return;
    }
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (!mounted || _selectedSlotId != selectedSlotId) return;
      setState(() => _selectedSlotId = null);
    });
  }

  Widget _serviceOption(Layanan service, NumberFormat currency) {
    final selected = _selectedServiceIds.contains(service.id);
    final details = [
      if (service.kategori != null) service.kategori!,
      '${service.estimasiMenit} menit',
      currency.format(service.hargaJasa),
    ].join(' • ');
    return Semantics(
      button: true,
      checked: selected,
      label: '${service.nama}, $details',
      child: Material(
        color: selected
            ? AppColors.surfaceContainer
            : AppColors.surfaceContainerLow,
        borderRadius: BorderRadius.circular(AppRadii.md),
        child: InkWell(
          borderRadius: BorderRadius.circular(AppRadii.md),
          onTap: () => setState(() {
            if (selected) {
              _selectedServiceIds.remove(service.id);
            } else {
              _selectedServiceIds.add(service.id);
            }
          }),
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 6),
            child: Row(
              children: [
                ExcludeSemantics(
                  child: Checkbox(
                    value: selected,
                    onChanged: (_) => setState(() {
                      if (selected) {
                        _selectedServiceIds.remove(service.id);
                      } else {
                        _selectedServiceIds.add(service.id);
                      }
                    }),
                    activeColor: AppColors.primaryContainer,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(5),
                    ),
                  ),
                ),
                const SizedBox(width: 4),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        service.nama,
                        style: const TextStyle(fontWeight: FontWeight.w700),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        details,
                        style: const TextStyle(
                          fontSize: 12,
                          color: AppColors.onSurfaceVariant,
                        ),
                      ),
                    ],
                  ),
                ),
                if (selected)
                  const Icon(
                    Icons.check_circle_rounded,
                    color: AppColors.primaryContainer,
                    size: 20,
                  ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _serviceCard(List<Layanan> services) {
    final currency = NumberFormat.currency(
      locale: 'id_ID',
      symbol: 'Rp',
      decimalDigits: 0,
    );
    return _card(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Pilih Jenis Layanan',
                style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700),
              ),
              Text(
                '${_selectedServiceIds.length} dipilih',
                style: const TextStyle(
                  fontSize: 12,
                  color: AppColors.onSurfaceVariant,
                ),
              ),
            ],
          ),
          const SizedBox(height: 10),
          if (services.isEmpty)
            const Text(
              'Tidak ada layanan aktif.',
              style: TextStyle(color: AppColors.onSurfaceVariant),
            )
          else
            for (final service in services) _serviceOption(service, currency),
        ],
      ),
    );
  }

  Widget _complaintCard() => _card(
    child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Keluhan',
          style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700),
        ),
        const SizedBox(height: 4),
        const Text(
          'Wajib diisi untuk diteruskan ke bengkel.',
          style: TextStyle(fontSize: 12, color: AppColors.onSurfaceVariant),
        ),
        const SizedBox(height: 10),
        TextFormField(
          controller: _complaintController,
          maxLength: 2000,
          maxLines: 3,
          validator: (value) => value == null || value.trim().isEmpty
              ? 'Keluhan wajib diisi.'
              : null,
          autovalidateMode: AutovalidateMode.onUserInteraction,
          decoration: InputDecoration(
            filled: true,
            fillColor: AppColors.surfaceContainerLow,
            labelText: 'Jelaskan keluhan',
            hintText: 'Contoh: Rem depan berdecit saat pengereman.',
            alignLabelWithHint: true,
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(AppRadii.md),
              borderSide: const BorderSide(color: AppColors.outlineVariant),
            ),
            enabledBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(AppRadii.md),
              borderSide: const BorderSide(color: AppColors.outlineVariant),
            ),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(AppRadii.md),
              borderSide: const BorderSide(
                color: AppColors.focusRing,
                width: 2,
              ),
            ),
            errorBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(AppRadii.md),
              borderSide: const BorderSide(color: AppColors.statusFailedText),
            ),
            focusedErrorBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(AppRadii.md),
              borderSide: const BorderSide(
                color: AppColors.statusFailedText,
                width: 2,
              ),
            ),
          ),
        ),
      ],
    ),
  );

  Widget _card({required Widget child}) => Container(
    padding: const EdgeInsets.all(14),
    decoration: BoxDecoration(
      color: AppColors.surface,
      borderRadius: BorderRadius.circular(AppRadii.card),
      border: Border.all(color: AppColors.borderSubtle),
    ),
    child: child,
  );

  Widget _errorState(Object error) => Center(
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
          Text(error.toString(), textAlign: TextAlign.center),
          const SizedBox(height: 12),
          OutlinedButton(
            onPressed: () => ref.invalidate(bookingInputsProvider),
            child: const Text('Coba lagi'),
          ),
        ],
      ),
    ),
  );

  void _refreshBookingChoices(String selectedDate) {
    ref.invalidate(bookingSlotsProvider(selectedDate));
    ref.invalidate(bookingInputsProvider);
    if (mounted) {
      setState(() {
        _selectedDate = null;
        _selectedSlotId = null;
        _selectedVehicleId = null;
        _selectedServiceIds.clear();
      });
    }
  }
}

DateTime _jakartaNow() => DateTime.now().toUtc().add(const Duration(hours: 7));

DateTime? _slotStartInJakarta(BookingSlot slot) {
  final dateParts = slot.tanggal.split('-');
  if (dateParts.length < 3) return null;
  final date = [
    int.tryParse(dateParts[0]),
    int.tryParse(dateParts[1]),
    int.tryParse(dateParts[2].substring(0, 2)),
  ];
  if (date.any((part) => part == null)) return null;

  final timeMatch = RegExp(
    r'^(\d{1,2}):(\d{2})(?::(\d{2}))?',
  ).firstMatch(slot.jamMulai.trim());
  if (timeMatch == null) return null;
  final hour = int.parse(timeMatch.group(1)!);
  final minute = int.parse(timeMatch.group(2)!);
  final second = int.tryParse(timeMatch.group(3) ?? '0') ?? 0;
  if (hour > 23 || minute > 59 || second > 59) return null;

  return DateTime.utc(date[0]!, date[1]!, date[2]!, hour, minute, second);
}
