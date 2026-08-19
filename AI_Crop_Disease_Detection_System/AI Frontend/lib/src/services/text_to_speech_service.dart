import 'package:flutter/services.dart';

class TextToSpeechResult {
  const TextToSpeechResult({
    required this.success,
    required this.requestedLanguage,
    required this.actualLanguage,
    required this.fallbackUsed,
    required this.message,
  });

  const TextToSpeechResult.failure(this.message)
    : success = false,
      requestedLanguage = '',
      actualLanguage = '',
      fallbackUsed = false;

  final bool success;
  final String requestedLanguage;
  final String actualLanguage;
  final bool fallbackUsed;
  final String message;

  factory TextToSpeechResult.fromMap(Map<Object?, Object?> map) {
    return TextToSpeechResult(
      success: map['success'] as bool? ?? false,
      requestedLanguage: map['requestedLanguage'] as String? ?? '',
      actualLanguage: map['actualLanguage'] as String? ?? '',
      fallbackUsed: map['fallbackUsed'] as bool? ?? false,
      message: map['message'] as String? ?? '',
    );
  }
}

class TextToSpeechService {
  const TextToSpeechService();

  static const _channel = MethodChannel('ai_crop_disease_detection/tts');

  Future<TextToSpeechResult> speak({
    required String text,
    required String languageCode,
  }) async {
    try {
      final response = await _channel.invokeMethod<Map<Object?, Object?>>(
        'speak',
        {'text': text, 'languageCode': languageCode},
      );
      if (response == null) {
        return const TextToSpeechResult.failure(
          'The phone did not return a speech result.',
        );
      }
      return TextToSpeechResult.fromMap(response);
    } on PlatformException catch (error) {
      return TextToSpeechResult.failure(
        error.message ?? 'Text-to-speech is unavailable.',
      );
    } on MissingPluginException {
      return const TextToSpeechResult.failure(
        'Text-to-speech is unavailable on this device.',
      );
    }
  }

  Future<bool> stop() async {
    try {
      return await _channel.invokeMethod<bool>('stop') ?? false;
    } on PlatformException {
      return false;
    } on MissingPluginException {
      return false;
    }
  }
}
