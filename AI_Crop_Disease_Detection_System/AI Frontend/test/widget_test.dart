import 'package:ai_crop_disease_detection/src/app.dart';
import 'package:ai_crop_disease_detection/src/services/api_client.dart';
import 'package:ai_crop_disease_detection/src/services/session_store.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  testWidgets('restores an empty session and shows login', (tester) async {
    final apiClient = ApiClient(sessionStore: InMemorySessionStore());

    await tester.pumpWidget(CropDiseaseApp(apiClient: apiClient));

    expect(find.text('CropDetec'), findsWidgets);
    await tester.pumpAndSettle();

    expect(find.text('Welcome back'), findsOneWidget);
    expect(find.text('Create account'), findsOneWidget);
  });
}
