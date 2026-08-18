import 'disease.dart';

class Crop {
  const Crop({
    required this.id,
    required this.name,
    this.scientificName,
    this.description,
    this.diseases = const [],
  });

  final int id;
  final String name;
  final String? scientificName;
  final String? description;
  final List<Disease> diseases;

  factory Crop.fromJson(Map<String, dynamic> json) {
    final diseasesJson = json['diseases'] as List<dynamic>? ?? [];

    return Crop(
      id: json['id'] as int,
      name: json['name'] as String,
      scientificName: json['scientific_name'] as String?,
      description: json['description'] as String?,
      diseases: diseasesJson
          .map((disease) => Disease.fromJson(disease as Map<String, dynamic>))
          .toList(),
    );
  }
}
