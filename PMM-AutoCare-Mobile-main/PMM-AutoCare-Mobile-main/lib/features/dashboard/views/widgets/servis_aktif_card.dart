import 'package:flutter/material.dart';

import '../../../../common/theme/app_theme.dart';
import '../../data/models/dashboard_model.dart';
import 'riwayat_status_timeline.dart';
import 'status_badge.dart';

class ServisAktifCard extends StatelessWidget {
  const ServisAktifCard({
    required this.servisAktif,
    this.onDetailTap,
    super.key,
  });

  final ServisAktif servisAktif;
  final VoidCallback? onDetailTap;

  @override
  Widget build(BuildContext context) {
    final textTheme = Theme.of(context).textTheme;

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(AppSpacing.lg),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(AppRadii.card),
        border: Border.all(color: AppColors.borderSubtle, width: 1),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header Baris: Live Indicator & Status Pill
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  Container(
                    width: 8,
                    height: 8,
                    decoration: const BoxDecoration(
                      color: AppColors.secondaryContainer,
                      shape: BoxShape.circle,
                    ),
                  ),
                  const SizedBox(width: AppSpacing.sm),
                  Text(
                    'SERVIS BERJALAN',
                    style: textTheme.labelSmall?.copyWith(
                      color: AppColors.onSurfaceVariant,
                      letterSpacing: 0.6,
                    ),
                  ),
                ],
              ),
              StatusBadge(status: servisAktif.status),
            ],
          ),
          const SizedBox(height: AppSpacing.md),

          // Detail Mobil & Plat Nomor
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Expanded(
                child: Text(
                  '${servisAktif.mobil.merk} ${servisAktif.mobil.tipe}',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: textTheme.titleMedium?.copyWith(
                    fontSize: 17,
                    color: AppColors.textPrimary,
                  ),
                ),
              ),
              const SizedBox(width: AppSpacing.sm),
              Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: AppSpacing.sm,
                  vertical: AppSpacing.xs,
                ),
                decoration: BoxDecoration(
                  color: AppColors.surfaceContainer,
                  borderRadius: BorderRadius.circular(AppRadii.sm),
                ),
                child: Text(
                  servisAktif.mobil.platNomor,
                  style: textTheme.labelSmall?.copyWith(
                    color: AppColors.primaryContainer,
                    fontFamily: 'monospace',
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: AppSpacing.xs / 2),
          Text(
            servisAktif.kodePesanan,
            style: textTheme.labelSmall?.copyWith(fontFamily: 'monospace'),
          ),
          const SizedBox(height: AppSpacing.lg),

          // Riwayat status nyata (dari tabel tb_riwayat_status)
          Text(
            'Riwayat status',
            style: textTheme.labelMedium?.copyWith(
              color: AppColors.onSurfaceVariant,
            ),
          ),
          const SizedBox(height: AppSpacing.sm),
          RiwayatStatusTimeline(entries: servisAktif.riwayatStatus),

          const Divider(height: 1, color: AppColors.borderSubtle),
          const SizedBox(height: AppSpacing.sm),

          Align(
            alignment: Alignment.centerRight,
            child: Semantics(
              button: true,
              label: 'Lihat progres ${servisAktif.kodePesanan}',
              child: InkWell(
                onTap: onDetailTap,
                borderRadius: BorderRadius.circular(AppRadii.sm),
                child: Padding(
                  padding: const EdgeInsets.symmetric(
                    vertical: AppSpacing.xs,
                    horizontal: AppSpacing.sm,
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Text(
                        'Lihat progres',
                        style: textTheme.labelMedium?.copyWith(
                          color: AppColors.primaryContainer,
                        ),
                      ),
                      const SizedBox(width: AppSpacing.xs),
                      const Icon(
                        Icons.arrow_forward_rounded,
                        size: 14,
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
    );
  }
}
