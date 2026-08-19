import 'dart:io';

import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';

import '../models/crop.dart';
import '../services/api_client.dart';
import 'diagnosis_result_screen.dart';

class DiagnosisCaptureScreen extends StatefulWidget {
  const DiagnosisCaptureScreen({
    required this.apiClient,
    this.showAppBar = true,
    this.pickImage,
    super.key,
  });

  final ApiClient apiClient;
  final bool showAppBar;
  final Future<XFile?> Function(ImageSource source)? pickImage;

  @override
  State<DiagnosisCaptureScreen> createState() => _DiagnosisCaptureScreenState();
}

class _DiagnosisCaptureScreenState extends State<DiagnosisCaptureScreen> {
  final ImagePicker _picker = ImagePicker();
  late Future<List<Crop>> _crops;
  Crop? _selectedCrop;
  File? _selectedImage;
  String? _message;
  bool _isLeafDetectionError = false;
  bool _isUploading = false;

  @override
  void initState() {
    super.initState();
    _crops = _loadCrops();
  }

  Future<List<Crop>> _loadCrops() async {
    final result = await widget.apiClient.crops();

    if (!result.isSuccess) {
      throw Exception(result.errorMessage ?? 'Unable to load crops.');
    }

    return result.data ?? [];
  }

  Future<void> _pickImage(ImageSource source) async {
    final pickedImage = widget.pickImage == null
        ? await _picker.pickImage(
            source: source,
            maxWidth: 1600,
            maxHeight: 1600,
            imageQuality: 88,
          )
        : await widget.pickImage!(source);

    if (pickedImage == null) {
      return;
    }

    final image = File(pickedImage.path);
    final size = await image.length();
    final extension = pickedImage.path.split('.').last.toLowerCase();

    if (!['jpg', 'jpeg', 'png', 'webp'].contains(extension)) {
      setState(() {
        _message = 'Choose a JPG, PNG, or WEBP image.';
        _isLeafDetectionError = false;
      });
      return;
    }

    if (size > 5 * 1024 * 1024) {
      setState(() {
        _message = 'Choose an image smaller than 5 MB.';
        _isLeafDetectionError = false;
      });
      return;
    }

    setState(() {
      _selectedImage = image;
      _message = null;
      _isLeafDetectionError = false;
    });
  }

  Future<void> _submit() async {
    final image = _selectedImage;

    if (image == null) {
      setState(() {
        _message = 'Add a clear crop leaf photo first.';
        _isLeafDetectionError = false;
      });
      return;
    }

    setState(() {
      _isUploading = true;
      _message = null;
      _isLeafDetectionError = false;
    });

    final result = await widget.apiClient.storeDiagnosis(
      image: image,
      cropId: _selectedCrop?.id,
    );

    if (!mounted) {
      return;
    }

    setState(() {
      _isUploading = false;
    });

    if (!result.isSuccess || result.data == null) {
      setState(() {
        _message = result.errorMessage ?? 'Diagnosis failed. Please try again.';
        _isLeafDetectionError = result.errorCode == 'crop_leaf_not_detected';
      });
      return;
    }

    await Navigator.of(context).push(
      MaterialPageRoute(
        builder: (_) => DiagnosisResultScreen(
          diagnosis: result.data!,
          languagePreference:
              widget.apiClient.currentSession?.languagePreference ?? 'en',
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: widget.showAppBar
          ? AppBar(title: const Text('Start diagnosis'))
          : null,
      body: SafeArea(
        child: FutureBuilder<List<Crop>>(
          future: _crops,
          builder: (context, snapshot) {
            if (snapshot.connectionState != ConnectionState.done) {
              return const Center(child: CircularProgressIndicator());
            }

            final crops = snapshot.data ?? [];

            return ListView(
              padding: const EdgeInsets.fromLTRB(20, 12, 20, 28),
              children: [
                _ImagePreview(image: _selectedImage),
                const SizedBox(height: 14),
                Row(
                  children: [
                    Expanded(
                      child: OutlinedButton.icon(
                        onPressed: _isUploading
                            ? null
                            : () => _pickImage(ImageSource.camera),
                        icon: const Icon(Icons.photo_camera_outlined),
                        label: const Text('Camera'),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: OutlinedButton.icon(
                        onPressed: _isUploading
                            ? null
                            : () => _pickImage(ImageSource.gallery),
                        icon: const Icon(Icons.photo_library_outlined),
                        label: const Text('Gallery'),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 18),
                if (snapshot.hasError)
                  _InlineMessage(
                    icon: Icons.cloud_off_outlined,
                    message: snapshot.error.toString(),
                  )
                else if (crops.isNotEmpty)
                  DropdownButtonFormField<Crop>(
                    initialValue: _selectedCrop,
                    decoration: const InputDecoration(
                      labelText: 'Crop',
                      border: OutlineInputBorder(),
                    ),
                    items: [
                      for (final crop in crops)
                        DropdownMenuItem(value: crop, child: Text(crop.name)),
                    ],
                    onChanged: _isUploading
                        ? null
                        : (crop) {
                            setState(() {
                              _selectedCrop = crop;
                            });
                          },
                  ),
                if (_message != null) ...[
                  const SizedBox(height: 14),
                  _InlineMessage(
                    icon: Icons.error_outline,
                    message: _message!,
                    isError: true,
                  ),
                  if (_isLeafDetectionError) ...[
                    const SizedBox(height: 10),
                    OutlinedButton.icon(
                      onPressed: _isUploading
                          ? null
                          : () => _pickImage(ImageSource.camera),
                      icon: const Icon(Icons.refresh_outlined),
                      label: const Text('Retake photo'),
                    ),
                  ],
                ],
                const SizedBox(height: 18),
                FilledButton.icon(
                  onPressed: _isUploading ? null : _submit,
                  icon: _isUploading
                      ? const SizedBox.square(
                          dimension: 18,
                          child: CircularProgressIndicator(strokeWidth: 2),
                        )
                      : const Icon(Icons.search_outlined),
                  label: Text(
                    _isUploading ? 'Uploading image' : 'Run diagnosis',
                  ),
                ),
                const SizedBox(height: 14),
                Text(
                  'Predictions support field decisions but do not replace advice from an agricultural professional.',
                  textAlign: TextAlign.center,
                  style: Theme.of(context).textTheme.bodyMedium,
                ),
              ],
            );
          },
        ),
      ),
    );
  }
}

class _ImagePreview extends StatelessWidget {
  const _ImagePreview({required this.image});

  final File? image;

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    return AspectRatio(
      aspectRatio: 4 / 3,
      child: Container(
        decoration: BoxDecoration(
          color: colorScheme.primaryContainer,
          borderRadius: BorderRadius.circular(8),
          border: Border.all(color: colorScheme.outlineVariant),
        ),
        clipBehavior: Clip.antiAlias,
        child: image == null
            ? Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    Icons.eco_outlined,
                    color: colorScheme.primary,
                    size: 48,
                  ),
                  const SizedBox(height: 12),
                  Text(
                    'Add a clear leaf photo',
                    style: TextStyle(
                      color: colorScheme.primary,
                      fontWeight: FontWeight.w800,
                    ),
                  ),
                ],
              )
            : Image.file(image!, fit: BoxFit.cover),
      ),
    );
  }
}

class _InlineMessage extends StatelessWidget {
  const _InlineMessage({
    required this.icon,
    required this.message,
    this.isError = false,
  });

  final IconData icon;
  final String message;
  final bool isError;

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: isError
            ? colorScheme.errorContainer
            : colorScheme.primaryContainer,
        borderRadius: BorderRadius.circular(8),
        border: Border.all(
          color: isError ? colorScheme.error : colorScheme.outlineVariant,
        ),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: isError ? colorScheme.error : colorScheme.primary),
          const SizedBox(width: 10),
          Expanded(
            child: Text(
              message,
              style: TextStyle(
                color: isError ? colorScheme.onErrorContainer : null,
                fontWeight: isError ? FontWeight.w700 : null,
              ),
            ),
          ),
        ],
      ),
    );
  }
}
