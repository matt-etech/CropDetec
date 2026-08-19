from functools import lru_cache
from io import BytesIO
import os
from pathlib import Path

import numpy as np
from PIL import Image

MODEL_PATH = Path(__file__).resolve().parents[1] / "models" / "mobilenetv2_crop_disease.keras"
MIN_VEGETATION_RATIO = float(os.getenv("LEAF_MIN_VEGETATION_RATIO", "0.08"))
MIN_GREEN_RATIO = float(os.getenv("LEAF_MIN_GREEN_RATIO", "0.12"))
MIN_CENTRE_GREEN_RATIO = float(os.getenv("LEAF_MIN_CENTRE_GREEN_RATIO", "0.15"))
MAX_CORNER_GREEN_RATIO = float(os.getenv("LEAF_MAX_CORNER_GREEN_RATIO", "0.75"))
MIN_IMAGE_VARIATION = float(os.getenv("LEAF_MIN_IMAGE_VARIATION", "8.0"))
MIN_MODEL_CONFIDENCE = float(os.getenv("LEAF_MIN_MODEL_CONFIDENCE", "0.55"))
MIN_AUGMENTED_CONFIDENCE = float(os.getenv("LEAF_MIN_AUGMENTED_CONFIDENCE", "0.45"))
MIN_CLASS_MARGIN = float(os.getenv("LEAF_MIN_CLASS_MARGIN", "0.20"))


class NonLeafImageError(ValueError):
    """Raised when an uploaded image does not look like a crop leaf."""


def model_exists() -> bool:
    return MODEL_PATH.exists()


@lru_cache(maxsize=1)
def load_prediction_assets():
    import tensorflow as tf

    from app.labels import load_label_map

    return tf.keras.models.load_model(MODEL_PATH), load_label_map()


def leaf_image_metrics(contents: bytes) -> tuple[float, float, float, float, float]:
    """Return colour, composition, and visual-variation leaf metrics."""
    image = Image.open(BytesIO(contents)).convert("RGB").resize((224, 224))
    rgb = np.asarray(image, dtype=np.float32)
    hsv = np.asarray(image.convert("HSV"), dtype=np.uint8)
    hue, saturation, value = hsv[..., 0], hsv[..., 1], hsv[..., 2]

    # Covers healthy green tissue plus yellow/brown tissue common on diseased leaves.
    vegetation_pixels = (
        (hue >= 8)
        & (hue <= 115)
        & (saturation >= 45)
        & (value >= 25)
    )
    green_pixels = (
        (hue >= 35)
        & (hue <= 115)
        & (saturation >= 45)
        & (value >= 25)
    )
    vegetation_ratio = float(np.mean(vegetation_pixels))
    green_ratio = float(np.mean(green_pixels))
    centre_green_ratio = float(np.mean(green_pixels[45:179, 45:179]))
    corners = np.concatenate(
        [
            green_pixels[:45, :45].ravel(),
            green_pixels[:45, -45:].ravel(),
            green_pixels[-45:, :45].ravel(),
            green_pixels[-45:, -45:].ravel(),
        ]
    )
    corner_green_ratio = float(np.mean(corners))
    image_variation = float(np.mean(np.std(rgb, axis=(0, 1))))
    return (
        vegetation_ratio,
        green_ratio,
        centre_green_ratio,
        corner_green_ratio,
        image_variation,
    )


def validate_leaf_image(contents: bytes) -> None:
    (
        vegetation_ratio,
        green_ratio,
        centre_green_ratio,
        corner_green_ratio,
        image_variation,
    ) = leaf_image_metrics(contents)
    if (
        vegetation_ratio < MIN_VEGETATION_RATIO
        or green_ratio < MIN_GREEN_RATIO
        or centre_green_ratio < MIN_CENTRE_GREEN_RATIO
        or corner_green_ratio > MAX_CORNER_GREEN_RATIO
        or image_variation < MIN_IMAGE_VARIATION
    ):
        raise NonLeafImageError(
            "No crop leaf was detected. Retake a clear photo with one leaf "
            "filling most of the frame."
        )


def predict_with_model(contents: bytes) -> tuple[str, float] | None:
    validate_leaf_image(contents)

    if not model_exists():
        return None

    # Load TensorFlow lazily so the placeholder service can run before model setup.
    model, label_map = load_prediction_assets()

    source = Image.open(BytesIO(contents)).convert("RGB")
    width, height = source.size
    inset_x, inset_y = int(width * 0.12), int(height * 0.12)
    variants = [
        source.resize((224, 224)),
        source.transpose(Image.Transpose.FLIP_LEFT_RIGHT).resize((224, 224)),
        source.crop((inset_x, inset_y, width - inset_x, height - inset_y)).resize(
            (224, 224)
        ),
    ]
    batch = np.stack(
        [np.asarray(variant, dtype=np.float32) for variant in variants],
        axis=0,
    )
    predictions = np.asarray(model.predict(batch, verbose=0))
    class_indices = np.argmax(predictions, axis=1)
    class_index = int(class_indices[0])
    confidences = predictions[:, class_index]
    mean_predictions = np.mean(predictions, axis=0)
    sorted_confidences = np.sort(mean_predictions)
    class_margin = float(sorted_confidences[-1] - sorted_confidences[-2])
    confidence = float(np.mean(confidences))

    if (
        not np.all(class_indices == class_index)
        or confidence < MIN_MODEL_CONFIDENCE
        or float(np.min(confidences)) < MIN_AUGMENTED_CONFIDENCE
        or class_margin < MIN_CLASS_MARGIN
    ):
        raise NonLeafImageError(
            "No crop leaf was detected with enough confidence. Retake a clear "
            "photo with one leaf filling most of the frame."
        )

    return label_map.get(str(class_index), "unknown"), confidence
