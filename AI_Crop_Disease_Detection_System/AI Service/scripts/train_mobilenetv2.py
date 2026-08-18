from pathlib import Path

import tensorflow as tf

DATA_DIR = Path("data/processed")
MODEL_DIR = Path("models")
IMAGE_SIZE = (224, 224)
BATCH_SIZE = 32
EPOCHS = 10


def main() -> None:
    train_dir = DATA_DIR / "train"
    validation_dir = DATA_DIR / "validation"

    if not train_dir.exists() or not validation_dir.exists():
        raise SystemExit("Run scripts/prepare_dataset.py before training.")

    train_ds = tf.keras.utils.image_dataset_from_directory(
        train_dir,
        image_size=IMAGE_SIZE,
        batch_size=BATCH_SIZE,
    )
    validation_ds = tf.keras.utils.image_dataset_from_directory(
        validation_dir,
        image_size=IMAGE_SIZE,
        batch_size=BATCH_SIZE,
    )

    class_names = train_ds.class_names
    base_model = tf.keras.applications.MobileNetV2(
        input_shape=IMAGE_SIZE + (3,),
        include_top=False,
        weights="imagenet",
    )
    base_model.trainable = False

    model = tf.keras.Sequential([
        tf.keras.layers.Rescaling(1.0 / 255),
        tf.keras.layers.RandomFlip("horizontal"),
        tf.keras.layers.RandomRotation(0.08),
        base_model,
        tf.keras.layers.GlobalAveragePooling2D(),
        tf.keras.layers.Dropout(0.2),
        tf.keras.layers.Dense(len(class_names), activation="softmax"),
    ])

    model.compile(
        optimizer=tf.keras.optimizers.Adam(),
        loss="sparse_categorical_crossentropy",
        metrics=["accuracy"],
    )
    model.fit(train_ds, validation_data=validation_ds, epochs=EPOCHS)

    MODEL_DIR.mkdir(exist_ok=True)
    model.save(MODEL_DIR / "mobilenetv2_crop_disease.keras")
    (MODEL_DIR / "class_names.txt").write_text("\n".join(class_names), encoding="utf-8")
    print("Saved model and class names.")


if __name__ == "__main__":
    main()
