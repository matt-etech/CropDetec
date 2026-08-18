class Treatment {
  const Treatment({
    required this.id,
    required this.title,
    required this.instructions,
    required this.type,
  });

  final int id;
  final String title;
  final String instructions;
  final String type;

  factory Treatment.fromJson(Map<String, dynamic> json) {
    return Treatment(
      id: json['id'] as int,
      title: json['title'] as String,
      instructions: json['instructions'] as String,
      type: json['type'] as String? ?? 'general',
    );
  }
}
