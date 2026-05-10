#include <Servo.h>
#include <LiquidCrystal_I2C.h>
#include <SPI.h>
#include <MFRC522.h>

#define SS_PIN 10
#define RST_PIN 9

// Known UIDs
String UIDs[] = {
  "8C EB 09 29",
  "C1 4C E7 06",
  "94 4A CA 6C",
  "CE D5 F1 05",
  "77 AF 64 47"
};

// Names
String names[] = {
  "Chashe",
  "Robert",
  "Romero",
  "Gillane",
  "Albert"
};

bool inside[] = { false, false, false, false, false };
int totalUsers = 5;

Servo servo;
LiquidCrystal_I2C lcd(0x27, 16, 2);
MFRC522 rfid(SS_PIN, RST_PIN);

void setup() {
  Serial.begin(9600);

  servo.attach(3);
  servo.write(70);

  lcd.init();
  lcd.backlight();

  SPI.begin();
  rfid.PCD_Init();

  lcd.setCursor(0,0);
  lcd.print("System Ready");
  delay(2000);
  lcd.clear();
}

void loop() {

  lcd.setCursor(0,0);
  lcd.print("Scan your card ");

  // Wait for card
  if (!rfid.PICC_IsNewCardPresent()) return;
  if (!rfid.PICC_ReadCardSerial()) return;

  lcd.clear();
  lcd.print("Scanning...");

  String ID = "";
  ID.reserve(30); // helps memory

  // Read UID
  for (byte i = 0; i < rfid.uid.size; i++) {
    if (rfid.uid.uidByte[i] < 0x10) ID += "0";
    ID += String(rfid.uid.uidByte[i], HEX);
    if (i < rfid.uid.size - 1) ID += " ";
  }

  ID.toUpperCase();

  // 🔥 SEND TO PYTHON
  Serial.print("UID: ");
  Serial.println(ID);

  bool found = false;

  // Check user locally (for LCD + servo only)
  for (int i = 0; i < totalUsers; i++) {
    if (ID == UIDs[i]) {
      found = true;

      if (!inside[i]) {
        inside[i] = true;
        enterMessage(names[i]);
      } else {
        inside[i] = false;
        exitMessage();
      }
      break;
    }
  }

  if (!found) {
    deniedMessage();
  }

  rfid.PICC_HaltA();
  rfid.PCD_StopCrypto1();

  delay(1000);
  lcd.clear();
}

// ================= FUNCTIONS =================

void enterMessage(String name) {
  lcd.clear();
  lcd.setCursor(0,0);
  lcd.print("Welcome");
  lcd.setCursor(0,1);
  lcd.print(name);

  servo.write(160);
  delay(3000);
  servo.write(70);

  lcd.clear();
  lcd.print("Gate Closed");
  delay(1500);
}

void exitMessage() {
  lcd.clear();
  lcd.setCursor(0,0);
  lcd.print("Goodbye!");

  servo.write(160);
  delay(3000);
  servo.write(70);

  lcd.clear();
  lcd.print("Gate Closed");
  delay(1500);
}

void deniedMessage() {
  lcd.clear();
  lcd.setCursor(0,0);
  lcd.print("Access Denied");
  delay(2000);
}