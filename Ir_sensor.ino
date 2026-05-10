int irSensor1 = 12;
int irSensor2 = 11;
int buzzer = 7;

// Variables for edge detection
bool lastObstacle1 = false;  // false = no obstacle (reading 1), true = obstacle (reading 0)
bool lastObstacle2 = false;
bool beeping = false;
unsigned long beepStartTime = 0;
const int beepDuration = 150;  // milliseconds the buzzer stays ON per beep

void setup()
{
    Serial.begin(9600);
    pinMode(irSensor1, INPUT);
    pinMode(irSensor2, INPUT);
    pinMode(buzzer, OUTPUT);
    digitalWrite(buzzer, LOW);
}

void loop()
{
    int value1 = digitalRead(irSensor1);
    int value2 = digitalRead(irSensor2);

    // Convert reading to obstacle flag (0 = obstacle)
    bool obstacle1 = (value1 == 0);
    bool obstacle2 = (value2 == 0);

    // Print sensor values
    Serial.println("");
    Serial.print("Sensor 1 Value = ");
    Serial.print(value1);
    Serial.print("  |  Sensor 2 Value = ");
    Serial.println(value2);

    // --- Handle beep triggering on new detection (rising edge) ---
    // For sensor 1: if it was NOT obstructed and now IS obstructed
    if (!lastObstacle1 && obstacle1) {
        startBeep();
    }
    // For sensor 2
    if (!lastObstacle2 && obstacle2) {
        startBeep();
    }

    // --- Manage the beep duration (non-blocking) ---
    if (beeping && (millis() - beepStartTime >= beepDuration)) {
        digitalWrite(buzzer, LOW);
        beeping = false;
    }

    // --- Save current states for next loop ---
    lastObstacle1 = obstacle1;
    lastObstacle2 = obstacle2;

    delay(50);
}

void startBeep() {
    digitalWrite(buzzer, HIGH);
    beeping = true;
    beepStartTime = millis();
}