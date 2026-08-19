import 'package:ai_crop_disease_detection/src/utils/zimbabwe_phone.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  test('normalizes supported Zimbabwe mobile formats', () {
    expect(normalizeZimbabwePhone('077 123 4567'), '+263771234567');
    expect(normalizeZimbabwePhone('263771234567'), '+263771234567');
    expect(normalizeZimbabwePhone('+263771234567'), '+263771234567');
  });

  test('rejects foreign, landline, and malformed numbers', () {
    expect(validateZimbabwePhone('+264811234567'), isNotNull);
    expect(validateZimbabwePhone('0242123456'), isNotNull);
    expect(validateZimbabwePhone('077123'), isNotNull);
  });

  test('allows the optional phone field to remain empty', () {
    expect(validateZimbabwePhone(''), isNull);
  });
}
