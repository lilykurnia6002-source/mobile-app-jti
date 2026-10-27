import 'package:flutter/material.dart';

import '../../../../common/theme/app_theme.dart';

class StatusBadge extends StatelessWidget {
  const StatusBadge({required this.status, super.key});
  final String status;

  @override
  Widget build(BuildContext context) {
    final (bg, text) =
        statusColors[status] ??
        (AppColors.surfaceContainer, AppColors.onSurfaceVariant);
    final border = statusBorderColors[status] ?? AppColors.outlineVariant;

    return Container(
      padding: const EdgeInsets.symmetric(
        horizontal: AppSpacing.sm,
        vertical: AppSpacing.xs,
      ),
      decoration: BoxDecoration(
        color: bg,
        borderRadius: BorderRadius.circular(AppRadii.pill),
        border: Border.all(color: border),
      ),
      child: Text(
        status,
        style: Theme.of(context).textTheme.labelSmall?.copyWith(
          color: text,
          fontSize: 11,
          fontWeight: FontWeight.w700,
        ),
      ),
    );
  }
}
