class ReportPeriod {
  const ReportPeriod({required this.start, required this.end});

  final DateTime start;
  final DateTime end;

  @override
  bool operator ==(Object other) =>
      other is ReportPeriod && other.start == start && other.end == end;

  @override
  int get hashCode => Object.hash(start, end);
}

class ReportSummary {
  const ReportSummary(this.values);

  final Map<String, dynamic> values;

  num number(String key) {
    final value = values[key];
    if (value is num) return value;
    return num.tryParse('$value') ?? 0;
  }

  String text(String key) => '${values[key] ?? ''}';
}

class ReportPoint {
  const ReportPoint({required this.date, required this.value, this.amount});

  final DateTime date;
  final double value;
  final double? amount;
}

class ReportSection {
  const ReportSection({required this.summary, required this.daily});

  final ReportSummary summary;
  final List<ReportPoint> daily;
}

class OwnerReport {
  const OwnerReport({
    required this.period,
    required this.transactions,
    required this.productivity,
    required this.nonCash,
  });

  final ReportPeriod period;
  final ReportSection transactions;
  final ReportSection productivity;
  final ReportSection nonCash;

  static OwnerReport fromResponses({
    required DateTime start,
    required DateTime end,
    required Map<String, dynamic> transactions,
    required Map<String, dynamic> productivity,
    required Map<String, dynamic> nonCash,
  }) {
    return OwnerReport(
      period: ReportPeriod(start: start, end: end),
      transactions: _section(
        transactions,
        valueKeys: const ['orders', 'jumlah'],
      ),
      productivity: _section(productivity, valueKeys: const ['jobs', 'jumlah']),
      nonCash: _section(
        nonCash,
        valueKeys: const ['non_cash_ratio', 'ratio'],
        ratio: true,
      ),
    );
  }

  static ReportSection _section(
    Map<String, dynamic> response, {
    required List<String> valueKeys,
    bool ratio = false,
  }) {
    final rawData = response['data'];
    final data = rawData is Map<String, dynamic> ? rawData : response;
    final summary = data['summary'] is Map<String, dynamic>
        ? ReportSummary(Map<String, dynamic>.from(data['summary'] as Map))
        : const ReportSummary({});
    final rawDaily = data['daily'];
    final daily = rawDaily is List
        ? rawDaily.whereType<Map>().map((item) {
            final json = Map<String, dynamic>.from(item);
            final date =
                DateTime.tryParse(
                  '${json['date'] ?? json['tanggal'] ?? json['day'] ?? ''}',
                ) ??
                DateTime.now();
            final value = _value(json, valueKeys, ratio: ratio);
            final amount = _num(json['amount'] ?? json['total_amount']);
            return ReportPoint(date: date, value: value, amount: amount);
          }).toList()
        : const <ReportPoint>[];
    return ReportSection(summary: summary, daily: daily);
  }

  static double _value(
    Map<String, dynamic> json,
    List<String> keys, {
    required bool ratio,
  }) {
    for (final key in keys) {
      final value = _num(json[key]);
      if (value != null) return ratio && value <= 1 ? value * 100 : value;
    }
    final nonCash = _num(json['non_cash_transactions']);
    final total = _num(json['transactions']);
    if (ratio && nonCash != null && total != null && total > 0) {
      return nonCash / total * 100;
    }
    return 0;
  }

  static double? _num(Object? value) {
    if (value is num) return value.toDouble();
    return double.tryParse('$value');
  }
}
