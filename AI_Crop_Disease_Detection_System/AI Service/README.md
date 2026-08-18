# AI Crop Disease Prediction Service

FastAPI skeleton for the image classification service used by the Laravel API.

## Local setup

```bash
python -m venv .venv
.venv\Scripts\activate
pip install -r requirements.txt
uvicorn app.main:app --reload --port 8001
```

Set Laravel's `AI_SERVICE_URL` to `http://127.0.0.1:8001`.

The service loads `models/mobilenetv2_crop_disease.keras` when present. If the model is absent, it returns a deterministic placeholder label so the mobile and backend integration can still be tested.

## Dataset and model workflow

This project can use the public PlantVillage dataset for the first real model. PlantVillage contains 54,306 healthy and diseased leaf images across 14 crop species and 26 diseases. The configured subset focuses on Zimbabwe-relevant crops that exist in PlantVillage: maize, tomato, potato, bell pepper, soybean, and squash.

To download a manageable subset on Windows:

```powershell
.\scripts\download_plantvillage_subset.ps1 -ImagesPerClass 300
```

Or organize your own images like this:

```text
data/raw/tomato_early_blight/*.jpg
data/raw/maize_leaf_blight/*.jpg
data/raw/potato_late_blight/*.jpg
```

Then run:

```bash
python scripts/prepare_dataset.py
python scripts/train_mobilenetv2.py
python scripts/evaluate_model.py
```

The training script writes `models/mobilenetv2_crop_disease.keras` and `models/class_names.txt`.

The prediction service reads `models/class_names.txt` first so the deployed model uses the same class order produced during training. `labels.json` is only a fallback for the temporary placeholder setup.

## Turning on the real model

1. Download or collect image datasets for each supported disease.
2. Place them in `data/raw/<class_label>/`, for example:

```text
data/raw/tomato_early_blight/
data/raw/maize_leaf_blight/
```

3. Run the prepare, train, and evaluate commands above.
4. Start the FastAPI service:

```bash
uvicorn app.main:app --reload --port 8001
```

5. Set the Laravel backend `.env` value:

```env
AI_SERVICE_URL=http://127.0.0.1:8001
```

After that, new image uploads will call the trained AI model instead of the fallback.
