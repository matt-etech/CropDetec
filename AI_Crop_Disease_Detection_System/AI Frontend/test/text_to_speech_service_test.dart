import 'package:ai_crop_disease_detection/src/services/text_to_speech_service.dart';
import 'package:flutter/services.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  TestWidgetsFlutterBinding.ensureInitialized();
  const channel = MethodChannel('ai_crop_disease_detection/tts');
  const service = TextToSpeechService();

  tearDown(() async {
    TestDefaultBinaryMessengerBinding.instance.defaultBinaryMessenger
        .setMockMethodCallHandler(channel, null);
  });

  test('reports successful Shona speech when the device supports it', () async {
    TestDefaultBinaryMessengerBinding.instance.defaultBinaryMessenger
        .setMockMethodCallHandler(channel, (call) async {
          expect(call.method, 'speak');
          expect(call.arguments['languageCode'], 'sn');
          return {
            'success': true,
            'requestedLanguage': 'sn',
            'actualLanguage': 'sn',
            'fallbackUsed': false,
            'message': 'Speech started.',
          };
        });

    final result = await service.speak(text: 'Mhedzisiro', languageCode: 'sn');

    expect(result.success, isTrue);
    expect(result.actualLanguage, 'sn');
    expect(result.fallbackUsed, isFalse);
  });

  test('reports the English fallback when Shona is unavailable', () async {
    TestDefaultBinaryMessengerBinding.instance.defaultBinaryMessenger
        .setMockMethodCallHandler(channel, (call) async {
          return {
            'success': true,
            'requestedLanguage': 'sn',
            'actualLanguage': 'en',
            'fallbackUsed': true,
            'message': 'English fallback used.',
          };
        });

    final result = await service.speak(text: 'Mhedzisiro', languageCode: 'sn');

    expect(result.success, isTrue);
    expect(result.actualLanguage, 'en');
    expect(result.fallbackUsed, isTrue);
  });

  test('turns a platform error into a visible failure result', () async {
    TestDefaultBinaryMessengerBinding.instance.defaultBinaryMessenger
        .setMockMethodCallHandler(channel, (call) async {
          throw PlatformException(
            code: 'tts_failed',
            message: 'Speech failed.',
          );
        });

    final result = await service.speak(text: 'Diagnosis', languageCode: 'en');

    expect(result.success, isFalse);
    expect(result.message, 'Speech failed.');
  });

  test('stops speech through the native channel', () async {
    TestDefaultBinaryMessengerBinding.instance.defaultBinaryMessenger
        .setMockMethodCallHandler(channel, (call) async {
          expect(call.method, 'stop');
          return true;
        });

    expect(await service.stop(), isTrue);
  });
}
