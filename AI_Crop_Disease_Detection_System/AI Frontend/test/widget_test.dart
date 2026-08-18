import 'package:ai_crop_disease_detection/src/app.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  testWidgets('shows the farmer dashboard shell', (tester) async {
    await tester.pumpWidget(const CropDiseaseApp());

    expect(find.text('CropDetec'), findsWidgets);
    await tester.pump();

    expect(find.text('Welcome back'), findsOneWidget);
    expect(find.text('Create account'), findsOneWidget);
  });
}
