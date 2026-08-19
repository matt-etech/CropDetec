# CropDetec MobileNetV2 Evaluation

Generated: 2026-08-19T06:49:20.468040+00:00
Test images: 450
Accuracy: 0.9200
Macro precision: 0.9194
Macro recall: 0.9200
Macro F1: 0.9190
Acceptance status: **PASS**

| Class | Precision | Recall | F1 | Support |
|---|---:|---:|---:|---:|
| maize_common_rust | 0.9778 | 0.9778 | 0.9778 | 45 |
| maize_gray_leaf_spot | 0.8478 | 0.8667 | 0.8571 | 45 |
| maize_leaf_blight | 0.8864 | 0.8667 | 0.8764 | 45 |
| pepper_bacterial_spot | 0.9375 | 1.0000 | 0.9677 | 45 |
| potato_early_blight | 1.0000 | 1.0000 | 1.0000 | 45 |
| potato_late_blight | 0.8600 | 0.9556 | 0.9053 | 45 |
| soybean_healthy | 0.9778 | 0.9778 | 0.9778 | 45 |
| squash_powdery_mildew | 1.0000 | 1.0000 | 1.0000 | 45 |
| tomato_early_blight | 0.8500 | 0.7556 | 0.8000 | 45 |
| tomato_late_blight | 0.8571 | 0.8000 | 0.8276 | 45 |

Acceptance criteria:

- Accuracy >= 0.80
- Macro F1 >= 0.75

See `confusion_matrix.csv` for the full confusion matrix.
