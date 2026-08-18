import 'crop.dart';
import 'disease.dart';

class Diagnosis {
  const Diagnosis({
    required this.id,
    required this.predictedLabel,
    required this.confidence,
    required this.status,
    required this.createdAt,
    this.imageUrl,
    this.crop,
    this.disease,
    this.recommendationSnapshot,
  });

  final int id;
  final String predictedLabel;
  final double confidence;
  final String status;
  final DateTime createdAt;
  final String? imageUrl;
  final Crop? crop;
  final Disease? disease;
  final String? recommendationSnapshot;

  factory Diagnosis.fromJson(Map<String, dynamic> json) {
    return Diagnosis(
      id: json['id'] as int,
      predictedLabel: json['predicted_label'] as String? ?? 'unknown',
      confidence: double.tryParse(json['confidence'].toString()) ?? 0,
      status: json['status'] as String? ?? 'completed',
      imageUrl: json['image_url'] as String?,
      createdAt: DateTime.tryParse(json['created_at'] as String? ?? '') ??
          DateTime.fromMillisecondsSinceEpoch(0),
      crop: json['crop'] == null
          ? null
          : Crop.fromJson(json['crop'] as Map<String, dynamic>),
      disease: json['disease'] == null
          ? null
          : Disease.fromJson(json['disease'] as Map<String, dynamic>),
      recommendationSnapshot: json['recommendation_snapshot'] as String?,
    );
  }
}
