from functools import lru_cache
from pathlib import Path

MODEL_PATH = Path(__file__).resolve().parents[1] / "models" / "mobilenetv2_crop_disease.keras"


def model_exists() -> bool:
    return MODEL_PATH.exists()


@lru_cache(maxsize=1)
def load_prediction_assets():
    import tensorflow as tf

    from app.labels import load_label_map

    return tf.keras.models.load_model(MODEL_PATH), load_label_map()


def predict_with_model(_: bytes) -> tuple[str, float] | None:
    if not model_exists():
        return None

    # Load TensorFlow lazily so the placeholder service can run before model setup.
    import numpy as np
    from PIL import Image
    from io import BytesIO

    model, label_map = load_prediction_assets()

    image = Image.open(BytesIO(_)).convert("RGB").resize((224, 224))
    batch = np.expand_dims(np.asarray(image, dtype=np.float32), axis=0)
    predictions = model.predict(batch, verbose=0)[0]
    class_index = int(np.argmax(predictions))
    confidence = float(predictions[class_index])

    return label_map.get(str(class_index), "unknown"), confidence
