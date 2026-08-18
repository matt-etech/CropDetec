import 'treatment.dart';

class Disease {
  const Disease({
    required this.id,
    required this.name,
    required this.classLabel,
    this.description,
    this.symptoms,
    this.prevention,
    this.treatments = const [],
  });

  final int id;
  final String name;
  final String classLabel;
  final String? description;
  final String? symptoms;
  final String? prevention;
  final List<Treatment> treatments;

  factory Disease.fromJson(Map<String, dynamic> json) {
    final treatmentsJson = json['treatments'] as List<dynamic>? ?? [];

    return Disease(
      id: json['id'] as int,
      name: json['name'] as String,
      classLabel: json['class_label'] as String,
      description: json['description'] as String?,
      symptoms: json['symptoms'] as String?,
      prevention: json['prevention'] as String?,
      treatments: treatmentsJson
          .map((treatment) => Treatment.fromJson(treatment as Map<String, dynamic>))
          .toList(),
    );
  }
}
