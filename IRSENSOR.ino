#include <Servo.h>

Servo tap_servo;

// Pin definitions
int sensor_pin = 4;
int tap_servo_pin = 5;
int buzzer_pin = 6;

int val;

void setup() {
  pinMode(sensor_pin, INPUT);
  tap_servo.attach(tap_servo_pin);
  // No need for pinMode on buzzer when using tone()
}

void loop() {
  val = digitalRead(sensor_pin);

  if (val == 0) {
    // No object detected
    tap_servo.write(0);       // Servo to 0°
    noTone(buzzer_pin);       // Turn buzzer off
    delay(2000);               // Pause for 500ms
  }
  
  if (val == 1) {
    // Object detected
    tap_servo.write(180);     // Servo to 180°
    tone(buzzer_pin, 2000);   // Beep at 2000 Hz
    delay(500);               // Keep beeping and servo position for 500ms
    noTone(buzzer_pin);       // Stop beeping (optional – can also leave beeping until detection ends)
    delay(0.5);               // Extra delay before re-checking sensor
  }
}