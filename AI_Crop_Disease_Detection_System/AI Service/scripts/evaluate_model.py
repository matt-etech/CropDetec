from pathlib import Path

import numpy as np
import tensorflow as tf

DATA_DIR = Path("data/processed/test")
MODEL_PATH = Path("models/mobilenetv2_crop_disease.keras")
IMAGE_SIZE = (224, 224)
BATCH_SIZE = 32


def main() -> None:
    if not MODEL_PATH.exists():
        raise SystemExit("Train or copy models/mobilenetv2_crop_disease.keras first.")

    test_ds = tf.keras.utils.image_dataset_from_directory(
        DATA_DIR,
        image_size=IMAGE_SIZE,
        batch_size=BATCH_SIZE,
        shuffle=False,
    )
    model = tf.keras.models.load_model(MODEL_PATH)
    probabilities = model.predict(test_ds, verbose=0)
    predictions = np.argmax(probabilities, axis=1)
    truth = np.concatenate([labels.numpy() for _, labels in test_ds])

    accuracy = float(np.mean(predictions == truth))
    print(f"accuracy={accuracy:.4f}")

    for index, class_name in enumerate(test_ds.class_names):
        true_positive = int(np.sum((predictions == index) & (truth == index)))
        false_positive = int(np.sum((predictions == index) & (truth != index)))
        false_negative = int(np.sum((predictions != index) & (truth == index)))
        precision = true_positive / max(true_positive + false_positive, 1)
        recall = true_positive / max(true_positive + false_negative, 1)
        f1 = 2 * precision * recall / max(precision + recall, 1e-9)
        print(f"{class_name}: precision={precision:.4f} recall={recall:.4f} f1={f1:.4f}")

    matrix = tf.math.confusion_matrix(truth, predictions, num_classes=len(test_ds.class_names))
    print("confusion_matrix=")
    print(matrix.numpy())


if __name__ == "__main__":
    main()
