import 'package:flutter/material.dart';

import '../models/diagnosis.dart';
import '../services/api_client.dart';
import 'diagnosis_result_screen.dart';

class DiagnosisHistoryScreen extends StatefulWidget {
  const DiagnosisHistoryScreen({
    required this.apiClient,
    this.showAppBar = true,
    super.key,
  });

  final ApiClient apiClient;
  final bool showAppBar;

  @override
  State<DiagnosisHistoryScreen> createState() => _DiagnosisHistoryScreenState();
}

class _DiagnosisHistoryScreenState extends State<DiagnosisHistoryScreen> {
  late Future<List<Diagnosis>> _diagnoses;
  String _cropFilter = 'all';
  String _diseaseFilter = 'all';
  String _dateFilter = 'all';

  @override
  void initState() {
    super.initState();
    _diagnoses = _loadDiagnoses();
  }

  Future<List<Diagnosis>> _loadDiagnoses() async {
    final result = await widget.apiClient.diagnoses();

    if (!result.isSuccess) {
      throw Exception(
        result.errorMessage ?? 'Unable to load diagnosis history.',
      );
    }

    return result.data ?? [];
  }

  List<Diagnosis> _filtered(List<Diagnosis> diagnoses) {
    final now = DateTime.now();

    return diagnoses.where((diagnosis) {
      final cropName = diagnosis.crop?.name ?? 'Unknown crop';
      final diseaseName = diagnosis.disease?.name ?? diagnosis.predictedLabel;
      final matchesCrop = _cropFilter == 'all' || cropName == _cropFilter;
      final matchesDisease =
          _diseaseFilter == 'all' || diseaseName == _diseaseFilter;
      final matchesDate = switch (_dateFilter) {
        'today' =>
          diagnosis.createdAt.year == now.year &&
              diagnosis.createdAt.month == now.month &&
              diagnosis.createdAt.day == now.day,
        'week' => now.difference(diagnosis.createdAt).inDays <= 7,
        _ => true,
      };

      return matchesCrop && matchesDisease && matchesDate;
    }).toList();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: widget.showAppBar
          ? AppBar(title: const Text('Diagnosis history'))
          : null,
      body: FutureBuilder<List<Diagnosis>>(
        future: _diagnoses,
        builder: (context, snapshot) {
          if (snapshot.connectionState != ConnectionState.done) {
            return const Center(child: CircularProgressIndicator());
          }

          if (snapshot.hasError) {
            return _HistoryState(
              title: 'Could not load history',
              message: snapshot.error.toString(),
            );
          }

          final diagnoses = snapshot.data ?? [];

          if (diagnoses.isEmpty) {
            return const _HistoryState(
              title: 'No diagnoses yet',
              message: 'Captured crop diagnoses will appear here.',
            );
          }

          final filteredDiagnoses = _filtered(diagnoses);

          return ListView.separated(
            padding: const EdgeInsets.all(20),
            itemCount: filteredDiagnoses.isEmpty
                ? 2
                : filteredDiagnoses.length + 1,
            separatorBuilder: (_, _) => const SizedBox(height: 12),
            itemBuilder: (context, index) {
              if (index == 0) {
                return _HistoryFilters(
                  diagnoses: diagnoses,
                  cropFilter: _cropFilter,
                  diseaseFilter: _diseaseFilter,
                  dateFilter: _dateFilter,
                  onCropChanged: (value) => setState(() => _cropFilter = value),
                  onDiseaseChanged: (value) =>
                      setState(() => _diseaseFilter = value),
                  onDateChanged: (value) => setState(() => _dateFilter = value),
                );
              }

              if (filteredDiagnoses.isEmpty) {
                return const _HistoryState(
                  title: 'No matching diagnoses',
                  message: 'Try a different crop, disease, or date filter.',
                );
              }

              return _DiagnosisCard(
                diagnosis: filteredDiagnoses[index - 1],
                languagePreference:
                    widget.apiClient.currentSession?.languagePreference ?? 'en',
              );
            },
          );
        },
      ),
    );
  }
}

class _HistoryFilters extends StatelessWidget {
  const _HistoryFilters({
    required this.diagnoses,
    required this.cropFilter,
    required this.diseaseFilter,
    required this.dateFilter,
    required this.onCropChanged,
    required this.onDiseaseChanged,
    required this.onDateChanged,
  });

  final List<Diagnosis> diagnoses;
  final String cropFilter;
  final String diseaseFilter;
  final String dateFilter;
  final ValueChanged<String> onCropChanged;
  final ValueChanged<String> onDiseaseChanged;
  final ValueChanged<String> onDateChanged;

  @override
  Widget build(BuildContext context) {
    final crops = {
      for (final diagnosis in diagnoses) diagnosis.crop?.name ?? 'Unknown crop',
    }.toList()..sort();
    final diseases = {
      for (final diagnosis in diagnoses)
        diagnosis.disease?.name ?? diagnosis.predictedLabel,
    }.toList()..sort();

    return Card(
      child: Padding(
        padding: const EdgeInsets.all(14),
        child: Column(
          children: [
            _FilterDropdown(
              label: 'Crop',
              value: cropFilter,
              values: crops,
              onChanged: onCropChanged,
            ),
            const SizedBox(height: 10),
            _FilterDropdown(
              label: 'Disease',
              value: diseaseFilter,
              values: diseases,
              onChanged: onDiseaseChanged,
            ),
            const SizedBox(height: 10),
            DropdownButtonFormField<String>(
              key: ValueKey(dateFilter),
              initialValue: dateFilter,
              decoration: const InputDecoration(
                labelText: 'Date',
                border: OutlineInputBorder(),
              ),
              items: const [
                DropdownMenuItem(value: 'all', child: Text('All dates')),
                DropdownMenuItem(value: 'today', child: Text('Today')),
                DropdownMenuItem(value: 'week', child: Text('Last 7 days')),
              ],
              onChanged: (value) => onDateChanged(value ?? 'all'),
            ),
          ],
        ),
      ),
    );
  }
}

class _FilterDropdown extends StatelessWidget {
  const _FilterDropdown({
    required this.label,
    required this.value,
    required this.values,
    required this.onChanged,
  });

  final String label;
  final String value;
  final List<String> values;
  final ValueChanged<String> onChanged;

  @override
  Widget build(BuildContext context) {
    return DropdownButtonFormField<String>(
      key: ValueKey('$label:$value'),
      initialValue: value,
      decoration: InputDecoration(
        labelText: label,
        border: const OutlineInputBorder(),
      ),
      items: [
        DropdownMenuItem(
          value: 'all',
          child: Text('All ${label.toLowerCase()}s'),
        ),
        for (final value in values)
          DropdownMenuItem(value: value, child: Text(value)),
      ],
      onChanged: (value) => onChanged(value ?? 'all'),
    );
  }
}

class _DiagnosisCard extends StatelessWidget {
  const _DiagnosisCard({
    required this.diagnosis,
    required this.languagePreference,
  });

  final Diagnosis diagnosis;
  final String languagePreference;

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    final cropName = diagnosis.crop?.name ?? 'Unknown crop';
    final diseaseName = diagnosis.disease?.name ?? diagnosis.predictedLabel;
    final date =
        '${diagnosis.createdAt.year}-'
        '${diagnosis.createdAt.month.toString().padLeft(2, '0')}-'
        '${diagnosis.createdAt.day.toString().padLeft(2, '0')}';

    return Card(
      child: InkWell(
        borderRadius: BorderRadius.circular(8),
        onTap: () {
          Navigator.of(context).push(
            MaterialPageRoute(
              builder: (_) => DiagnosisResultScreen(
                diagnosis: diagnosis,
                languagePreference: languagePreference,
              ),
            ),
          );
        },
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              ClipRRect(
                borderRadius: BorderRadius.circular(8),
                child: SizedBox(
                  height: 72,
                  width: 72,
                  child: diagnosis.imageUrl == null
                      ? Container(
                          color: colorScheme.primaryContainer,
                          child: Icon(
                            Icons.image_outlined,
                            color: colorScheme.primary,
                          ),
                        )
                      : Image.network(
                          diagnosis.imageUrl!,
                          fit: BoxFit.cover,
                          errorBuilder: (_, _, _) => Container(
                            color: colorScheme.primaryContainer,
                            child: Icon(
                              Icons.image_outlined,
                              color: colorScheme.primary,
                            ),
                          ),
                        ),
                ),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      diseaseName,
                      style: Theme.of(context).textTheme.titleLarge,
                    ),
                    const SizedBox(height: 6),
                    Text(
                      cropName,
                      style: TextStyle(color: colorScheme.primary),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      'Confidence: ${diagnosis.confidence.toStringAsFixed(1)}% - $date',
                      style: Theme.of(context).textTheme.bodyLarge,
                    ),
                    if (diagnosis.recommendationSnapshot != null) ...[
                      const SizedBox(height: 10),
                      Text(
                        diagnosis.recommendationSnapshot!,
                        maxLines: 4,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ],
                  ],
                ),
              ),
              Icon(Icons.chevron_right, color: colorScheme.onSurfaceVariant),
            ],
          ),
        ),
      ),
    );
  }
}

class _HistoryState extends StatelessWidget {
  const _HistoryState({required this.title, required this.message});

  final String title;
  final String message;

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    return Center(
      child: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(Icons.history_outlined, color: colorScheme.primary, size: 44),
            const SizedBox(height: 12),
            Text(title, style: Theme.of(context).textTheme.titleLarge),
            const SizedBox(height: 8),
            Text(
              message,
              textAlign: TextAlign.center,
              style: Theme.of(context).textTheme.bodyLarge,
            ),
          ],
        ),
      ),
    );
  }
}
