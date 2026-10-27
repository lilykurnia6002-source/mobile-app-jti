import 'package:animations/animations.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import 'common/theme/app_theme.dart';
import 'common/widgets/main_shell.dart';
import 'features/auth/data/providers/auth_provider.dart';
import 'features/auth/views/screens/login_screen.dart';
import 'features/auth/views/screens/register_screen.dart';
import 'features/dashboard/views/screens/dashboard_screen.dart';
import 'features/mobil/views/screens/mobil_list_screen.dart';
import 'features/notifikasi/views/screens/notifikasi_screen.dart';
import 'features/pesanan/views/screens/booking_detail_screen.dart';
import 'features/pesanan/views/screens/booking_form_screen.dart';
import 'features/pesanan/views/screens/booking_confirmation_screen.dart';
import 'features/pesanan/views/screens/pesanan_screen.dart';
import 'features/profil/views/screens/profil_screen.dart';
import 'features/reports/views/screens/owner_reports_screen.dart';
import 'features/nota/views/screens/nota_detail_screen.dart';

/// Menyalakan ulang redirect GoRouter setiap kali status login berubah.
class _AuthRefreshNotifier extends ChangeNotifier {
  _AuthRefreshNotifier(Ref ref) {
    ref.listen(authControllerProvider, (_, _) => notifyListeners());
  }
}

final appRouterProvider = Provider<GoRouter>((ref) {
  final refresh = _AuthRefreshNotifier(ref);

  return GoRouter(
    initialLocation: LoginScreen.routePath,
    refreshListenable: refresh,
    redirect: (context, state) {
      final auth = ref.read(authControllerProvider);
      final session = auth.asData?.value;
      final loggedIn = session != null;
      final goingToLogin = state.matchedLocation == LoginScreen.routePath;
      final goingToRegister = state.matchedLocation == RegisterScreen.routePath;
      final goingToOwnerReports =
          state.matchedLocation == OwnerReportsScreen.routePath;
      if (!loggedIn && !goingToLogin && !goingToRegister) {
        return LoginScreen.routePath;
      }
      if (!loggedIn) return null;
      if (session.isOwner) {
        if (goingToLogin || goingToRegister) {
          return OwnerReportsScreen.routePath;
        }
        if (!goingToOwnerReports) return OwnerReportsScreen.routePath;
        return null;
      }
      if (goingToOwnerReports) return DashboardScreen.routePath;
      if (goingToLogin || goingToRegister) return DashboardScreen.routePath;
      return null;
    },
    routes: [
      GoRoute(
        path: LoginScreen.routePath,
        builder: (context, state) => const LoginScreen(),
      ),
      GoRoute(
        path: RegisterScreen.routePath,
        builder: (context, state) => const RegisterScreen(),
      ),
      GoRoute(
        path: OwnerReportsScreen.routePath,
        builder: (context, state) => const OwnerReportsScreen(),
      ),
      GoRoute(
        path: BookingFormScreen.routePath,
        builder: (context, state) => const BookingFormScreen(),
      ),
      GoRoute(
        path: BookingConfirmationScreen.routePath,
        redirect: (context, state) => state.extra is BookingConfirmationData
            ? null
            : BookingFormScreen.routePath,
        builder: (context, state) => BookingConfirmationScreen(
          data: state.extra! as BookingConfirmationData,
        ),
      ),
      GoRoute(
        path: MobilListScreen.routePath,
        builder: (context, state) => const MobilListScreen(),
      ),
      GoRoute(
        path: '${NotaDetailScreen.routePath}/:id',
        builder: (context, state) =>
            NotaDetailScreen(orderId: state.pathParameters['id'] ?? ''),
      ),
      GoRoute(
        path: '${BookingDetailScreen.routePath}/:id',
        pageBuilder: (context, state) => _fadeThroughPage(
          key: state.pageKey,
          child: BookingDetailScreen(orderId: state.pathParameters['id'] ?? ''),
        ),
      ),
      StatefulShellRoute.indexedStack(
        builder: (context, state, navigationShell) =>
            MainShell(navigationShell: navigationShell),
        branches: [
          StatefulShellBranch(
            routes: [
              GoRoute(
                path: DashboardScreen.routePath,
                builder: (context, state) => const DashboardScreen(),
              ),
            ],
          ),
          StatefulShellBranch(
            routes: [
              GoRoute(
                path: PesananScreen.routePath,
                builder: (context, state) => const PesananScreen(),
              ),
            ],
          ),
          StatefulShellBranch(
            routes: [
              GoRoute(
                path: NotifikasiScreen.routePath,
                builder: (context, state) => const NotifikasiScreen(),
              ),
            ],
          ),
          StatefulShellBranch(
            routes: [
              GoRoute(
                path: ProfilScreen.routePath,
                builder: (context, state) => const ProfilScreen(),
              ),
            ],
          ),
        ],
      ),
    ],
  );
});

Page<void> _fadeThroughPage({required LocalKey key, required Widget child}) {
  return CustomTransitionPage<void>(
    key: key,
    child: child,
    transitionDuration: const Duration(milliseconds: 180),
    reverseTransitionDuration: const Duration(milliseconds: 120),
    transitionsBuilder: (context, animation, secondaryAnimation, child) {
      if (MediaQuery.disableAnimationsOf(context)) return child;
      return FadeThroughTransition(
        animation: animation,
        secondaryAnimation: secondaryAnimation,
        fillColor: AppColors.background,
        child: child,
      );
    },
  );
}
