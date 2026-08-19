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
  String get stopVoice => _text('stopVoice');
  String get confidence => _text('confidence');
  String get speechFailed => _text('speechFailed');
  String get shonaSpeechFallback => _text('shonaSpeechFallback');
  String get home => _text('home');
  String get diagnose => _text('diagnose');
  String get history => _text('history');
  String get crops => _text('crops');
  String get profile => _text('profile');
  String get startDiagnosis => _text('startDiagnosis');
  String get diagnosisHistory => _text('diagnosisHistory');
  String get cropLibrary => _text('cropLibrary');
  String get viewDiagnosisHistory => _text('viewDiagnosisHistory');
  String get openCropLibrary => _text('openCropLibrary');
  String get hello => _text('hello');
  String get phaseOne => _text('phaseOne');
  String get homeIntroduction => _text('homeIntroduction');
  String get useLightMode => _text('useLightMode');
  String get useDarkMode => _text('useDarkMode');
  String get accountMenu => _text('accountMenu');
  String get logOut => _text('logOut');
  String get profileHint => _text('profileHint');
  String get fullName => _text('fullName');
  String get enterName => _text('enterName');
  String get phoneNumber => _text('phoneNumber');
  String get preferredLanguage => _text('preferredLanguage');
  String get english => _text('english');
  String get shona => _text('shona');
  String get savingProfile => _text('savingProfile');
  String get saveProfile => _text('saveProfile');
  String get couldNotLoadCrops => _text('couldNotLoadCrops');
  String get noCropsYet => _text('noCropsYet');
  String get noCropsMessage => _text('noCropsMessage');
  String get cropPicture => _text('cropPicture');
  String get more => _text('more');
  String get cerealCrop => _text('cerealCrop');
  String get fruitVegetable => _text('fruitVegetable');
  String get rootCrop => _text('rootCrop');
  String get vegetableCrop => _text('vegetableCrop');
  String get legumeCrop => _text('legumeCrop');
  String get oilseedCrop => _text('oilseedCrop');
  String get cucurbitCrop => _text('cucurbitCrop');
  String get supportedCrop => _text('supportedCrop');

  String _text(String key) {
    return _localized[languageCode]?[key] ?? _localized['en']![key]!;
  }
}

const _localized = {
  'en': {
    'diagnosisResult': 'Diagnosis result',
    'lowConfidence': 'Low confidence',
    'retakePhoto':
        'Retake the photo in brighter light or consult an extension officer.',
    'recommendation': 'Recommendation',
    'fieldAdvice': 'Field advice',
    'disclaimer':
        'Use this result as support for field decisions, not as a replacement for professional agricultural advice.',
    'speakResult': 'Read result aloud',
    'stopVoice': 'Stop voice',
    'confidence': 'Confidence',
    'speechFailed': 'This phone could not read the result aloud.',
    'shonaSpeechFallback':
        'A Shona voice is not installed on this phone, so English speech was used.',
    'home': 'Home',
    'diagnose': 'Diagnose',
    'history': 'History',
    'crops': 'Crops',
    'profile': 'Profile',
    'startDiagnosis': 'Start diagnosis',
    'diagnosisHistory': 'Diagnosis history',
    'cropLibrary': 'Crop library',
    'viewDiagnosisHistory': 'View diagnosis history',
    'openCropLibrary': 'Open crop library',
    'hello': 'Hello',
    'phaseOne': 'PHASE 1',
    'homeIntroduction':
        'Use CropDetec to photograph a crop leaf, review its diagnosis, and keep a history of results.',
    'useLightMode': 'Use light mode',
    'useDarkMode': 'Use dark mode',
    'accountMenu': 'Account menu',
    'logOut': 'Log out',
    'profileHint': 'Update your farmer profile and preferred language.',
    'fullName': 'Full name',
    'enterName': 'Enter your name.',
    'phoneNumber': 'Phone number',
    'preferredLanguage': 'Preferred language',
    'english': 'English',
    'shona': 'Shona',
    'savingProfile': 'Saving profile',
    'saveProfile': 'Save profile',
    'couldNotLoadCrops': 'Could not load crops',
    'noCropsYet': 'No crops yet',
    'noCropsMessage': 'Seed crop and disease records from the backend first.',
    'cropPicture': 'crop picture',
    'more': 'more',
    'cerealCrop': 'Cereal crop',
    'fruitVegetable': 'Fruit vegetable',
    'rootCrop': 'Root crop',
    'vegetableCrop': 'Vegetable crop',
    'legumeCrop': 'Legume crop',
    'oilseedCrop': 'Oilseed crop',
    'cucurbitCrop': 'Cucurbit crop',
    'supportedCrop': 'Supported crop',
  },
  'sn': {
    'diagnosisResult': 'Mhedzisiro yekuongorora',
    'lowConfidence': 'Chivimbo chakaderera',
    'retakePhoto':
        'Tora mufananidzo zvakare muchiedza chakanaka kana kubvunza nyanzvi yezvekurima.',
    'recommendation': 'Kurudziro',
    'fieldAdvice': 'Zano remumunda',
    'disclaimer':
        'Shandisa mhedzisiro iyi sekubatsira pakusarudza zvekuita mumunda, kwete kutsiva zano renyanzvi yezvekurima.',
    'speakResult': 'Verenga mhedzisiro',
    'stopVoice': 'Misa inzwi',
    'confidence': 'Chivimbo',
    'speechFailed': 'Foni iyi haina kukwanisa kuverenga mhedzisiro.',
    'shonaSpeechFallback':
        'Inzwi reChiShona harina kuiswa pafoni iyi, saka inzwi reChirungu rashandiswa.',
    'home': 'Kumba',
    'diagnose': 'Ongorora',
    'history': 'Nhoroondo',
    'crops': 'Zvirimwa',
    'profile': 'Nhoroondo yako',
    'startDiagnosis': 'Tanga kuongorora',
    'diagnosisHistory': 'Nhoroondo yekuongorora',
    'cropLibrary': 'Ruzivo rwezvirimwa',
    'viewDiagnosisHistory': 'Ona nhoroondo yekuongorora',
    'openCropLibrary': 'Vhura ruzivo rwezvirimwa',
    'hello': 'Mhoro',
    'phaseOne': 'CHIKAMU 1',
    'homeIntroduction':
        'Shandisa CropDetec kutora mufananidzo weshizha, kuona mhedzisiro, uye kuchengeta nhoroondo.',
    'useLightMode': 'Shandisa chiedza',
    'useDarkMode': 'Shandisa rima',
    'accountMenu': 'Menyu yeakaundi',
    'logOut': 'Buda',
    'profileHint': 'Gadzirisa ruzivo rwako nemutauro waunoda.',
    'fullName': 'Zita rakazara',
    'enterName': 'Nyora zita rako.',
    'phoneNumber': 'Nhamba yefoni',
    'preferredLanguage': 'Mutauro waunoda',
    'english': 'Chirungu',
    'shona': 'ChiShona',
    'savingProfile': 'Ruzivo rwuri kuchengetwa',
    'saveProfile': 'Chengetedza',
    'couldNotLoadCrops': 'Zvirimwa hazvina kukwanisa kuwanikwa',
    'noCropsYet': 'Hapana zvirimwa parizvino',
    'noCropsMessage': 'Tanga waisa ruzivo rwezvirimwa nezvirwere paseva.',
    'cropPicture': 'mufananidzo wechirimwa',
    'more': 'zvimwe',
    'cerealCrop': 'Chirimwa chezviyo',
    'fruitVegetable': 'Muriwo une muchero',
    'rootCrop': 'Chirimwa chemudzi',
    'vegetableCrop': 'Chirimwa chemuriwo',
    'legumeCrop': 'Chirimwa chebhinzi',
    'oilseedCrop': 'Chirimwa chembeu dzemafuta',
    'cucurbitCrop': 'Chirimwa chemhuri yemanhanga',
    'supportedCrop': 'Chirimwa chinotsigirwa',
  },
};
