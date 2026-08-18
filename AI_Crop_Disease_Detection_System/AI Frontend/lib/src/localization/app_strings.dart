class AppStrings {
  const AppStrings._(this.languageCode);

  final String languageCode;

  static AppStrings forLanguage(String languageCode) {
    return AppStrings._(languageCode == 'sn' ? 'sn' : 'en');
  }

  String get diagnosisResult => _text('diagnosisResult');
  String get lowConfidence => _text('lowConfidence');
  String get retakePhoto => _text('retakePhoto');
  String get recommendation => _text('recommendation');
  String get fieldAdvice => _text('fieldAdvice');
  String get disclaimer => _text('disclaimer');
  String get speakResult => _text('speakResult');
  String get confidence => _text('confidence');

  String _text(String key) {
    return _localized[languageCode]?[key] ?? _localized['en']![key]!;
  }
}

const _localized = {
  'en': {
    'diagnosisResult': 'Diagnosis result',
    'lowConfidence': 'Low confidence',
    'retakePhoto': 'Retake the photo in brighter light or consult an extension officer.',
    'recommendation': 'Recommendation',
    'fieldAdvice': 'Field advice',
    'disclaimer': 'Use this result as support for field decisions, not as a replacement for professional agricultural advice.',
    'speakResult': 'Read result aloud',
    'confidence': 'Confidence',
  },
  'sn': {
    'diagnosisResult': 'Mhedzisiro yekuongorora',
    'lowConfidence': 'Chivimbo chakaderera',
    'retakePhoto': 'Tora mufananidzo zvakare muchiedza chakanaka kana kubvunza nyanzvi yezvekurima.',
    'recommendation': 'Kurudziro',
    'fieldAdvice': 'Zano remumunda',
    'disclaimer': 'Shandisa mhedzisiro iyi sekubatsira pakusarudza zvekuita mumunda, kwete kutsiva zano renyanzvi yezvekurima.',
    'speakResult': 'Verenga mhedzisiro',
    'confidence': 'Chivimbo',
  },
};
