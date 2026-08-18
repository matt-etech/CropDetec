import 'package:flutter/services.dart';

class TextToSpeechService {
  const TextToSpeechService();

  static const _channel = MethodChannel('ai_crop_disease_detection/tts');

  Future<bool> speak({
    required String text,
    required String languageCode,
  }) async {
    try {
      return await _channel.invokeMethod<bool>('speak', {
            'text': text,
            'languageCode': languageCode,
          }) ??
          false;
    } on PlatformException {
      return false;
    } on MissingPluginException {
      return false;
    }
  }
}
