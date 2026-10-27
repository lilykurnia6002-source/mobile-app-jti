import 'dart:io';

import 'package:excel/excel.dart' hide Border;

import 'package:flutter/material.dart';
import 'package:flutter_chart/flutter_chart.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import 'package:path_provider/path_provider.dart';
import 'package:pdf/widgets.dart' as pw;
import 'package:share_plus/share_plus.dart';

import '../../../../common/network/api_client.dart';
import '../../../../common/theme/app_theme.dart';
import '../../../auth/data/providers/auth_provider.dart';
import '../../data/models/owner_report.dart';
import '../../data/providers/owner_report_provider.dart';

class OwnerReportsScreen extends ConsumerStatefulWidget {
  const OwnerReportsScreen({super.key});

  static const routePath = '/owner/reports';

  @override
  ConsumerState<OwnerReportsScreen> createState() => _OwnerReportsScreenState();
}

class _OwnerReportsScreenState extends ConsumerState<OwnerReportsScreen> {
  late ReportPeriod _period;
  bool _exporting = false;

  @override
  void initState() {
    super.initState();
    final today = _dateOnly(DateTime.now());
    _period = ReportPeriod(
      start: today.subtract(const Duration(days: 29)),
      end: today,
    );
  }

  Future<void> _pickPeriod() async {
    final picked = await showDateRangePicker(
      context: context,
      firstDate: DateTime(2020),
      lastDate: DateTime.now(),
      initialDateRange: DateTimeRange(start: _period.start, end: _period.end),
      helpText: 'Pilih periode laporan',
      saveText: 'Terapkan',
      builder: (context, child) => Theme(
        data: Theme.of(context).copyWith(
          colorScheme: Theme.of(context).colorScheme.copyWith(
            primary: AppColors.primary,
            secondary: AppColors.secondaryContainer,
          ),
        ),
        child: child!,
      ),
    );
    if (!mounted || picked == null) return;
    setState(() {
      _period = ReportPeriod(
        start: _dateOnly(picked.start),
        end: _dateOnly(picked.end),
      );
    });
  }

  Future<void> _export(OwnerReport report, {required bool excel}) async {
    setState(() => _exporting = true);
    try {
      final directory = await getTemporaryDirectory();
      final stamp = DateFormat('yyyyMMdd').format(report.period.start);
      final endStamp = DateFormat('yyyyMMdd').format(report.period.end);
      final extension = excel ? 'xlsx' : 'pdf';
      final file = File(
        '${directory.path}/laporan-pemilik-$stamp-$endStamp.$extension',
      );
      final bytes = excel ? _excelBytes(report) : await _pdfBytes(report);
      await file.writeAsBytes(bytes, flush: true);
      await Share.shareXFiles([
        XFile(
          file.path,
          mimeType: excel
              ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
              : 'application/pdf',
        ),
      ], subject: 'Laporan PMM AutoCare $stamp - $endStamp');
    } catch (error) {
      if (mounted) {
        ScaffoldMessenger.of(
          context,
        ).showSnackBar(SnackBar(content: Text('Ekspor gagal: $error')));
      }
    } finally {
      if (mounted) setState(() => _exporting = false);
    }
  }

  List<int> _excelBytes(OwnerReport report) {
    final excel = Excel.createExcel();
    final sheet = excel['Laporan'];
    sheet.appendRow([
      TextCellValue('Laporan PMM AutoCare'),
      TextCellValue(
        '${_formatDate(report.period.start)} - ${_formatDate(report.period.end)}',
      ),
    ]);
    sheet.appendRow([TextCellValue('Metrik'), TextCellValue('Nilai')]);
    sheet.appendRow([
      TextCellValue('Total transaksi'),
      IntCellValue(report.transactions.summary.number('total_orders').round()),
    ]);
    sheet.appendRow([
      TextCellValue('Nilai transaksi'),
      DoubleCellValue(
        report.transactions.summary.number('total_amount').toDouble(),
      ),
    ]);
    sheet.appendRow([
      TextCellValue('Total pekerjaan'),
      IntCellValue(report.productivity.summary.number('total_jobs').round()),
    ]);
    sheet.appendRow([
      TextCellValue('Transaksi non-tunai (%)'),
      DoubleCellValue(_ratio(report.nonCash.summary, 'non_cash_ratio')),
    ]);
    sheet.appendRow([]);
    sheet.appendRow([
      TextCellValue('Tanggal'),
      TextCellValue('Transaksi'),
      TextCellValue('Nilai'),
    ]);
    for (final point in report.transactions.daily) {
      sheet.appendRow([
        DateCellValue.fromDateTime(point.date),
        DoubleCellValue(point.value),
        DoubleCellValue(point.amount ?? 0),
      ]);
    }
    return excel.save() ?? <int>[];
  }

  Future<List<int>> _pdfBytes(OwnerReport report) async {
    final document = pw.Document();
    document.addPage(
      pw.Page(
        build: (context) => pw.Column(
          crossAxisAlignment: pw.CrossAxisAlignment.start,
          children: [
            pw.Text(
              'Laporan PMM AutoCare',
              style: pw.TextStyle(fontSize: 22, fontWeight: pw.FontWeight.bold),
            ),
            pw.SizedBox(height: 8),
            pw.Text(
              '${_formatDate(report.period.start)} - ${_formatDate(report.period.end)}',
            ),
            pw.SizedBox(height: 20),
            pw.TableHelper.fromTextArray(
              headers: const ['Metrik', 'Nilai'],
              data: [
                [
                  'Total transaksi',
                  '${report.transactions.summary.number('total_orders').round()}',
                ],
                [
                  'Nilai transaksi',
                  _currency(report.transactions.summary.number('total_amount')),
                ],
                [
                  'Total pekerjaan',
                  '${report.productivity.summary.number('total_jobs').round()}',
                ],
                [
                  'Non-tunai',
                  '${_ratio(report.nonCash.summary, 'non_cash_ratio').toStringAsFixed(1)}%',
                ],
              ],
            ),
            pw.SizedBox(height: 20),
            pw.Text(
              'Transaksi harian',
              style: pw.TextStyle(fontWeight: pw.FontWeight.bold),
            ),
            pw.TableHelper.fromTextArray(
              headers: const ['Tanggal', 'Transaksi', 'Nilai'],
              data: [
                for (final point in report.transactions.daily)
                  [
                    _formatDate(point.date),
                    '${point.value.round()}',
                    _currency(point.amount ?? 0),
                  ],
              ],
            ),
          ],
        ),
      ),
    );
    return document.save();
  }

  @override
  Widget build(BuildContext context) {
    final reportAsync = ref.watch(ownerReportProvider(_period));
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Laporan pemilik'),
        actions: [
          IconButton(
            tooltip: 'Keluar',
            onPressed: () async {
              await ref.read(authControllerProvider.notifier).logout();
              if (context.mounted) context.go('/login');
            },
            icon: const Icon(Icons.logout_rounded),
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () async => ref.invalidate(ownerReportProvider(_period)),
        color: AppColors.primaryContainer,
        child: ListView(
          padding: const EdgeInsets.fromLTRB(16, 12, 16, 36),
          children: [
            _PeriodHeader(period: _period, onTap: _pickPeriod),
            const SizedBox(height: AppSpacing.lg),
            reportAsync.when(
              loading: () => const _LoadingState(),
              error: (error, _) => _ErrorState(
                error: error,
                onRetry: () => ref.invalidate(ownerReportProvider(_period)),
              ),
              data: (report) => _ReportContent(
                report: report,
                exporting: _exporting,
                onExportExcel: () => _export(report, excel: true),
                onExportPdf: () => _export(report, excel: false),
              ),
            ),
          ],
        ),
      ),
    );
  }

  static DateTime _dateOnly(DateTime value) =>
      DateTime(value.year, value.month, value.day);
  static String _formatDate(DateTime value) =>
      DateFormat('d MMM yyyy', 'id_ID').format(value);
  static String _currency(num value) => NumberFormat.currency(
    locale: 'id_ID',
    symbol: 'Rp ',
    decimalDigits: 0,
  ).format(value);
  static double _ratio(ReportSummary summary, String key) {
    final value = summary.number(key).toDouble();
    return value <= 1 ? value * 100 : value;
  }
}

class _PeriodHeader extends StatelessWidget {
  const _PeriodHeader({required this.period, required this.onTap});
  final ReportPeriod period;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) => Semantics(
    button: true,
    label: 'Pilih periode laporan',
    value:
        '${_OwnerReportsScreenState._formatDate(period.start)} sampai '
        '${_OwnerReportsScreenState._formatDate(period.end)}',
    child: InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(AppRadii.card),
      child: Ink(
        padding: const EdgeInsets.symmetric(
          horizontal: AppSpacing.lg,
          vertical: AppSpacing.md,
        ),
        decoration: BoxDecoration(
          color: AppColors.primary,
          borderRadius: BorderRadius.circular(AppRadii.card),
        ),
        child: Row(
          children: [
            Container(
              width: 44,
              height: 44,
              decoration: BoxDecoration(
                color: AppColors.secondaryContainer,
                borderRadius: BorderRadius.circular(AppRadii.sm),
              ),
              child: const Icon(
                Icons.calendar_month_rounded,
                color: AppColors.primary,
                size: 24,
              ),
            ),
            const SizedBox(width: AppSpacing.md),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Periode laporan',
                    style: Theme.of(context).textTheme.bodySmall?.copyWith(
                      color: Colors.white.withValues(alpha: 0.72),
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    '${_OwnerReportsScreenState._formatDate(period.start)} - '
                    '${_OwnerReportsScreenState._formatDate(period.end)}',
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: Theme.of(context).textTheme.titleMedium?.copyWith(
                      color: Colors.white,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(width: AppSpacing.sm),
            const Icon(
              Icons.tune_rounded,
              color: AppColors.onPrimary,
              size: 22,
            ),
          ],
        ),
      ),
    ),
  );
}

class _ReportContent extends StatelessWidget {
  const _ReportContent({
    required this.report,
    required this.exporting,
    required this.onExportExcel,
    required this.onExportPdf,
  });

  final OwnerReport report;
  final bool exporting;
  final VoidCallback onExportExcel;
  final VoidCallback onExportPdf;

  @override
  Widget build(BuildContext context) {
    final transactionCount = report.transactions.summary.number('total_orders');
    final totalAmount = report.transactions.summary.number('total_amount');
    final jobs = report.productivity.summary.number('total_jobs');
    final nonCash = _OwnerReportsScreenState._ratio(
      report.nonCash.summary,
      'non_cash_ratio',
    );
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Expanded(
              child: _Metric(
                label: 'Total transaksi',
                value: '${transactionCount.round()}',
                icon: Icons.receipt_long_rounded,
              ),
            ),
            const SizedBox(width: AppSpacing.md),
            Expanded(
              child: _Metric(
                label: 'Nilai transaksi',
                value: _OwnerReportsScreenState._currency(totalAmount),
                icon: Icons.payments_rounded,
              ),
            ),
          ],
        ),
        const SizedBox(height: AppSpacing.md),
        Row(
          children: [
            Expanded(
              child: _Metric(
                label: 'Total pekerjaan',
                value: '${jobs.round()}',
                icon: Icons.engineering_rounded,
              ),
            ),
            const SizedBox(width: AppSpacing.md),
            Expanded(
              child: _Metric(
                label: 'Transaksi non-tunai',
                value: '${nonCash.toStringAsFixed(1)}%',
                icon: Icons.account_balance_wallet_rounded,
              ),
            ),
          ],
        ),
        const SizedBox(height: AppSpacing.xl),
        Text(
          'Tren transaksi',
          style: Theme.of(
            context,
          ).textTheme.titleLarge?.copyWith(fontWeight: FontWeight.w700),
        ),
        const SizedBox(height: AppSpacing.xs),
        Text(
          'Jumlah transaksi per hari',
          style: Theme.of(
            context,
          ).textTheme.bodyMedium?.copyWith(color: AppColors.textMuted),
        ),
        const SizedBox(height: AppSpacing.md),
        _TransactionChart(points: report.transactions.daily),
        const SizedBox(height: AppSpacing.xl),
        Row(
          children: [
            Expanded(
              child: Text(
                'Bagikan laporan',
                style: Theme.of(
                  context,
                ).textTheme.titleLarge?.copyWith(fontWeight: FontWeight.w700),
              ),
            ),
            if (exporting)
              const SizedBox(
                width: 20,
                height: 20,
                child: CircularProgressIndicator(strokeWidth: 2),
              ),
          ],
        ),
        const SizedBox(height: AppSpacing.md),
        Row(
          children: [
            Expanded(
              child: OutlinedButton.icon(
                onPressed: exporting ? null : onExportExcel,
                icon: const Icon(Icons.table_view_rounded, size: 20),
                label: const Text('Excel'),
              ),
            ),
            const SizedBox(width: AppSpacing.md),
            Expanded(
              child: FilledButton.icon(
                onPressed: exporting ? null : onExportPdf,
                icon: const Icon(Icons.picture_as_pdf_rounded, size: 20),
                label: const Text('PDF'),
              ),
            ),
          ],
        ),
      ],
    );
  }
}

class _Metric extends StatelessWidget {
  const _Metric({required this.label, required this.value, required this.icon});
  final String label;
  final String value;
  final IconData icon;

  @override
  Widget build(BuildContext context) => ConstrainedBox(
    constraints: const BoxConstraints(minHeight: 116),
    child: Container(
      padding: const EdgeInsets.all(AppSpacing.md),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(AppRadii.md),
        border: Border.all(color: AppColors.borderSubtle),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: AppColors.primaryContainer, size: 22),
          const SizedBox(height: AppSpacing.md),
          FittedBox(
            fit: BoxFit.scaleDown,
            alignment: Alignment.centerLeft,
            child: Text(
              value,
              maxLines: 1,
              style: const TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.w800,
                color: AppColors.textPrimary,
              ),
            ),
          ),
          const SizedBox(height: AppSpacing.xs),
          Text(
            label,
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
            style: const TextStyle(fontSize: 12, color: AppColors.textMuted),
          ),
        ],
      ),
    ),
  );
}

class _TransactionChart extends StatelessWidget {
  const _TransactionChart({required this.points});
  final List<ReportPoint> points;

  static List<ReportPoint> _displayPoints(List<ReportPoint> points) {
    if (points.length <= 7) return points;
    final lastIndex = points.length - 1;
    return [
      for (var index = 0; index < 7; index++)
        points[(index * lastIndex / 6).round()],
    ];
  }

  @override
  Widget build(BuildContext context) {
    if (points.isEmpty) {
      return Container(
        height: 180,
        alignment: Alignment.center,
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(AppRadii.card),
          border: Border.all(color: AppColors.borderSubtle),
        ),
        child: const Text(
          'Belum ada transaksi pada periode ini',
          style: TextStyle(color: AppColors.textMuted),
        ),
      );
    }

    final chartPoints = _displayPoints(points);
    return Semantics(
      label: 'Tren transaksi harian untuk ${points.length} hari',
      child: Container(
        height: 240,
        padding: const EdgeInsets.fromLTRB(
          AppSpacing.xs,
          AppSpacing.md,
          AppSpacing.xs,
          AppSpacing.sm,
        ),
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(AppRadii.card),
          border: Border.all(color: AppColors.borderSubtle),
        ),
        child: LayoutBuilder(
          builder: (context, constraints) => ChartLine(
            size: Size(constraints.maxWidth, constraints.maxHeight),
            chartBeans: [
              for (final point in chartPoints)
                ChartBean(
                  x: DateFormat('d/M').format(point.date),
                  y: point.value,
                ),
            ],
            lineColor: AppColors.secondaryFixedDim,
            xyColor: AppColors.outlineVariant,
            fontColor: AppColors.textMuted,
            fontSize: 11,
            lineWidth: 3,
            yNum: 4,
            isCurve: true,
            isShowHintY: true,
            isShowYValue: true,
            // flutter_chart only renders seven points and its touch lookup
            // follows the same limit; keep touch off after downsampling.
            isCanTouch: false,
          ),
        ),
      ),
    );
  }
}

class _LoadingState extends StatelessWidget {
  const _LoadingState();

  @override
  Widget build(BuildContext context) => const Padding(
    padding: EdgeInsets.symmetric(vertical: AppSpacing.xxl),
    child: Center(child: CircularProgressIndicator()),
  );
}

class _ErrorState extends StatelessWidget {
  const _ErrorState({required this.error, required this.onRetry});
  final Object error;
  final VoidCallback onRetry;

  @override
  Widget build(BuildContext context) => Container(
    padding: const EdgeInsets.all(AppSpacing.xl),
    decoration: BoxDecoration(
      color: AppColors.surface,
      borderRadius: BorderRadius.circular(AppRadii.card),
      border: Border.all(color: AppColors.borderSubtle),
    ),
    child: Column(
      children: [
        const Icon(
          Icons.cloud_off_rounded,
          size: 44,
          color: AppColors.statusFailedText,
        ),
        const SizedBox(height: AppSpacing.md),
        const Text(
          'Laporan belum dapat dimuat',
          style: TextStyle(
            fontWeight: FontWeight.w800,
            color: AppColors.textPrimary,
          ),
        ),
        const SizedBox(height: AppSpacing.xs),
        Text(
          error is ApiException
              ? (error as ApiException).message
              : 'Periksa koneksi lalu coba lagi.',
          textAlign: TextAlign.center,
          style: const TextStyle(color: AppColors.textMuted),
        ),
        const SizedBox(height: AppSpacing.lg),
        FilledButton.icon(
          onPressed: onRetry,
          icon: const Icon(Icons.refresh_rounded),
          label: const Text('Coba lagi'),
        ),
      ],
    ),
  );
}
