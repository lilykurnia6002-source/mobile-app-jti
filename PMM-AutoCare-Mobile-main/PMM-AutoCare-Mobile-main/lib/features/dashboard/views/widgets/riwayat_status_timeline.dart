import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:timelines_plus/timelines_plus.dart';

import '../../../../common/theme/app_theme.dart';
import '../../data/models/dashboard_model.dart';

/// Jejak status pesanan nyata dari `riwayat_status`, sebanyak apapun
/// entrinya. Bukan tracker 6 langkah tetap karena backend tidak punya
/// kolom untuk itu.
class RiwayatStatusTimeline extends StatelessWidget {
  const RiwayatStatusTimeline({required this.entries, super.key});
  final List<RiwayatStatusEntry> entries;

  @override
  Widget build(BuildContext context) {
    return TimelineTheme(
      data: TimelineThemeData(
        direction: Axis.vertical,
        nodePosition: 0,
        indicatorPosition: 0.5,
        indicatorTheme: IndicatorThemeData(size: 10),
        connectorTheme: ConnectorThemeData(
          color: AppColors.borderSubtle,
          thickness: 2,
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          for (var i = 0; i < entries.length; i++)
            TimelineTile(
              node: TimelineNode(
                indicator: const DotIndicator(
                  size: 10,
                  color: AppColors.secondaryContainer,
                ),
                endConnector: i == entries.length - 1
                    ? null
                    : const SolidLineConnector(),
              ),
              contents: Padding(
                padding: const EdgeInsets.only(left: 10, bottom: 12),
                child: _TimelineContents(entry: entries[i]),
              ),
            ),
        ],
      ),
    );
  }
}

class _TimelineContents extends StatelessWidget {
  const _TimelineContents({required this.entry});
  final RiwayatStatusEntry entry;

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          entry.keterangan ?? entry.status,
          style: const TextStyle(
            fontSize: 13,
            fontWeight: FontWeight.w600,
            color: AppColors.textPrimary,
          ),
        ),
        if (entry.waktu != null)
          Text(
            _formatWaktu(entry.waktu!),
            style: const TextStyle(fontSize: 11, color: AppColors.outline),
          ),
      ],
    );
  }
}

String _formatWaktu(String raw) {
  final dt = DateTime.tryParse(raw);
  if (dt == null) return raw;
  return DateFormat('d MMM y, HH:mm', 'id_ID').format(dt);
}
