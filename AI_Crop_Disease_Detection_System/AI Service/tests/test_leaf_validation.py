import io
import unittest

from PIL import Image, ImageDraw

from app.model import NonLeafImageError, validate_leaf_image


def image_bytes(image: Image.Image) -> bytes:
    output = io.BytesIO()
    image.save(output, format="PNG")
    return output.getvalue()


class LeafValidationTest(unittest.TestCase):
    def test_rejects_a_flat_non_leaf_image(self):
        image = Image.new("RGB", (300, 300), "#3366cc")

        with self.assertRaisesRegex(NonLeafImageError, "No crop leaf was detected"):
            validate_leaf_image(image_bytes(image))

    def test_accepts_a_clear_leaf_shaped_subject(self):
        image = Image.new("RGB", (300, 300), "#d8d2c4")
        draw = ImageDraw.Draw(image)
        draw.ellipse((55, 25, 245, 275), fill="#3b8f3b", outline="#174f24", width=8)
        draw.line((150, 40, 150, 280), fill="#d7dc75", width=7)
        draw.ellipse((95, 105, 125, 140), fill="#916827")

        validate_leaf_image(image_bytes(image))

    def test_rejects_a_keyboard_on_a_brown_desk(self):
        image = Image.new("RGB", (320, 240), "#9a6a3a")
        draw = ImageDraw.Draw(image)
        for row in range(5):
            for column in range(12):
                left = 25 + (column * 22)
                top = 45 + (row * 26)
                draw.rounded_rectangle(
                    (left, top, left + 18, top + 20),
                    radius=2,
                    fill="#242424",
                    outline="#777777",
                )

        with self.assertRaisesRegex(NonLeafImageError, "No crop leaf was detected"):
            validate_leaf_image(image_bytes(image))

    def test_rejects_a_random_multicolour_picture(self):
        image = Image.new("RGB", (320, 240), "#355caa")
        draw = ImageDraw.Draw(image)
        draw.rectangle((0, 120, 320, 240), fill="#c88a55")
        draw.rectangle((30, 30, 120, 115), fill="#df4b51")
        draw.ellipse((190, 25, 285, 115), fill="#e3c44a")

        with self.assertRaisesRegex(NonLeafImageError, "No crop leaf was detected"):
            validate_leaf_image(image_bytes(image))

    def test_rejects_a_uniform_green_scene_without_a_leaf_subject(self):
        image = Image.new("RGB", (320, 240), "#3f8f45")
        draw = ImageDraw.Draw(image)
        for offset in range(0, 320, 16):
            draw.line((offset, 0, offset + 40, 240), fill="#4f9f55", width=4)

        with self.assertRaisesRegex(NonLeafImageError, "No crop leaf was detected"):
            validate_leaf_image(image_bytes(image))


if __name__ == "__main__":
    unittest.main()
