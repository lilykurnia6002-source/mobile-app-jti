import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../features/notifikasi/data/providers/notifikasi_provider.dart';
import '../theme/app_theme.dart';

class MainShell extends ConsumerWidget {
  const MainShell({required this.navigationShell, super.key});
  final StatefulNavigationShell navigationShell;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final unreadCount = ref.watch(unreadNotificationCountProvider);

    return Scaffold(
      backgroundColor: AppColors.background,
      body: navigationShell,
      bottomNavigationBar: _StitchBottomNavBar(
        currentIndex: navigationShell.currentIndex,
        unreadNotificationCount: unreadCount.asData?.value,
        onTap: (index) => navigationShell.goBranch(
          index,
          initialLocation: index == navigationShell.currentIndex,
        ),
      ),
    );
  }
}

class _StitchBottomNavBar extends StatelessWidget {
  const _StitchBottomNavBar({
    required this.currentIndex,
    required this.unreadNotificationCount,
    required this.onTap,
  });

  final int currentIndex;
  final int? unreadNotificationCount;
  final ValueChanged<int> onTap;

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(
        color: AppColors.surface,
        border: Border(
          top: BorderSide(color: AppColors.borderSubtle, width: 1),
        ),
      ),
      child: SafeArea(
        top: false,
        child: SizedBox(
          height: 72,
          child: Row(
            children: [
              Expanded(
                child: _buildNavItem(
                  context,
                  index: 0,
                  icon: Icons.home_rounded,
                  label: 'Beranda',
                ),
              ),
              Expanded(
                child: _buildNavItem(
                  context,
                  index: 1,
                  icon: Icons.build_circle_rounded,
                  label: 'Pesanan',
                ),
              ),
              Expanded(
                child: _buildNavItem(
                  context,
                  index: 2,
                  icon: Icons.notifications_rounded,
                  label: 'Notifikasi',
                  badgeCount: unreadNotificationCount,
                ),
              ),
              Expanded(
                child: _buildNavItem(
                  context,
                  index: 3,
                  icon: Icons.person_rounded,
                  label: 'Profil',
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildNavItem(
    BuildContext context, {
    required int index,
    required IconData icon,
    required String label,
    int? badgeCount,
  }) {
    final isSelected = currentIndex == index;
    final color = isSelected ? AppColors.primaryContainer : AppColors.outline;
    final textTheme = Theme.of(context).textTheme;

    return Semantics(
      button: true,
      selected: isSelected,
      label: label,
      child: Padding(
        padding: const EdgeInsets.symmetric(
          horizontal: AppSpacing.xs,
          vertical: AppSpacing.xs,
        ),
        child: Material(
          color: isSelected
              ? AppColors.surfaceContainerLow
              : Colors.transparent,
          borderRadius: BorderRadius.circular(AppRadii.md),
          clipBehavior: Clip.antiAlias,
          child: InkWell(
            onTap: () => onTap(index),
            child: Padding(
              padding: const EdgeInsets.symmetric(
                horizontal: AppSpacing.sm,
                vertical: AppSpacing.xs,
              ),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Stack(
                    clipBehavior: Clip.none,
                    children: [
                      Icon(icon, size: 22, color: color),
                      if (badgeCount != null && badgeCount > 0)
                        Positioned(
                          top: -7,
                          right: -11,
                          child: Container(
                            constraints: const BoxConstraints(
                              minWidth: 17,
                              minHeight: 17,
                            ),
                            padding: const EdgeInsets.symmetric(
                              horizontal: AppSpacing.xs,
                            ),
                            alignment: Alignment.center,
                            decoration: BoxDecoration(
                              color: AppColors.secondaryContainer,
                              borderRadius: BorderRadius.circular(AppRadii.sm),
                              border: Border.all(
                                color: AppColors.surface,
                                width: 1.5,
                              ),
                            ),
                            child: Text(
                              badgeCount > 99 ? '99+' : '$badgeCount',
                              style: textTheme.labelSmall?.copyWith(
                                color: AppColors.onSecondaryContainer,
                                fontSize: 9,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                          ),
                        ),
                    ],
                  ),
                  const SizedBox(height: AppSpacing.xs),
                  Text(
                    label,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: textTheme.labelSmall?.copyWith(
                      color: color,
                      fontWeight: isSelected
                          ? FontWeight.w700
                          : FontWeight.w500,
                    ),
                  ),
                  const SizedBox(height: AppSpacing.xs),
                  if (isSelected)
                    Container(
                      width: 5,
                      height: 5,
                      decoration: const BoxDecoration(
                        color: AppColors.secondaryContainer,
                        shape: BoxShape.circle,
                      ),
                    )
                  else
                    const SizedBox(height: 5),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
