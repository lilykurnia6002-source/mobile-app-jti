import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

import '../../../../common/theme/app_theme.dart';
import '../../data/models/dashboard_model.dart';
import 'status_badge.dart';

class RiwayatCard extends StatelessWidget {
  const RiwayatCard({required this.item, this.onTap, super.key});
  final RiwayatItem item;
  final VoidCallback? onTap;

  @override
  Widget build(BuildContext context) {
    final textTheme = Theme.of(context).textTheme;
    final currency = NumberFormat.currency(
      locale: 'id_ID',
      symbol: 'Rp',
      decimalDigits: 0,
    );

    String tanggalFormatted = 'Waktu tidak tersedia';
    final waktuPesan = item.waktuPesan?.trim();
    if (waktuPesan != null && waktuPesan.isNotEmpty) {
      final dt = DateTime.tryParse(waktuPesan);
      if (dt != null) {
        tanggalFormatted = DateFormat('d MMM y', 'id_ID').format(dt);
      } else {
        tanggalFormatted = waktuPesan;
      }
    }

    return Material(
      color: Colors.transparent,
      borderRadius: BorderRadius.circular(AppRadii.card),
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: onTap,
        child: Container(
          width: double.infinity,
          padding: const EdgeInsets.all(AppSpacing.md),
          decoration: BoxDecoration(
            color: AppColors.surface,
            borderRadius: BorderRadius.circular(AppRadii.card),
            border: Border.all(color: AppColors.borderSubtle, width: 1),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header: Kode & Status Badge
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    item.kodePesanan,
                    style: textTheme.labelSmall?.copyWith(
                      fontFamily: 'monospace',
                    ),
                  ),
                  StatusBadge(status: item.status),
                ],
              ),
              const SizedBox(height: AppSpacing.sm),

              // Detail & Total Biaya
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          '${item.mobil.merk} ${item.mobil.tipe}',
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          style: textTheme.titleSmall?.copyWith(
                            color: AppColors.textPrimary,
                          ),
                        ),
                        const SizedBox(height: AppSpacing.xs),
                        Row(
                          children: [
                            Text(
                              item.mobil.platNomor,
                              style: textTheme.bodySmall?.copyWith(
                                fontWeight: FontWeight.w500,
                              ),
                            ),
                            Padding(
                              padding: const EdgeInsets.symmetric(
                                horizontal: AppSpacing.xs,
                              ),
                              child: Text(
                                '•',
                                style: textTheme.bodySmall?.copyWith(
                                  color: AppColors.outline,
                                ),
                              ),
                            ),
                            Flexible(
                              child: Text(
                                tanggalFormatted,
                                overflow: TextOverflow.ellipsis,
                                style: textTheme.bodySmall,
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(width: AppSpacing.md),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      Text(
                        'Total Biaya',
                        style: textTheme.labelSmall?.copyWith(fontSize: 10.5),
                      ),
                      const SizedBox(height: AppSpacing.xs / 2),
                      Text(
                        currency.format(item.totalBiaya),
                        style: textTheme.titleSmall?.copyWith(
                          color: AppColors.textPrimary,
                          fontWeight: FontWeight.w700,
                          fontFeatures: const [FontFeature.tabularFigures()],
                        ),
                      ),
                    ],
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
