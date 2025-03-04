/*
All pins setup
    Motor to ESP32
        +       G5
    
    Water Pump
        +       G18

    Moisture Sensor to ESP32
        RED     5V
        BLACK   GND
        YELLOW  G34
        BLACK   GND

        RED     5V
        BLACK   GND
        YELLOW  G35
        BLACK   GND

    Load Cell to HX711
        RED     E+
        BLACK   E-
        White   A-
        Green   A+
            HX711 to ESP32
                GND     GND
                DT      G27
                SCK     G14
                VCC     5V

    DS18 to Pluggable Terminal
        YELLOW  DAT
        RED     VCC
        BLACK   GND
            Pluggable Terminal to ESP32
                DAT     G25
                VCC     VCC
                GND     GND

                DAT     G26
                VCC     VCC
                GND     GND

    NPK to MAX485
        YELLOW  A  
        BLUE    B
            NPK to ESP32
                BROWN   5V
                BLACK   GND
            MAX485 to ESP32
                VCC     5V
                GND     GND
                RO      G23
                RE      G19
                DE      G21
                DI      G22
*/

// libraries
#include <WiFi.h>               // wifi library
#include <HTTPClient.h>         // http library
#include <OneWire.h>            // wire temperature sensor
#include <HX711_ADC.h>          // load cell library
#include <DallasTemperature.h>  // ds18 library
#include <SoftwareSerial.h>     // npk library

// wifi connection
const char* ssid = "binay wifi";
const char* password = "kenetsabog";
const String serverIP = "192.168.108.143";  //this is fido ipaddress

// make an http request object
HTTPClient http;
int httpResponseCode;
String response;

// define the pins
const int motor = 5;
const int HX711_sck = 14;
const int waterPump = 18;
const int ds18pin1 = 25;
const int ds18pin2 = 26;
const int HX711_dout = 27;
const int moistureSensor1 = 34;
const int moistureSensor2 = 35;

// Define the GPIO pins for RE, DE, DI, and RO
#define RE 19
#define DE 21
#define DI 22
#define RO 23

// load cell offset values
const long tare_offset = 7848856;       // offset to 0 weight
const float calibrationValue = -102.62;  // multiplier to get true weight

// timer
volatile unsigned long requestMistingTime;

//HX711 constructor:
HX711_ADC LoadCell(HX711_dout, HX711_sck);

// create onewire using the ds18pin
OneWire oneWire1(ds18pin1);
OneWire oneWire2(ds18pin2);

// use the onewire or ds18 as sensor
DallasTemperature sensors1(&oneWire1);
DallasTemperature sensors2(&oneWire2);

// errorTries for sensors
int errorTries = 0;

const uint32_t TIMEOUT = 500UL;

byte values[11];

//const byte code[]= {0x01, 0x03, 0x00, 0x1e, 0x00, 0x03, 0x65, 0xCD};
const byte nitro[] = { 0x01, 0x03, 0x00, 0x04, 0x00, 0x01, 0xC5, 0xCB };
const byte phos[] = { 0x01, 0x03, 0x00, 0x05, 0x00, 0x01, 0x94, 0x0B };
const byte pota[] = { 0x01, 0x03, 0x00, 0x06, 0x00, 0x01, 0x64, 0x0B };
const byte ph[] = { 0x01, 0x03, 0x00, 0x03, 0x00, 0x01, 0x74, 0x0A };

SoftwareSerial mod(RO, DI);  // Rx pin, Tx pin

// first run of the code
void setup() {
  // serial and software to be 4800
  Serial.begin(4800);
  mod.begin(4800);
  delay(1000);

  // state wifi as station
  WiFi.mode(WIFI_STA);

  // create an event for WiFi if there is change
  WiFi.onEvent(disconnectedWiFi, ARDUINO_EVENT_WIFI_STA_DISCONNECTED);
  WiFi.onEvent(connectedWiFi, ARDUINO_EVENT_WIFI_STA_CONNECTED);

  // connect through wifi using ssid and password
  WiFi.begin(ssid, password);

  // make an http object and set the http request 10 seconds timeout
  http.setTimeout(10000);

  // start Load cell, set offset and calibration factor
  LoadCell.begin();
  LoadCell.start(2000);
  LoadCell.setTareOffset(tare_offset);
  LoadCell.setCalFactor(calibrationValue);

  // long LoadCell.getTareOffset();						  //get the tare offset (raw data value output without the scale "calFactor")
  // float LoadCell.getCalFactor(); 						//returns the current calibration factor

  // state RE, DE, motor and water pump as output
  pinMode(RE, OUTPUT);
  pinMode(DE, OUTPUT);
  pinMode(motor, OUTPUT);
  pinMode(waterPump, OUTPUT);

  // read values 0 - 4095 from analog
  analogReadResolution(12);
}

// loop the code
void loop() {
  // do not run the loop if there is no wifi
  if (WiFi.status() != WL_CONNECTED) return;

  // API and header to read the request of the server for esp32 to do
  http.begin("http://"+ serverIP +"/HotCompostThesisWebsite/contexts/ReadServerRequestProcess.php");
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  // make a get request from the server
  httpResponseCode = http.GET();
  response = http.getString();

  // free the http resources
  http.end();

  // if response code is not OK, return
  if (httpResponseCode < 0) return;

  // if server requests the weight or npk, give the load cell weight / give the npk
  if (response == "NPK") giveNPK();
  if ( response.indexOf("Weight") != -1 ) giveWeight();
  response.indexOf("MistRequest") != -1 ? digitalWrite(waterPump, HIGH) : digitalWrite(waterPump, LOW);

  // API and header to read any on going hot compost in the database
  http.begin("http://"+ serverIP +"/HotCompostThesisWebsite/contexts/ReadHotCompostProcess.php");
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  // make a get request from the server
  httpResponseCode = http.GET();
  response = http.getString();

  // free the http resources
  http.end();

  // if response code is not OK, return
  if (httpResponseCode < 0) return;

  // if there is no hot compost in progress, return
  if (response == "None") return;

  // if the response is Layering, check if there are actions needed
  if (response == "Layering") return (layeringProcess());

  // if the compost is mixing, turn on the motor
  if (response == "Mixing") return (mixingProcess());

  // if the response is not in progress, return
  if (response != "In Progress") return;

  // get the value from the moisture sensor and DS18
  double moisturePercent = getmoisturePercent();
  float temperatureC = gettemperatureC();
  
  // if moisture isn't in the threshold
  if (moisturePercent < 50) {
    // return to get another value for 10 tries if there is a misreading
    if (errorTries < 10) {
      errorTries++;
      return;
    }
    // do misting if there is error 10 tries
    errorTries = 0;
    return mistContent(moisturePercent);
  }

  // if temperature isn't in the threshold
  if (temperatureC > 65) {
    // return to get another value for 10 tries if there is a misreading
    if (errorTries < 10) {
      errorTries++;
      return;
    }
    // do mixing if there is error 10 tries
    errorTries = 0;
    return requestMixContent();
  }

  // set error tries as 0 again to reset if there is good reading
  errorTries = 0;

  // create payload for POST
  String payload = "inputMoisturePercent=" + String(moisturePercent, 2) + "&inputTemperatureCelsius=" + String(temperatureC, 2);

  // API and header to read the recent data and send the data of sensor to database with interval of 1hr
  http.begin("http://"+ serverIP +"/HotCompostThesisWebsite/contexts/ReadTimeProcess.php");
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  // POST the payload to server and get the response message
  httpResponseCode = http.POST(payload);
  response = http.getString();

  // free the http resources
  http.end();
}

// if not connected in the wifi
void disconnectedWiFi(WiFiEvent_t wifi_event, WiFiEventInfo_t wifi_info) {
  Serial.print("\nNo Connection. Retrying");
  // connect through wifi using ssid and password
  WiFi.begin(ssid, password);
}

// if connected to the wifi
void connectedWiFi(WiFiEvent_t wifi_event, WiFiEventInfo_t wifi_info) {
  Serial.println("\nConnected to the Wifi network");
}

// process of giving esp32 load cell weight to server
void giveWeight() {
  // if there is still no update in load cell, return
  if (!LoadCell.update()) return;

  // if the weight measurement updated, get the value
  float weightReading = LoadCell.getData();

  // create payload for POST
  String payload = "inputWeightReading=" + String(weightReading, 2);

  // API and header to send and read the weight to database
  http.begin("http://"+ serverIP +"/HotCompostThesisWebsite/contexts/UpdateWeightSensorProcess.php");
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  // POST the payload to server
  httpResponseCode = http.POST(payload);

  // free the http resources
  http.end();
}

// process of mixing the contents
void mixingProcess() {
  // delay for 1 seconds before starting
  delay (3000);
  // turn on the motor for 5 seconds
  digitalWrite(motor, HIGH);
  delay (5000);
  // turn off the motor
  digitalWrite(motor, LOW);
}

// process of getting the misting time
void getMistingTime() {
  httpResponseCode = -1;
  while (httpResponseCode < 0){
    // API and header to check weight to get misting time
    http.begin("http://"+ serverIP +"/HotCompostThesisWebsite/contexts/ReadWeightMistingTimeProcess.php");
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    // make a get request from the server
    httpResponseCode = http.GET();
    String responseTime = http.getString();

    // get the misting time from response
    requestMistingTime = responseTime.toInt();

    // free the http resources
    http.end();
  }
}

// returns the moisture percentage
double getmoisturePercent() {
  // get the value from the moisture sensor and return it
  double moistureValue1 = analogRead(moistureSensor1);
  double moistureValue2 = analogRead(moistureSensor2);
  // double moisturePercent1 = (1 - (moistureValue1 / 2500)) * 100;
  // double moisturePercent2 = (1 - (moistureValue2 / 1094)) * 100;
  // double moisturePercent = (moisturePercent1 + moisturePercent2) / 2;

  double moisturePercent = (1 - (moistureValue2 / 2642)) * 100;     // one moisture

  return moisturePercent;
}

// returns the temperature in Celsius
float gettemperatureC() {
  sensors1.requestTemperatures();                           // Request temperature readings
  sensors2.requestTemperatures();                           // Request temperature readings
  float temperatureC1 = sensors1.getTempCByIndex(0);        // Read temperature in Celsius
  float temperatureC2 = sensors2.getTempCByIndex(0);        // Read temperature in Celsius

  // float temperatureC = (temperatureC1 + temperatureC2) / 2; // two sensor average
  // temperatureC = temperatureC + 4.86;                       // calibration 4.86

  float temperatureC = temperatureC1 + 4.86;                  // only one sensor + callibration

  return temperatureC;
}

// process if the hotcompost is on Layering
void layeringProcess() {
  // API and header to check any request of layering to esp32
  http.begin("http://"+ serverIP +"/HotCompostThesisWebsite/contexts/ReadLayeringRequestsProcess.php");
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  // make a get request from the server
  httpResponseCode = http.GET();
  response = http.getString();

  // free the http resources
  http.end();

  // if response code is not OK, return
  if (httpResponseCode < 0) return;

  // if there is hot compost that requests misting or mixing, return
  if (response == "None") return;

  // if the response does not have a word Misting Accepted, return
  if ( response.indexOf("Misting Accepted") < 0 ) return;

  // get the misting time and turn on accordingly to requestMistingTime
  getMistingTime();
  digitalWrite(waterPump, HIGH);
  delay(requestMistingTime);

  // get the value from the moisture sensor and loop if less than 50
  double moisturePercent = getmoisturePercent();
  while (moisturePercent < 50) moisturePercent = getmoisturePercent();

  // turn off the waterpump if done misting time and right moisture percent
  digitalWrite(waterPump, LOW);

  // get the response as payload
  String payload = "requestAccepted=" + response;

  // reset the httpResponse Code
  httpResponseCode = -1;

  // while the response code is not success, loop the request
  while (httpResponseCode < 0){
    // API and header to send the accepted to be mist to done
    http.begin("http://"+ serverIP +"/HotCompostThesisWebsite/contexts/UpdateMistingAcceptedToDone.php");
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    // POST the payload to server
    httpResponseCode = http.POST(payload);

    // get the feedback of server to client
    response = http.getString();

    // free the http resources
    http.end();

    // if the response is not ok, return
    if (response != "Done") httpResponseCode = -1;
  }
}

// process of misting if lower than 50% moisture
void mistContent(double moisturePercent) {
  // get while response code is not good, insert a notification of misting
  httpResponseCode = -1;
  while (httpResponseCode < 0){
    // API and header to request for mixing to database
    http.begin("http://"+ serverIP +"/HotCompostThesisWebsite/contexts/CreateMistNotification.php");
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    // make a get request from the server
    httpResponseCode = http.GET();
    response = http.getString();

    // if the response is not notification sent, reset the response code
    if (response != "Misting Notification Sent") httpResponseCode = -1;

    // free the http resources
    http.end();
  }

  // turn on the waterPump
  digitalWrite(waterPump, HIGH);

  // get the value from the moisture sensor and loop if less than 50
  while (moisturePercent < 50) moisturePercent = getmoisturePercent();

  // turn off the waterpump
  digitalWrite(waterPump, LOW);
}

// process of requesting the contents to mix
void requestMixContent() {
  // API and header to request for mixing to database
  http.begin("http://"+ serverIP +"/HotCompostThesisWebsite/contexts/RequestMixingProcess.php");
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  // make a get request from the server
  httpResponseCode = http.GET();
  response = http.getString();

  // free the http resources
  http.end();
}

// process of giving esp32 npk to server
void giveNPK() {
  // process of giving esp32 NPK to server
  byte val1, val2, val3, val6;

  // get values with calibration factor (multiplied or divided)
  Serial.println("");
  Serial.print("Nitrogen: ");
  val1 = nitrogen();
  Serial.println(val1);

  Serial.print("Phosphorous: ");
  val2 = phosphorous() * 0.3714285714;
  Serial.println(val2);

  Serial.print("Potassium: ");
  val3 = potassium() * 0.193877551;
  Serial.println(val3);

  Serial.print("pH: ");
  val6 = (pH() / 10) - 1;
  Serial.println(val6);

  // create a payload to send to the server
  String payload = "inputNitrogenReading=" + String(val1) + "&inputPhosphorusReading=" + String(val2) + "&inputPotassiumReading=" + String(val3) + "&inputPHReading=" + String(val6);

  // API and header to send and read the weight to database
  http.begin("http://"+ serverIP +"/HotCompostThesisWebsite/contexts/UpdateNPKSensorProcess.php");
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  // POST the payload to server
  httpResponseCode = http.POST(payload);

  // get the feedback of server to client
  response = http.getString();

  // free the http resources
  http.end();
}

byte pH() {
  uint32_t startTime = 0;
  uint8_t byteCount = 0;

  digitalWrite(DE, HIGH);
  digitalWrite(RE, HIGH);
  delay(10);
  mod.write(ph, sizeof(ph));
  mod.flush();
  digitalWrite(DE, LOW);
  digitalWrite(RE, LOW);

  startTime = millis();
  while (millis() - startTime <= TIMEOUT) {
    if (mod.available() && byteCount < sizeof(values)) {
      values[byteCount++] = mod.read();
      printHexByte(values[byteCount - 1]);
    }
  }
  Serial.println();
  return values[4];
}

byte nitrogen() {
  uint32_t startTime = 0;
  uint8_t byteCount = 0;

  digitalWrite(DE, HIGH);
  digitalWrite(RE, HIGH);
  delay(10);
  mod.write(nitro, sizeof(nitro));
  mod.flush();
  digitalWrite(DE, LOW);
  digitalWrite(RE, LOW);

  startTime = millis();
  while (millis() - startTime <= TIMEOUT) {
    if (mod.available() && byteCount < sizeof(values)) {
      values[byteCount++] = mod.read();
      printHexByte(values[byteCount - 1]);
    }
  }
  Serial.println();
  return values[4];
}

byte phosphorous() {
  uint32_t startTime = 0;
  uint8_t byteCount = 0;

  digitalWrite(DE, HIGH);
  digitalWrite(RE, HIGH);
  delay(10);
  mod.write(phos, sizeof(phos));
  mod.flush();
  digitalWrite(DE, LOW);
  digitalWrite(RE, LOW);

  startTime = millis();
  while (millis() - startTime <= TIMEOUT) {
    if (mod.available() && byteCount < sizeof(values)) {
      values[byteCount++] = mod.read();
      printHexByte(values[byteCount - 1]);
    }
  }
  Serial.println();
  return values[4];
}

byte potassium() {
  uint32_t startTime = 0;
  uint8_t byteCount = 0;

  digitalWrite(DE, HIGH);
  digitalWrite(RE, HIGH);
  delay(10);
  mod.write(pota, sizeof(pota));
  mod.flush();
  digitalWrite(DE, LOW);
  digitalWrite(RE, LOW);

  startTime = millis();
  while (millis() - startTime <= TIMEOUT) {
    if (mod.available() && byteCount < sizeof(values)) {
      values[byteCount++] = mod.read();
      printHexByte(values[byteCount - 1]);
    }
  }
  Serial.println();
  return values[4];
}

void printHexByte(byte b) {
  Serial.print((b >> 4) & 0xF, HEX);
  Serial.print(b & 0xF, HEX);
  Serial.print(' ');
}