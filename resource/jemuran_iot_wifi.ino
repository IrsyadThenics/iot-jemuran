#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>

// === WiFi Configuration ===
const char* ssid = "eepiswlan";
const char* password = "eepisJOSS";

// === Server Configuration ===
const char* serverURL = "http://10.253.128.209/iot-jemuran/api/save.php";
const char* deviceId = "jemuran_001";

// === Pin Definition ===
#define rainSensor A0
#define in3 D1
#define in4 D2
#define enb D5
#define ledStatus D4

// === Variables ===
int rainValue;
int threshold = 700;  
String lastStatus = "";  // status sebelumnya
String motor_action = "";

void setup() {
  Serial.begin(115200);

  pinMode(in3, OUTPUT);
  pinMode(in4, OUTPUT);
  pinMode(enb, OUTPUT);
  pinMode(ledStatus, OUTPUT);

  stopMotor();
  digitalWrite(ledStatus, LOW);

  // Connect WiFi
  WiFi.begin(ssid, password);
  Serial.print("Menghubungkan ke WiFi ");
  Serial.println(ssid);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("\nWiFi Terhubung!");
  Serial.print("IP Address: ");
  Serial.println(WiFi.localIP());
}

void loop() {
  // Baca sensor
  rainValue = analogRead(rainSensor);
  Serial.print("Nilai Sensor: ");
  Serial.println(rainValue);

  // Tentukan status
  String status;
  if (rainValue < threshold) {
    status = "hujan";
  } else {
    status = "cerah";
  }

  // Gerakkan motor HANYA jika status cuaca berubah
  if (status != lastStatus) {
    Serial.println("=== STATUS BERUBAH ===");

    if (status == "hujan") {
      digitalWrite(ledStatus, HIGH);
      Serial.println("🌧️ Hujan terdeteksi → Motor narik jemuran 3 detik");
      pullClothes(3000);
      motor_action = "ON";
    } else {
      digitalWrite(ledStatus, LOW);
      Serial.println("☀️ Cuaca cerah → Motor keluarin jemuran 5 detik");
      extendClothes(3000);
      motor_action = "OFF";
    }

    lastStatus = status;  // simpan status terbaru
  }

  // Kirim data ke server setiap loop
  if (WiFi.status() == WL_CONNECTED) {
    WiFiClient client;
    HTTPClient http;

    http.begin(client, serverURL);
    http.addHeader("Content-Type", "application/json");

    String jsonData = "{";
jsonData += "\"device_id\":\"" + String(deviceId) + "\",";
jsonData += "\"rain_value\":" + String(rainValue) + ",";
jsonData += "\"status\":\"" + status + "\",";
jsonData += "\"motor_action\":\"" + motor_action + "\"";
jsonData += "}";


    int httpResponseCode = http.POST(jsonData);

    if (httpResponseCode > 0) {
      Serial.print("Server Response Code: ");
      Serial.println(httpResponseCode);
      Serial.println("Response: " + http.getString());
    } else {
      Serial.print("Error Code: ");
      Serial.println(httpResponseCode);
    }

    http.end();
  }

  delay(10000);  // cek tiap 10 detik
}

// === Motor Functions ===
void pullClothes(unsigned long duration) {
  digitalWrite(in3, HIGH);
  digitalWrite(in4, LOW);
  analogWrite(enb, 800);
  delay(duration);
  stopMotor();
}

void extendClothes(unsigned long duration) {
  digitalWrite(in3, LOW);
  digitalWrite(in4, HIGH);
  analogWrite(enb, 800);
  delay(duration);
  stopMotor();
}

void stopMotor() {
  digitalWrite(in3, LOW);
  digitalWrite(in4, LOW);
  analogWrite(enb, 0);
  Serial.println("⏹️ Motor berhenti");
}