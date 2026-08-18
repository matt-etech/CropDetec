from pathlib import Path

MODEL_PATH = Path(__file__).resolve().parents[1] / "models" / "mobilenetv2_crop_disease.keras"


def model_exists() -> bool:
    return MODEL_PATH.exists()


def predict_with_model(_: bytes) -> tuple[str, float] | None:
    if not model_exists():
        return None

    # Load TensorFlow lazily so the placeholder service can run before model setup.
    import numpy as np
    import tensorflow as tf
    from PIL import Image
    from io import BytesIO

    from app.labels import load_label_map

    model = tf.keras.models.load_model(MODEL_PATH)
    label_map = load_label_map()

    image = Image.open(BytesIO(_)).convert("RGB").resize((224, 224))
    batch = np.expand_dims(np.asarray(image, dtype=np.float32), axis=0)
    predictions = model.predict(batch, verbose=0)[0]
    class_index = int(np.argmax(predictions))
    confidence = float(predictions[class_index])

    return label_map.get(str(class_index), "unknown"), confidence
