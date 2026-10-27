import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';

import '../../../../common/theme/app_theme.dart';
import '../../../pesanan/views/screens/booking_detail_screen.dart';
import '../../data/models/notifikasi_model.dart';
import '../../data/providers/notifikasi_provider.dart';

class NotifikasiScreen extends ConsumerWidget {
  static const routePath = '/notifikasi';

  const NotifikasiScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final notifikasi = ref.watch(notifikasiProvider);
    final unreadCount = ref.watch(unreadNotificationCountProvider);

    Future<void> loadMore() async {
      try {
        await ref.read(notifikasiProvider.notifier).loadMore();
      } catch (_) {
        if (!context.mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Gagal memuat notifikasi lainnya.')),
        );
      }
    }

    Future<void> openNotification(NotifikasiModel item) async {
      try {
        if (!item.dibaca) {
          await ref
              .read(notifikasiProvider.notifier)
              .markAsRead(item.idNotifikasi);
        }
        if (!context.mounted) return;

        final orderId = item.idPesanan;
        if (orderId != null && orderId > 0) {
          await context.push('${BookingDetailScreen.routePath}/$orderId');
        }
      } catch (_) {
        if (!context.mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Notifikasi belum dapat diperbarui.')),
        );
      }
    }

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Notifikasi'),
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(1),
          child: Container(height: 1, color: AppColors.borderSubtle),
        ),
      ),
      body: RefreshIndicator(
        color: AppColors.primaryContainer,
        backgroundColor: AppColors.surface,
        onRefresh: () async {
          try {
            ref.invalidate(notifikasiProvider);
            await ref.read(notifikasiProvider.future);
          } finally {
            ref.invalidate(unreadNotificationCountProvider);
          }
        },
        child: notifikasi.when(
          loading: () => const _LoadingState(),
          error: (error, _) =>
              _LoadError(onRetry: () => ref.invalidate(notifikasiProvider)),
          data: (page) {
            final unreadLabel = unreadCount.when(
              data: (count) => count > 0 ? '$count belum dibaca' : null,
              loading: () => null,
              error: (_, _) => null,
            );

            if (page.items.isEmpty) {
              return _EmptyNotifications(unreadLabel: unreadLabel);
            }

            return NotificationListener<ScrollNotification>(
              onNotification: (notification) {
                if (notification is ScrollUpdateNotification &&
                    notification.metrics.extentAfter < 320 &&
                    page.hasNextPage &&
                    !page.isLoadingMore) {
                  unawaited(loadMore());
                }
                return false;
              },
              child: ListView.separated(
                physics: const AlwaysScrollableScrollPhysics(),
                padding: const EdgeInsets.fromLTRB(
                  AppSpacing.lg,
                  AppSpacing.md,
                  AppSpacing.lg,
                  AppSpacing.xl,
                ),
                itemCount:
                    page.items.length + 1 + (unreadLabel == null ? 0 : 1),
                separatorBuilder: (_, _) =>
                    const SizedBox(height: AppSpacing.sm),
                itemBuilder: (context, index) {
                  if (unreadLabel != null && index == 0) {
                    return _UnreadSummary(label: unreadLabel);
                  }

                  final itemIndex = index - (unreadLabel == null ? 0 : 1);
                  if (itemIndex == page.items.length) {
                    if (!page.hasNextPage) {
                      return const _ListEnd();
                    }
                    return _LoadMoreButton(
                      isLoading: page.isLoadingMore,
                      onPressed: loadMore,
                    );
                  }

                  final item = page.items[itemIndex];
                  final canOpen =
                      !item.dibaca ||
                      (item.idPesanan != null && item.idPesanan! > 0);
                  return _NotificationTile(
                    notification: item,
                    onTap: canOpen ? () => openNotification(item) : null,
                  );
                },
              ),
            );
          },
        ),
      ),
    );
  }
}

class _LoadingState extends StatelessWidget {
  const _LoadingState();

  @override
  Widget build(BuildContext context) {
    return ListView(
      physics: const AlwaysScrollableScrollPhysics(),
      padding: const EdgeInsets.fromLTRB(
        AppSpacing.lg,
        AppSpacing.xxl,
        AppSpacing.lg,
        AppSpacing.xl,
      ),
      children: [
        const SizedBox(height: AppSpacing.xxl),
        Center(
          child: Semantics(
            label: 'Memuat notifikasi',
            child: const CircularProgressIndicator(),
          ),
        ),
      ],
    );
  }
}

class _UnreadSummary extends StatelessWidget {
  const _UnreadSummary({required this.label});

  final String label;

  @override
  Widget build(BuildContext context) {
    return Semantics(
      liveRegion: true,
      label: label,
      child: Row(
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
            label,
            style: Theme.of(context).textTheme.labelMedium?.copyWith(
              color: AppColors.onSurfaceVariant,
            ),
          ),
        ],
      ),
    );
  }
}

class _LoadMoreButton extends StatelessWidget {
  const _LoadMoreButton({required this.isLoading, required this.onPressed});

  final bool isLoading;
  final VoidCallback onPressed;

  @override
  Widget build(BuildContext context) {
    return Center(
      child: OutlinedButton.icon(
        onPressed: isLoading ? null : onPressed,
        icon: isLoading
            ? const SizedBox(
                width: 18,
                height: 18,
                child: CircularProgressIndicator(strokeWidth: 2),
              )
            : const Icon(Icons.expand_more_rounded),
        label: Text(isLoading ? 'Memuat...' : 'Muat notifikasi lainnya'),
      ),
    );
  }
}

class _ListEnd extends StatelessWidget {
  const _ListEnd();

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(top: AppSpacing.xs),
      child: Center(
        child: Text(
          'Semua notifikasi sudah ditampilkan',
          style: Theme.of(context).textTheme.labelSmall,
        ),
      ),
    );
  }
}

class _NotificationTile extends StatelessWidget {
  const _NotificationTile({required this.notification, required this.onTap});

  final NotifikasiModel notification;
  final VoidCallback? onTap;

  @override
  Widget build(BuildContext context) {
    final isUnread = !notification.dibaca;
    final canOpen = onTap != null;
    final textTheme = Theme.of(context).textTheme;
    final dateLabel = notification.waktuKirim == null
        ? null
        : _formatWaktu(notification.waktuKirim!);
    final semanticsParts = <String>[
      isUnread ? 'Belum dibaca' : 'Sudah dibaca',
      notification.judul,
      notification.pesan,
    ];
    if (dateLabel != null) semanticsParts.add(dateLabel);
    if (canOpen) semanticsParts.add('Buka detail');
    final semanticsLabel = semanticsParts.join('. ');

    return Semantics(
      container: true,
      button: canOpen,
      label: semanticsLabel,
      child: Material(
        color: isUnread ? AppColors.surfaceContainerLow : AppColors.surface,
        borderRadius: BorderRadius.circular(AppRadii.card),
        clipBehavior: Clip.antiAlias,
        child: InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(AppRadii.card),
          child: Container(
            padding: const EdgeInsets.all(AppSpacing.lg),
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(AppRadii.card),
              border: Border.all(
                color: isUnread
                    ? AppColors.surfaceContainerHigh
                    : AppColors.borderSubtle,
              ),
            ),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                _NotificationIcon(isUnread: isUnread),
                const SizedBox(width: AppSpacing.md),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        notification.judul,
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                        style: textTheme.titleSmall?.copyWith(
                          color: AppColors.textPrimary,
                          fontWeight: isUnread
                              ? FontWeight.w700
                              : FontWeight.w600,
                        ),
                      ),
                      const SizedBox(height: AppSpacing.xs),
                      Text(
                        notification.pesan,
                        maxLines: 4,
                        overflow: TextOverflow.ellipsis,
                        style: textTheme.bodyMedium?.copyWith(
                          color: isUnread
                              ? AppColors.onSurfaceVariant
                              : AppColors.textMuted,
                          height: 1.45,
                        ),
                      ),
                      if (dateLabel != null) ...[
                        const SizedBox(height: AppSpacing.sm),
                        Text(
                          dateLabel,
                          style: textTheme.labelSmall?.copyWith(
                            color: AppColors.textMuted,
                          ),
                        ),
                      ],
                    ],
                  ),
                ),
                if (isUnread)
                  const Padding(
                    padding: EdgeInsets.only(left: AppSpacing.sm, top: 5),
                    child: SizedBox(
                      width: 8,
                      height: 8,
                      child: DecoratedBox(
                        decoration: BoxDecoration(
                          color: AppColors.secondaryContainer,
                          shape: BoxShape.circle,
                        ),
                      ),
                    ),
                  )
                else if (canOpen)
                  const Padding(
                    padding: EdgeInsets.only(left: AppSpacing.xs, top: 12),
                    child: Icon(
                      Icons.chevron_right_rounded,
                      color: AppColors.outline,
                      size: 20,
                    ),
                  ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  String _formatWaktu(String value) {
    final waktu =
        DateTime.tryParse(value) ??
        DateTime.tryParse(value.replaceFirst(' ', 'T'));
    if (waktu == null) return value;

    final local = waktu.toLocal();
    final now = DateTime.now();
    final sameDay =
        local.year == now.year &&
        local.month == now.month &&
        local.day == now.day;
    if (sameDay) {
      return 'Hari ini, ${DateFormat('HH:mm').format(local)}';
    }

    final yesterday = now.subtract(const Duration(days: 1));
    final isYesterday =
        local.year == yesterday.year &&
        local.month == yesterday.month &&
        local.day == yesterday.day;
    if (isYesterday) {
      return 'Kemarin, ${DateFormat('HH:mm').format(local)}';
    }

    return DateFormat('d MMM yyyy, HH:mm', 'id_ID').format(local);
  }
}

class _NotificationIcon extends StatelessWidget {
  const _NotificationIcon({required this.isUnread});

  final bool isUnread;

  @override
  Widget build(BuildContext context) {
    return ExcludeSemantics(
      child: Container(
        width: 44,
        height: 44,
        alignment: Alignment.center,
        decoration: BoxDecoration(
          color: isUnread
              ? AppColors.primaryContainer
              : AppColors.surfaceContainer,
          borderRadius: BorderRadius.circular(AppRadii.md),
        ),
        child: Icon(
          isUnread
              ? Icons.notifications_active_rounded
              : Icons.notifications_none_rounded,
          color: isUnread ? AppColors.onPrimary : AppColors.primaryContainer,
          size: 22,
        ),
      ),
    );
  }
}

class _EmptyNotifications extends StatelessWidget {
  const _EmptyNotifications({this.unreadLabel});

  final String? unreadLabel;

  @override
  Widget build(BuildContext context) {
    return ListView(
      physics: const AlwaysScrollableScrollPhysics(),
      padding: const EdgeInsets.fromLTRB(
        AppSpacing.xl,
        AppSpacing.xxl,
        AppSpacing.xl,
        AppSpacing.xl,
      ),
      children: [
        if (unreadLabel != null) ...[
          _UnreadSummary(label: unreadLabel!),
          const SizedBox(height: AppSpacing.xxl),
        ] else
          const SizedBox(height: AppSpacing.xxl),
        const Icon(
          Icons.notifications_none_rounded,
          size: 52,
          color: AppColors.outline,
          semanticLabel: 'Tidak ada notifikasi',
        ),
        const SizedBox(height: AppSpacing.lg),
        Text(
          'Belum ada notifikasi',
          textAlign: TextAlign.center,
          style: Theme.of(context).textTheme.titleMedium,
        ),
        const SizedBox(height: AppSpacing.xs),
        Text(
          'Pembaruan pesanan dan servis akan muncul di sini.',
          textAlign: TextAlign.center,
          style: Theme.of(context).textTheme.bodyMedium,
        ),
      ],
    );
  }
}

class _LoadError extends StatelessWidget {
  const _LoadError({required this.onRetry});

  final VoidCallback onRetry;

  @override
  Widget build(BuildContext context) {
    return ListView(
      physics: const AlwaysScrollableScrollPhysics(),
      padding: const EdgeInsets.fromLTRB(
        AppSpacing.xl,
        AppSpacing.xxl,
        AppSpacing.xl,
        AppSpacing.xl,
      ),
      children: [
        const SizedBox(height: AppSpacing.xxl),
        const Icon(
          Icons.cloud_off_rounded,
          size: 48,
          color: AppColors.outline,
          semanticLabel: 'Notifikasi tidak dapat dimuat',
        ),
        const SizedBox(height: AppSpacing.lg),
        Text(
          'Notifikasi belum dapat dimuat',
          textAlign: TextAlign.center,
          style: Theme.of(context).textTheme.titleMedium,
        ),
        const SizedBox(height: AppSpacing.xs),
        Text(
          'Periksa koneksi lalu coba lagi.',
          textAlign: TextAlign.center,
          style: Theme.of(context).textTheme.bodyMedium,
        ),
        const SizedBox(height: AppSpacing.lg),
        Center(
          child: OutlinedButton.icon(
            onPressed: onRetry,
            icon: const Icon(Icons.refresh_rounded),
            label: const Text('Coba lagi'),
          ),
        ),
      ],
    );
  }
}
