import 'package:flutter/material.dart';

class PhaseCard extends StatelessWidget {
  const PhaseCard({
    required this.title,
    required this.description,
    super.key,
  });

  final String title;
  final String description;

  @override
  Widget build(BuildContext context) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Icon(Icons.check_circle_outline, color: Color(0xFF15803D)),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(title, style: Theme.of(context).textTheme.titleLarge),
                  const SizedBox(height: 6),
                  Text(description, style: Theme.of(context).textTheme.bodyLarge),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
