import 'package:flutter/material.dart';

import '../models/crop.dart';
import '../services/api_client.dart';

class CropLibraryScreen extends StatefulWidget {
  const CropLibraryScreen({
    required this.apiClient,
    this.showAppBar = true,
    super.key,
  });

  final ApiClient apiClient;
  final bool showAppBar;

  @override
  State<CropLibraryScreen> createState() => _CropLibraryScreenState();
}

class _CropLibraryScreenState extends State<CropLibraryScreen> {
  late Future<List<Crop>> _crops;

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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: widget.showAppBar ? AppBar(title: const Text('Crop library')) : null,
      body: FutureBuilder<List<Crop>>(
        future: _crops,
        builder: (context, snapshot) {
          if (snapshot.connectionState != ConnectionState.done) {
            return const Center(child: CircularProgressIndicator());
          }

          if (snapshot.hasError) {
            return _StateMessage(
              icon: Icons.cloud_off_outlined,
              title: 'Could not load crops',
              message: snapshot.error.toString(),
            );
          }

          final crops = snapshot.data ?? [];

          if (crops.isEmpty) {
            return const _StateMessage(
              icon: Icons.eco_outlined,
              title: 'No crops yet',
              message: 'Seed crop and disease records from the backend first.',
            );
          }

          return LayoutBuilder(
            builder: (context, constraints) {
              final columns = constraints.maxWidth >= 680 ? 2 : 1;

              return GridView.builder(
                padding: const EdgeInsets.all(20),
                gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                  crossAxisCount: columns,
                  crossAxisSpacing: 14,
                  mainAxisSpacing: 14,
                  mainAxisExtent: 390,
                ),
                itemCount: crops.length,
                itemBuilder: (context, index) => _CropCard(crop: crops[index]),
              );
            },
          );
        },
      ),
    );
  }
}

class _CropCard extends StatelessWidget {
  const _CropCard({required this.crop});

  final Crop crop;

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;
    final artwork = _CropArtworkData.forCrop(crop.name);

    return Card(
      clipBehavior: Clip.antiAlias,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _CropImagePanel(
            cropName: crop.name,
            artwork: artwork,
          ),
          Expanded(
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(crop.name, style: Theme.of(context).textTheme.titleLarge),
                  if (crop.scientificName != null) ...[
                    const SizedBox(height: 4),
                    Text(
                      crop.scientificName!,
                      style: TextStyle(
                        color: colorScheme.primary,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ],
                  if (crop.description != null) ...[
                    const SizedBox(height: 8),
                    Text(
                      crop.description!,
                      maxLines: 3,
                      overflow: TextOverflow.ellipsis,
                      style: Theme.of(context).textTheme.bodyLarge,
                    ),
                  ],
                  const Spacer(),
                  if (crop.diseases.isNotEmpty) ...[
                    const SizedBox(height: 14),
                    Wrap(
                      spacing: 8,
                      runSpacing: 8,
                      children: [
                        for (final disease in crop.diseases.take(3))
                          Chip(
                            label: Text(disease.name),
                            backgroundColor: colorScheme.primaryContainer,
                            side: BorderSide(color: colorScheme.outlineVariant),
                          ),
                        if (crop.diseases.length > 3)
                          Chip(
                            label: Text('+${crop.diseases.length - 3} more'),
                            backgroundColor: colorScheme.surface,
                            side: BorderSide(color: colorScheme.outlineVariant),
                          ),
                      ],
                    ),
                  ],
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _CropImagePanel extends StatelessWidget {
  const _CropImagePanel({
    required this.cropName,
    required this.artwork,
  });

  final String cropName;
  final _CropArtworkData artwork;

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    return Semantics(
      image: true,
      label: '$cropName crop picture',
      child: SizedBox(
        height: 150,
        width: double.infinity,
        child: CustomPaint(
          painter: _CropArtworkPainter(
            artwork: artwork,
            colorScheme: colorScheme,
          ),
          child: Align(
            alignment: Alignment.bottomLeft,
            child: Container(
              margin: const EdgeInsets.all(12),
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
              decoration: BoxDecoration(
                color: colorScheme.surface.withAlpha(220),
                borderRadius: BorderRadius.circular(8),
                border: Border.all(color: colorScheme.outlineVariant),
              ),
              child: Text(
                artwork.label,
                style: TextStyle(
                  color: colorScheme.primary,
                  fontSize: 12,
                  fontWeight: FontWeight.w900,
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}

enum _CropArtworkKind {
  maize,
  tomato,
  potato,
  pepper,
  bean,
  soybean,
  squash,
  leaf,
}

class _CropArtworkData {
  const _CropArtworkData({
    required this.kind,
    required this.label,
    required this.primary,
    required this.secondary,
  });

  final _CropArtworkKind kind;
  final String label;
  final Color primary;
  final Color secondary;

  static _CropArtworkData forCrop(String name) {
    final normalized = name.toLowerCase();

    if (normalized.contains('maize') || normalized.contains('corn')) {
      return const _CropArtworkData(
        kind: _CropArtworkKind.maize,
        label: 'Cereal crop',
        primary: Color(0xFFFACC15),
        secondary: Color(0xFF15803D),
      );
    }

    if (normalized.contains('tomato')) {
      return const _CropArtworkData(
        kind: _CropArtworkKind.tomato,
        label: 'Fruit vegetable',
        primary: Color(0xFFDC2626),
        secondary: Color(0xFF16A34A),
      );
    }

    if (normalized.contains('potato')) {
      return const _CropArtworkData(
        kind: _CropArtworkKind.potato,
        label: 'Root crop',
        primary: Color(0xFFD6A15E),
        secondary: Color(0xFF4D7C0F),
      );
    }

    if (normalized.contains('pepper')) {
      return const _CropArtworkData(
        kind: _CropArtworkKind.pepper,
        label: 'Vegetable crop',
        primary: Color(0xFFEF4444),
        secondary: Color(0xFF15803D),
      );
    }

    if (normalized.contains('bean')) {
      return const _CropArtworkData(
        kind: _CropArtworkKind.bean,
        label: 'Legume crop',
        primary: Color(0xFFA16207),
        secondary: Color(0xFF16A34A),
      );
    }

    if (normalized.contains('soy')) {
      return const _CropArtworkData(
        kind: _CropArtworkKind.soybean,
        label: 'Oilseed crop',
        primary: Color(0xFF84CC16),
        secondary: Color(0xFF15803D),
      );
    }

    if (normalized.contains('squash') ||
        normalized.contains('pumpkin') ||
        normalized.contains('cucumber')) {
      return const _CropArtworkData(
        kind: _CropArtworkKind.squash,
        label: 'Cucurbit crop',
        primary: Color(0xFFF97316),
        secondary: Color(0xFF16A34A),
      );
    }

    return const _CropArtworkData(
      kind: _CropArtworkKind.leaf,
      label: 'Supported crop',
      primary: Color(0xFF22C55E),
      secondary: Color(0xFF15803D),
    );
  }
}

class _CropArtworkPainter extends CustomPainter {
  const _CropArtworkPainter({
    required this.artwork,
    required this.colorScheme,
  });

  final _CropArtworkData artwork;
  final ColorScheme colorScheme;

  @override
  void paint(Canvas canvas, Size size) {
    final background = Paint()
      ..shader = LinearGradient(
        begin: Alignment.topLeft,
        end: Alignment.bottomRight,
        colors: [
          Color.alphaBlend(artwork.secondary.withAlpha(28), colorScheme.surface),
          Color.alphaBlend(artwork.primary.withAlpha(34), colorScheme.surface),
        ],
      ).createShader(Offset.zero & size);
    canvas.drawRect(Offset.zero & size, background);

    final gridPaint = Paint()
      ..color = colorScheme.onSurface.withAlpha(12)
      ..strokeWidth = 1;
    for (var x = 0.0; x <= size.width; x += 28) {
      canvas.drawLine(Offset(x, 0), Offset(x, size.height), gridPaint);
    }
    for (var y = 0.0; y <= size.height; y += 28) {
      canvas.drawLine(Offset(0, y), Offset(size.width, y), gridPaint);
    }

    switch (artwork.kind) {
      case _CropArtworkKind.maize:
        _drawMaize(canvas, size);
        break;
      case _CropArtworkKind.tomato:
        _drawTomato(canvas, size);
        break;
      case _CropArtworkKind.potato:
        _drawPotato(canvas, size);
        break;
      case _CropArtworkKind.pepper:
        _drawPepper(canvas, size);
        break;
      case _CropArtworkKind.bean:
      case _CropArtworkKind.soybean:
        _drawBeans(canvas, size);
        break;
      case _CropArtworkKind.squash:
        _drawSquash(canvas, size);
        break;
      case _CropArtworkKind.leaf:
        _drawLeaf(canvas, size);
        break;
    }
  }

  void _drawStem(Canvas canvas, Size size) {
    final stemPaint = Paint()
      ..color = artwork.secondary
      ..strokeWidth = 8
      ..strokeCap = StrokeCap.round;
    canvas.drawLine(
      Offset(size.width * .50, size.height * .88),
      Offset(size.width * .50, size.height * .24),
      stemPaint,
    );
  }

  void _drawLeafShape(
    Canvas canvas,
    Offset center,
    Size leafSize,
    double rotation,
  ) {
    final path = Path()
      ..moveTo(0, -leafSize.height / 2)
      ..cubicTo(
        leafSize.width / 2,
        -leafSize.height / 4,
        leafSize.width / 2,
        leafSize.height / 4,
        0,
        leafSize.height / 2,
      )
      ..cubicTo(
        -leafSize.width / 2,
        leafSize.height / 4,
        -leafSize.width / 2,
        -leafSize.height / 4,
        0,
        -leafSize.height / 2,
      );

    canvas
      ..save()
      ..translate(center.dx, center.dy)
      ..rotate(rotation)
      ..drawPath(path, Paint()..color = artwork.secondary.withAlpha(225))
      ..restore();
  }

  void _drawMaize(Canvas canvas, Size size) {
    _drawStem(canvas, size);
    _drawLeafShape(canvas, Offset(size.width * .36, size.height * .56), const Size(46, 88), -0.8);
    _drawLeafShape(canvas, Offset(size.width * .65, size.height * .54), const Size(46, 88), 0.8);

    final cobPaint = Paint()..color = artwork.primary;
    final huskPaint = Paint()..color = artwork.secondary.withAlpha(230);
    final cob = RRect.fromRectAndRadius(
      Rect.fromCenter(
        center: Offset(size.width * .50, size.height * .46),
        width: 42,
        height: 86,
      ),
      const Radius.circular(24),
    );
    canvas.drawRRect(cob, cobPaint);
    canvas.drawOval(
      Rect.fromCenter(
        center: Offset(size.width * .39, size.height * .51),
        width: 24,
        height: 70,
      ),
      huskPaint,
    );
    canvas.drawOval(
      Rect.fromCenter(
        center: Offset(size.width * .61, size.height * .51),
        width: 24,
        height: 70,
      ),
      huskPaint,
    );
  }

  void _drawTomato(Canvas canvas, Size size) {
    _drawStem(canvas, size);
    _drawLeafShape(canvas, Offset(size.width * .40, size.height * .43), const Size(36, 64), -0.9);
    _drawLeafShape(canvas, Offset(size.width * .60, size.height * .43), const Size(36, 64), 0.9);
    final fruitPaint = Paint()..color = artwork.primary;
    for (final center in [
      Offset(size.width * .42, size.height * .64),
      Offset(size.width * .57, size.height * .61),
      Offset(size.width * .51, size.height * .76),
    ]) {
      canvas.drawCircle(center, 25, fruitPaint);
      canvas.drawCircle(center.translate(-8, -8), 6, Paint()..color = Colors.white.withAlpha(52));
    }
  }

  void _drawPotato(Canvas canvas, Size size) {
    final soilPaint = Paint()..color = colorScheme.onSurface.withAlpha(28);
    canvas.drawRRect(
      RRect.fromRectAndRadius(
        Rect.fromLTWH(size.width * .18, size.height * .68, size.width * .64, 16),
        const Radius.circular(10),
      ),
      soilPaint,
    );
    _drawLeafShape(canvas, Offset(size.width * .44, size.height * .42), const Size(38, 64), -0.7);
    _drawLeafShape(canvas, Offset(size.width * .58, size.height * .40), const Size(38, 64), 0.7);
    for (final center in [
      Offset(size.width * .39, size.height * .70),
      Offset(size.width * .53, size.height * .74),
      Offset(size.width * .64, size.height * .68),
    ]) {
      canvas.drawOval(
        Rect.fromCenter(center: center, width: 54, height: 34),
        Paint()..color = artwork.primary,
      );
    }
  }

  void _drawPepper(Canvas canvas, Size size) {
    _drawStem(canvas, size);
    _drawLeafShape(canvas, Offset(size.width * .38, size.height * .42), const Size(38, 68), -0.9);
    _drawLeafShape(canvas, Offset(size.width * .62, size.height * .42), const Size(38, 68), 0.9);
    final pepperPath = Path()
      ..moveTo(size.width * .49, size.height * .48)
      ..cubicTo(size.width * .72, size.height * .46, size.width * .68, size.height * .84, size.width * .52, size.height * .84)
      ..cubicTo(size.width * .36, size.height * .82, size.width * .30, size.height * .50, size.width * .49, size.height * .48)
      ..close();
    canvas.drawPath(pepperPath, Paint()..color = artwork.primary);
    canvas.drawCircle(
      Offset(size.width * .44, size.height * .60),
      8,
      Paint()..color = Colors.white.withAlpha(44),
    );
  }

  void _drawBeans(Canvas canvas, Size size) {
    _drawStem(canvas, size);
    _drawLeafShape(canvas, Offset(size.width * .36, size.height * .45), const Size(42, 76), -0.8);
    _drawLeafShape(canvas, Offset(size.width * .64, size.height * .45), const Size(42, 76), 0.8);
    final beanPaint = Paint()..color = artwork.primary;
    for (final center in [
      Offset(size.width * .38, size.height * .72),
      Offset(size.width * .50, size.height * .64),
      Offset(size.width * .62, size.height * .72),
    ]) {
      canvas.drawOval(
        Rect.fromCenter(center: center, width: 30, height: 44),
        beanPaint,
      );
    }
  }

  void _drawSquash(Canvas canvas, Size size) {
    _drawLeafShape(canvas, Offset(size.width * .42, size.height * .38), const Size(52, 82), -0.7);
    _drawLeafShape(canvas, Offset(size.width * .60, size.height * .38), const Size(52, 82), 0.7);
    final squashPaint = Paint()..color = artwork.primary;
    final center = Offset(size.width * .52, size.height * .69);
    canvas.drawOval(Rect.fromCenter(center: center, width: 96, height: 66), squashPaint);
    canvas.drawOval(
      Rect.fromCenter(center: center.translate(-20, 0), width: 32, height: 66),
      Paint()..color = artwork.primary.withAlpha(185),
    );
    canvas.drawOval(
      Rect.fromCenter(center: center.translate(20, 0), width: 32, height: 66),
      Paint()..color = artwork.primary.withAlpha(185),
    );
  }

  void _drawLeaf(Canvas canvas, Size size) {
    _drawStem(canvas, size);
    _drawLeafShape(canvas, Offset(size.width * .40, size.height * .50), const Size(58, 96), -0.7);
    _drawLeafShape(canvas, Offset(size.width * .60, size.height * .50), const Size(58, 96), 0.7);
  }

  @override
  bool shouldRepaint(covariant _CropArtworkPainter oldDelegate) {
    return oldDelegate.artwork != artwork || oldDelegate.colorScheme != colorScheme;
  }
}

class _StateMessage extends StatelessWidget {
  const _StateMessage({
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

    return Center(
      child: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(icon, color: colorScheme.primary, size: 44),
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
