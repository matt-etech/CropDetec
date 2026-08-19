import 'package:flutter/material.dart';

import '../localization/app_strings.dart';
import '../models/diagnosis.dart';
import '../services/text_to_speech_service.dart';

class DiagnosisResultScreen extends StatefulWidget {
  const DiagnosisResultScreen({
    required this.diagnosis,
    this.languagePreference = 'en',
    super.key,
  });

  final Diagnosis diagnosis;
  final String languagePreference;

  @override
  State<DiagnosisResultScreen> createState() => _DiagnosisResultScreenState();
}

class _DiagnosisResultScreenState extends State<DiagnosisResultScreen> {
  final _textToSpeech = const TextToSpeechService();
  bool _isSpeaking = false;

  Future<void> _speakResult() async {
    final diagnosis = widget.diagnosis;
    final strings = AppStrings.forLanguage(widget.languagePreference);
    final cropName = diagnosis.crop?.name ?? 'Unknown crop';
    final diseaseName = diagnosis.disease?.name ?? diagnosis.predictedLabel;
    final text = [
      '${strings.diagnosisResult}: $diseaseName.',
      'Crop: $cropName.',
      '${strings.confidence}: ${diagnosis.confidence.toStringAsFixed(1)} percent.',
      if (diagnosis.recommendationSnapshot != null)
        '${strings.recommendation}: ${diagnosis.recommendationSnapshot!}',
      strings.disclaimer,
    ].join(' ');

    setState(() {
      _isSpeaking = true;
    });

    final speechResult = await _textToSpeech.speak(
      text: text,
      languageCode: widget.languagePreference,
    );

    if (!mounted) {
      return;
    }

    if (!speechResult.success || speechResult.fallbackUsed) {
      final message = speechResult.fallbackUsed
          ? strings.shonaSpeechFallback
          : speechResult.message.isEmpty
          ? strings.speechFailed
          : speechResult.message;
      ScaffoldMessenger.of(
        context,
      ).showSnackBar(SnackBar(content: Text(message)));
    }

    if (!speechResult.success) {
      setState(() {
        _isSpeaking = false;
      });
    }
  }

  Future<void> _stopSpeaking() async {
    await _textToSpeech.stop();
    if (!mounted) {
      return;
    }
    setState(() {
      _isSpeaking = false;
    });
  }

  @override
  void dispose() {
    _textToSpeech.stop();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    final diagnosis = widget.diagnosis;
    final strings = AppStrings.forLanguage(widget.languagePreference);
    final cropName = diagnosis.crop?.name ?? 'Unknown crop';
    final diseaseName = diagnosis.disease?.name ?? diagnosis.predictedLabel;

    return Scaffold(
      appBar: AppBar(
        leading: const BackButton(),
        title: Text(strings.diagnosisResult),
      ),
      body: SafeArea(
        child: ListView(
          padding: const EdgeInsets.fromLTRB(20, 12, 20, 28),
          children: [
            if (diagnosis.imageUrl != null) ...[
              AspectRatio(
                aspectRatio: 4 / 3,
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(8),
                  child: Image.network(
                    diagnosis.imageUrl!,
                    fit: BoxFit.cover,
                    errorBuilder: (_, _, _) => Container(
                      color: colorScheme.primaryContainer,
                      child: Icon(
                        Icons.image_outlined,
                        color: colorScheme.primary,
                        size: 48,
                      ),
                    ),
                  ),
                ),
              ),
              const SizedBox(height: 18),
            ],
            _ResultHeader(
              diseaseName: diseaseName,
              cropName: cropName,
              confidence: diagnosis.confidence,
              confidenceLabel: strings.confidence,
            ),
            const SizedBox(height: 14),
            OutlinedButton.icon(
              onPressed: _isSpeaking ? _stopSpeaking : _speakResult,
              icon: Icon(
                _isSpeaking
                    ? Icons.stop_circle_outlined
                    : Icons.record_voice_over_outlined,
              ),
              label: Text(
                _isSpeaking ? strings.stopVoice : strings.speakResult,
              ),
            ),
            const SizedBox(height: 14),
            if (diagnosis.confidence < 60)
              _GuidanceBox(
                icon: Icons.warning_amber_outlined,
                title: strings.lowConfidence,
                message: strings.retakePhoto,
              ),
            if (diagnosis.recommendationSnapshot != null) ...[
              const SizedBox(height: 14),
              _GuidanceBox(
                icon: Icons.medical_services_outlined,
                title: strings.recommendation,
                message: diagnosis.recommendationSnapshot!,
              ),
            ],
            const SizedBox(height: 14),
            _GuidanceBox(
              icon: Icons.verified_user_outlined,
              title: strings.fieldAdvice,
              message: strings.disclaimer,
            ),
          ],
        ),
      ),
    );
  }
}

class _ResultHeader extends StatelessWidget {
  const _ResultHeader({
    required this.diseaseName,
    required this.cropName,
    required this.confidence,
    required this.confidenceLabel,
  });

  final String diseaseName;
  final String cropName;
  final double confidence;
  final String confidenceLabel;

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    return Card(
      child: Padding(
        padding: const EdgeInsets.all(18),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(diseaseName, style: Theme.of(context).textTheme.headlineLarge),
            const SizedBox(height: 8),
            Text(cropName, style: Theme.of(context).textTheme.titleLarge),
            const SizedBox(height: 12),
            ClipRRect(
              borderRadius: BorderRadius.circular(8),
              child: LinearProgressIndicator(
                value: (confidence / 100).clamp(0, 1).toDouble(),
                minHeight: 10,
                backgroundColor: colorScheme.outlineVariant,
                color: colorScheme.primary,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              '$confidenceLabel: ${confidence.toStringAsFixed(1)}%',
              style: Theme.of(context).textTheme.bodyLarge,
            ),
          ],
        ),
      ),
    );
  }
}

class _GuidanceBox extends StatelessWidget {
  const _GuidanceBox({
    required this.icon,
    required this.title,
    required this.message,
  });

  final IconData icon;
  final String title;
  final String message;

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(icon, color: colorScheme.primary),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(title, style: Theme.of(context).textTheme.titleLarge),
                  const SizedBox(height: 8),
                  Text(message, style: Theme.of(context).textTheme.bodyLarge),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
