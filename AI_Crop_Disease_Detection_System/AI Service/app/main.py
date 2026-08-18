from io import BytesIO
from typing import Annotated

from fastapi import FastAPI, File, Form, HTTPException, UploadFile
from PIL import Image, UnidentifiedImageError

from app.model import predict_with_model

app = FastAPI(title="AI Crop Disease Prediction Service")


@app.get("/health")
def health() -> dict[str, str]:
    return {"status": "ok", "service": "AI Crop Disease Prediction Service"}


@app.post("/predict")
async def predict(
    image: Annotated[UploadFile, File()],
    crop_id: Annotated[int | None, Form()] = None,
) -> dict[str, float | int | str | None]:
    contents = await image.read()

    try:
        with Image.open(BytesIO(contents)) as opened_image:
            opened_image.verify()
    except UnidentifiedImageError as exc:
        raise HTTPException(status_code=422, detail="Upload a valid crop image.") from exc

    model_prediction = predict_with_model(contents)
    if model_prediction is not None:
        label, confidence = model_prediction
        return {
            "predicted_label": label,
            "confidence": confidence,
            "crop_id": crop_id,
        }

    # Replace this mapping with a MobileNetV2/TensorFlow model and label decoder.
    label = "tomato_early_blight" if crop_id in (None, 1) else "maize_leaf_blight"

    return {
        "predicted_label": label,
        "confidence": 0.885,
        "crop_id": crop_id,
    }
