#include <WiFi.h>
#include <HTTPClient.h>

// CONFIGURAÇÕES DO SEU WI-FI ===
const char* ssid = "CE370_SENAI";
const char* password = "ac3ce7ss0";

// ENDEREÇO DO SERVIDOR LOCAL ===
const char* serverUrl = "http://10.141.128.54/TCC_SAFE_ZONE_FINAL/CONTROLLER/controller_historico.php?acao=inserir";

// CONFIGURAÇÃO DO SENSOR MQ-135
// AO-> D12 (analógico)
#define MQ135_AO 34

// Ajuste conforme seu ambiente
const int LIMIAR_BAIXO = 1500;
const int LIMIAR_MEDIO = 2000;
const int LIMIAR_ALTO  = 2500;

const int ADC_MAX = 4095;

void setup() {
  Serial.begin(9600);
  delay(1000);

  // Conexão Wi-Fi
  WiFi.begin(ssid, password);
  Serial.print("Conectando ao Wi-Fi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.print(".");
  }
  Serial.println("\nWi-Fi conectado com sucesso!");

  Serial.println("Teste MQ-135 iniciado...");
}

void loop() {
  if (WiFi.status() == WL_CONNECTED) {
    int valorAnalogico = analogRead(MQ135_AO);

    // Determina nível
    String nivel = "Muito Alto";
    if (valorAnalogico < LIMIAR_BAIXO) {
      nivel = "Baixo";
    }
    else if (valorAnalogico < LIMIAR_MEDIO) {
      nivel = "Médio";
    }
    else if (valorAnalogico < LIMIAR_ALTO) {
      nivel = "Alto";
    }

    // Debug Serial
    Serial.print("Bruto=");
    Serial.print(valorAnalogico);
    Serial.print(" | Nivel=");
    Serial.println(nivel);
    
    // sensor

    // === Enviar JSON único ===
    HTTPClient http;
    http.begin(serverUrl);
    http.addHeader("Content-Type", "application/json");

    String jsonPayload = "{";
    jsonPayload += "\"id_sensor\":11,";                        
    jsonPayload += "\"dados\":" + String(valorAnalogico) + ","; 
    jsonPayload += "\"unidade_medida\":\"" + nivel + "\"}";
    // juntar json    

    Serial.println("JSON enviado: " + jsonPayload);

    int httpResponseCode = http.POST(jsonPayload);
    if (httpResponseCode > 0) {
      Serial.println("Dados enviados com sucesso:");
      Serial.println(http.getString());
    } else {
      Serial.print("Erro ao enviar dados: ");
      Serial.println(httpResponseCode);
    }
    http.end();
  } else {
    Serial.println("Wi-Fi desconectado. Tentando reconectar...");
    WiFi.begin(ssid, password);
  }
  delay(30000); // Aguarda 30 segundos
}
