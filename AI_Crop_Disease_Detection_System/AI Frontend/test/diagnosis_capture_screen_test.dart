import 'dart:convert';
import 'dart:io';

import 'package:ai_crop_disease_detection/src/models/api_result.dart';
import 'package:ai_crop_disease_detection/src/models/crop.dart';
import 'package:ai_crop_disease_detection/src/models/diagnosis.dart';
import 'package:ai_crop_disease_detection/src/screens/diagnosis_capture_screen.dart';
import 'package:ai_crop_disease_detection/src/services/api_client.dart';
import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:image_picker/image_picker.dart';

class _RejectingApiClient extends ApiClient {
  @override
  Future<ApiResult<List<Crop>>> crops() async {
    return const ApiResult.success([]);
  }

  @override
  Future<ApiResult<Diagnosis>> storeDiagnosis({
    required File image,
    int? cropId,
  }) async {
    return const ApiResult.failure(
      'No crop leaf was detected. Retake a clear photo with one leaf filling most of the frame.',
      errorCode: 'crop_leaf_not_detected',
    );
  }
}

void main() {
  testWidgets('shows a retake error when the API rejects a non-leaf image', (
    tester,
  ) async {
    final temporaryDirectory = await Directory.systemTemp.createTemp(
      'cropdetec-camera-test',
    );
    final image = File('${temporaryDirectory.path}/not-a-leaf.png');
    await image.writeAsBytes(
      base64Decode(
        'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
      ),
    );
    addTearDown(() => temporaryDirectory.delete(recursive: true));

    await tester.pumpWidget(
      MaterialApp(
        home: DiagnosisCaptureScreen(
          apiClient: _RejectingApiClient(),
          pickImage: (_) async => XFile(image.path),
        ),
      ),
    );
    await tester.pumpAndSettle();

    await tester.tap(find.text('Camera'));
    await tester.pumpAndSettle();
    await tester.tap(find.text('Run diagnosis'));
    await tester.pumpAndSettle();

    expect(find.textContaining('No crop leaf was detected'), findsOneWidget);
    expect(find.text('Retake photo'), findsOneWidget);
    expect(find.byIcon(Icons.error_outline), findsOneWidget);
  });
}
