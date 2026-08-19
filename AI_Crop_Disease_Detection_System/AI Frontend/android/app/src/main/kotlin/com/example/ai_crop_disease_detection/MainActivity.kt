package com.example.ai_crop_disease_detection

import android.speech.tts.TextToSpeech
import io.flutter.embedding.android.FlutterActivity
import io.flutter.embedding.engine.FlutterEngine
import io.flutter.plugin.common.MethodChannel
import java.util.Locale

class MainActivity : FlutterActivity() {
    private var textToSpeech: TextToSpeech? = null

    override fun configureFlutterEngine(flutterEngine: FlutterEngine) {
        super.configureFlutterEngine(flutterEngine)

        MethodChannel(
            flutterEngine.dartExecutor.binaryMessenger,
            "ai_crop_disease_detection/tts"
        ).setMethodCallHandler { call, result ->
            if (call.method == "stop") {
                result.success(textToSpeech?.stop() == TextToSpeech.SUCCESS)
                return@setMethodCallHandler
            }

            if (call.method != "speak") {
                result.notImplemented()
                return@setMethodCallHandler
            }

            val text = call.argument<String>("text").orEmpty().trim()
            val languageCode = call.argument<String>("languageCode") ?: "en"
            if (text.isEmpty()) {
                result.error("empty_text", "There is no diagnosis text to read.", null)
                return@setMethodCallHandler
            }

            val existingEngine = textToSpeech
            if (existingEngine != null) {
                speak(existingEngine, text, languageCode, result)
                return@setMethodCallHandler
            }

            textToSpeech = TextToSpeech(this) { status ->
                val engine = textToSpeech
                if (status != TextToSpeech.SUCCESS || engine == null) {
                    result.error("tts_initialization_failed", "Android text-to-speech could not start.", null)
                    return@TextToSpeech
                }
                speak(engine, text, languageCode, result)
            }
        }
    }

    private fun speak(
        engine: TextToSpeech,
        text: String,
        requestedLanguage: String,
        result: MethodChannel.Result
    ) {
        val requestedLocale = if (requestedLanguage == "sn") Locale("sn", "ZW") else Locale.US
        val requestedAvailability = engine.isLanguageAvailable(requestedLocale)
        val requestedSupported = requestedAvailability >= TextToSpeech.LANG_AVAILABLE
        val actualLocale = if (requestedSupported) requestedLocale else Locale.US
        val actualLanguage = if (requestedSupported) requestedLanguage else "en"
        val fallbackUsed = requestedLanguage == "sn" && !requestedSupported

        if (engine.setLanguage(actualLocale) < TextToSpeech.LANG_AVAILABLE) {
            result.error("language_unavailable", "No supported Android speech language is installed.", null)
            return
        }

        val status = engine.speak(text, TextToSpeech.QUEUE_FLUSH, null, "diagnosis-result")
        result.success(
            mapOf(
                "success" to (status == TextToSpeech.SUCCESS),
                "requestedLanguage" to requestedLanguage,
                "actualLanguage" to actualLanguage,
                "fallbackUsed" to fallbackUsed,
                "message" to if (fallbackUsed) {
                    "Shona speech is unavailable on this phone; English speech was used."
                } else {
                    "Speech started."
                }
            )
        )
    }

    override fun onDestroy() {
        textToSpeech?.stop()
        textToSpeech?.shutdown()
        textToSpeech = null
        super.onDestroy()
    }
}
