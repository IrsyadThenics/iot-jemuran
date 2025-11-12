#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>

// === WiFi Configuration ===
const char* ssid = "Lenovo";       // Nama WiFi
const char* password = "sandinyaapa"; // Password WiFi

// === Server Configuration ===
const char* serverURL = "http://192.168.1.6/iot-jemuran/api/save.php"; // ambil ip laptop anda dan path folder tersimpan
const char* deviceId = "jemuran_001"; // ID unik device

// === Pin Definition ===
#define rainSensor A0   // Sensor hujan analog
#define in3 D1          // L298N in3
#define in4 D2          // L298N in4
#define enb D5          // L298N enb (PWM)
#define ledStatus D4    // LED status

// === Variabel ===
int rainValue;
int threshold = 500; // batas hujan (kalibrasi sensor)

void setup() {
  Serial.begin(115200);

  // Setup pin
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
  rainValue = analogRead(rainSensor);
  Serial.print("Nilai Sensor: ");
  Serial.println(rainValue);

  String status;

  if (rainValue < threshold) {
    // Hujan
    digitalWrite(ledStatus, HIGH);
    Serial.println("🌧 Hujan terdeteksi → Motor narik jemuran 3 detik");
    pullClothes(5000);
    
    status = "hujan";
  } else {
    // Cerah
    digitalWrite(ledStatus, LOW);
    Serial.println("☀ Cuaca cerah → Motor keluarin jemuran 5 detik");
    extendClothes(5000);
    
    status = "cerah";
  }

  // Kirim data ke server
  if (WiFi.status() == WL_CONNECTED) {
    WiFiClient client;
    HTTPClient http;

    http.begin(client, serverURL);
    http.addHeader("Content-Type", "application/json");

    String jsonData = "{\"device_id\":\"" + String(deviceId) + 
                  "\",\"rain_value\":" + String(rainValue) + 
                  ",\"status\":\"" + status + 
                  "\",\"motor_action\":\"STOP\"}";


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

  delay(10000); // delay antar update (10 detik)
}

// Fungsi tarik jemuran (reverse)
void pullClothes(unsigned long duration) {
  digitalWrite(in3, HIGH);
  digitalWrite(in4, LOW);
  analogWrite(enb, 800);
  delay(duration);
  stopMotor();
}

// Fungsi keluarin jemuran (forward)
void extendClothes(unsigned long duration) {
  digitalWrite(in3, LOW);
  digitalWrite(in4, HIGH);
  analogWrite(enb, 800);
  delay(duration);
  stopMotor();
}

// Fungsi stop motor
void stopMotor() {
  digitalWrite(in3, LOW);
  digitalWrite(in4, LOW);
  analogWrite(enb, 0);
  Serial.println("⏹ Motor berhenti");
}
