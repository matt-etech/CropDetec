String? normalizeZimbabwePhone(String? value) {
  final compact = (value ?? '').trim().replaceAll(RegExp(r'[\s()\-]'), '');
  if (compact.isEmpty) {
    return null;
  }
  if (compact.startsWith('0')) {
    return '+263${compact.substring(1)}';
  }
  if (compact.startsWith('263')) {
    return '+$compact';
  }
  return compact;
}

String? validateZimbabwePhone(String? value) {
  final normalized = normalizeZimbabwePhone(value);
  if (normalized == null) {
    return null;
  }
  if (!RegExp(r'^\+2637[1378]\d{7}$').hasMatch(normalized)) {
    return 'Enter a valid Zimbabwe mobile number, for example 0771234567.';
  }
  return null;
}
