# pmmauto_mobile

A new Flutter project.

## Getting Started

This project is a starting point for a Flutter application.

A few resources to get you started if this is your first Flutter project:

- [Learn Flutter](https://docs.flutter.dev/get-started/learn-flutter)
- [Write your first Flutter app](https://docs.flutter.dev/get-started/codelab)
- [Flutter learning resources](https://docs.flutter.dev/reference/learning-resources)

For help getting started with Flutter development, view the
[online documentation](https://docs.flutter.dev/), which offers tutorials,
samples, guidance on mobile development, and a full API reference.

## Local backend

The API origin is supplied explicitly with `--dart-define=API_BASE_URL` so
the same build works across emulators and physical devices:

```sh
# Android emulator
flutter run --dart-define=API_BASE_URL=http://10.0.2.2:8000

# iOS Simulator
flutter run --dart-define=API_BASE_URL=http://127.0.0.1:8000

# Physical device on the same network as the backend
flutter run --dart-define=API_BASE_URL=http://<your-machine-lan-ip>:8000
```

The value is the backend origin only; the app appends `/api/v1`.

For a physical device, bind Laravel to the host interface (for example,
`php artisan serve --host=0.0.0.0`) and allow the port through the host
firewall.
