import 'package:flutter/material.dart';

import '../../../../common/theme/app_theme.dart';

class QuickActionCards extends StatelessWidget {
  const QuickActionCards({
    super.key,
    required this.jumlahMobil,
    this.onPesanServisTap,
    this.onMobilSayaTap,
  });

  final int jumlahMobil;
  final VoidCallback? onPesanServisTap;
  final VoidCallback? onMobilSayaTap;

  @override
  Widget build(BuildContext context) {
    final textTheme = Theme.of(context).textTheme;

    return Row(
      children: [
        // Kartu Pesan Servis (Navy Container)
        Expanded(
          child: Material(
            color: Colors.transparent,
            borderRadius: BorderRadius.circular(AppRadii.card),
            clipBehavior: Clip.antiAlias,
            child: InkWell(
              onTap: onPesanServisTap,
              child: Container(
                height: 130,
                padding: const EdgeInsets.all(AppSpacing.md),
                decoration: BoxDecoration(
                  color: AppColors.primaryContainer,
                  borderRadius: BorderRadius.circular(AppRadii.card),
                ),
                child: Stack(
                  children: [
                    // Lingkaran dekoratif di kanan bawah
                    Positioned(
                      right: -15,
                      bottom: -15,
                      child: IgnorePointer(
                        child: Container(
                          width: 70,
                          height: 70,
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            color: AppColors.onPrimary.withValues(alpha: 0.08),
                          ),
                        ),
                      ),
                    ),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Container(
                              width: 38,
                              height: 38,
                              decoration: BoxDecoration(
                                color: AppColors.onPrimary.withValues(
                                  alpha: 0.15,
                                ),
                                borderRadius: BorderRadius.circular(
                                  AppRadii.sm,
                                ),
                              ),
                              child: const Icon(
                                Icons.calendar_month_rounded,
                                size: 20,
                                color: AppColors.onPrimary,
                              ),
                            ),
                            Container(
                              padding: const EdgeInsets.symmetric(
                                horizontal: AppSpacing.sm,
                                vertical: AppSpacing.xs,
                              ),
                              decoration: BoxDecoration(
                                color: AppColors.secondaryContainer,
                                borderRadius: BorderRadius.circular(
                                  AppRadii.pill,
                                ),
                              ),
                              child: Text(
                                'Booking',
                                style: textTheme.labelSmall?.copyWith(
                                  color: AppColors.onSecondaryContainer,
                                  fontWeight: FontWeight.w700,
                                ),
                              ),
                            ),
                          ],
                        ),
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Pesan servis',
                              style: textTheme.titleMedium?.copyWith(
                                color: AppColors.onPrimary,
                              ),
                            ),
                            const SizedBox(height: AppSpacing.xs / 2),
                            Text(
                              'Atur jadwal bengkel',
                              style: textTheme.bodySmall?.copyWith(
                                color: AppColors.onPrimary.withValues(
                                  alpha: 0.8,
                                ),
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
          ),
        ),
        const SizedBox(width: AppSpacing.md),
        // Kartu Mobil Saya (White Surface)
        Expanded(
          child: Material(
            color: Colors.transparent,
            borderRadius: BorderRadius.circular(AppRadii.card),
            clipBehavior: Clip.antiAlias,
            child: InkWell(
              onTap: onMobilSayaTap,
              child: Container(
                height: 130,
                padding: const EdgeInsets.all(AppSpacing.md),
                decoration: BoxDecoration(
                  color: AppColors.surface,
                  borderRadius: BorderRadius.circular(AppRadii.card),
                  border: Border.all(color: AppColors.borderSubtle, width: 1),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Container(
                          width: 38,
                          height: 38,
                          decoration: BoxDecoration(
                            color: AppColors.surfaceContainer,
                            borderRadius: BorderRadius.circular(AppRadii.sm),
                          ),
                          child: const Icon(
                            Icons.directions_car_rounded,
                            size: 20,
                            color: AppColors.primaryContainer,
                          ),
                        ),
                        const Icon(
                          Icons.chevron_right_rounded,
                          size: 20,
                          color: AppColors.outline,
                        ),
                      ],
                    ),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Mobil saya',
                          style: textTheme.titleMedium?.copyWith(
                            color: AppColors.textPrimary,
                          ),
                        ),
                        const SizedBox(height: AppSpacing.xs / 2),
                        Text(
                          '$jumlahMobil Mobil terdaftar',
                          style: textTheme.bodySmall,
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
          ),
        ),
      ],
    );
  }
}
