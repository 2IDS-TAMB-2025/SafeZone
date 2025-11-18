#include <WiFi.h>
#include <HTTPClient.h>
#include "DHT.h"

// ====================== CONFIG Wi-Fi ======================
const char* ssid = "CE370_SENAI";
const char* password = "ac3ce7ss0";

// ====================== API ======================
const char* serverURL = "http://10.141.128.37/TCC_SAFE_ZONE_FINAL/CONTROLLER/controller_historico.php?acao=inserir";

// ====================== MQ-135 ======================
#define MQ135_AO 34
const int LIMIAR_BAIXO = 1500;
const int LIMIAR_MEDIO = 2000;
const int LIMIAR_ALTO  = 2500;

// ====================== DHT11 ======================
#define DHTPIN 23
#define DHTTYPE DHT11
DHT dht(DHTPIN, DHTTYPE);

void setup() {
  Serial.begin(9600);
  delay(1000);

  dht.begin();

  // Conectar Wi-Fi
  WiFi.begin(ssid, password);
  Serial.print("Conectando ao Wi-Fi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.print(".");
  }
  Serial.println("\nWi-Fi conectado com sucesso!");
}

void loop() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;

    // ====================== MQ-135 ======================
    int valorAnalogico = analogRead(MQ135_AO);

    String nivel = "Muito Alto";
    if (valorAnalogico < LIMIAR_BAIXO) nivel = "Baixo";
    else if (valorAnalogico < LIMIAR_MEDIO) nivel = "Médio";
    else if (valorAnalogico < LIMIAR_ALTO) nivel = "Alto";

    Serial.print("MQ-135 Bruto="); Serial.print(valorAnalogico);
    Serial.print(" | Nivel="); Serial.println(nivel);

    http.begin(serverURL);
    http.addHeader("Content-Type", "application/json");

    String jsonGases = "{";
    jsonGases += "\"id_sensor\":18,";   // ID do MQ-135 no banco
    jsonGases += "\"dados\":" + String(valorAnalogico) + ",";
    jsonGases += "\"unidade_medida\":\"" + nivel + "\"}";
    
    int httpResponse = http.POST(jsonGases);
    if (httpResponse > 0) {
      Serial.println("MQ-135 enviado com sucesso!");
      Serial.println(http.getString());
    } else {
      Serial.print("Erro ao enviar MQ-135: "); Serial.println(httpResponse);
    }
    http.end();
    delay(1000);

    // ====================== DHT11 ======================
    float umidade = dht.readHumidity();
    float temperatura = dht.readTemperature();

    if (isnan(umidade) || isnan(temperatura)) {
      Serial.println("Erro ao ler o DHT11");
    } else {
      // ---- Temperatura ----
      http.begin(serverURL);
      http.addHeader("Content-Type", "application/json");

      String jsonTemperatura = "{";
      jsonTemperatura += "\"id_sensor\":13,";  // ID do sensor de Temperatura no banco
      jsonTemperatura += "\"dados\":" + String(temperatura) + ",";
      jsonTemperatura += "\"unidade_medida\":\"°C\"}";

      httpResponse = http.POST(jsonTemperatura);
      if (httpResponse > 0) {
        Serial.println("Temperatura enviada com sucesso!");
        Serial.println(http.getString());
      } else {
        Serial.print("Erro ao enviar temperatura: "); Serial.println(httpResponse);
      }
      http.end();
      delay(1000);

      // ---- Umidade ----
      http.begin(serverURL);
      http.addHeader("Content-Type", "application/json");

      String jsonUmidade = "{";
      jsonUmidade += "\"id_sensor\":14,";  // ID do sensor de Umidade no banco
      jsonUmidade += "\"dados\":" + String(umidade) + ",";
      jsonUmidade += "\"unidade_medida\":\"%\"}";

      httpResponse = http.POST(jsonUmidade);
      if (httpResponse > 0) {
        Serial.println("Umidade enviada com sucesso!");
        Serial.println(http.getString());
      } else {
        Serial.print("Erro ao enviar umidade: "); Serial.println(httpResponse);
      }
      http.end();
      delay(1000);
    }

  } else {
    Serial.println("Wi-Fi desconectado. Tentando reconectar...");
    WiFi.begin(ssid, password);
  }

  delay(30000); // espera 30s entre ciclos
}
