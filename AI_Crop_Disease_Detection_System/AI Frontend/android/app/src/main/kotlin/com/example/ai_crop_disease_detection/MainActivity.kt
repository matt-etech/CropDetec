package com.example.ai_crop_disease_detection

import io.flutter.embedding.android.FlutterActivity
import io.flutter.embedding.engine.FlutterEngine
import io.flutter.plugin.common.MethodChannel
import java.util.Locale
import android.speech.tts.TextToSpeech

class MainActivity : FlutterActivity() {
    private var textToSpeech: TextToSpeech? = null

    override fun configureFlutterEngine(flutterEngine: FlutterEngine) {
        super.configureFlutterEngine(flutterEngine)

        MethodChannel(
            flutterEngine.dartExecutor.binaryMessenger,
            "ai_crop_disease_detection/tts"
        ).setMethodCallHandler { call, result ->
            if (call.method != "speak") {
                result.notImplemented()
                return@setMethodCallHandler
            }

            val text = call.argument<String>("text").orEmpty()
            val languageCode = call.argument<String>("languageCode") ?: "en"
            val locale = if (languageCode == "sn") Locale("sn", "ZW") else Locale.US

            textToSpeech = textToSpeech ?: TextToSpeech(this) { status ->
                if (status == TextToSpeech.SUCCESS) {
                    textToSpeech?.language = locale
                    textToSpeech?.speak(text, TextToSpeech.QUEUE_FLUSH, null, "diagnosis-result")
                }
            }

            textToSpeech?.language = locale
            textToSpeech?.speak(text, TextToSpeech.QUEUE_FLUSH, null, "diagnosis-result")
            result.success(true)
        }
    }

    override fun onDestroy() {
        textToSpeech?.shutdown()
        textToSpeech = null
        super.onDestroy()
    }
}
