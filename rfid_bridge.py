import serial
import mysql.connector
from datetime import datetime
import time

# Database configuration
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': '',  # XAMPP default is empty
    'database': 'rfid_access_system'
}

# Arduino serial port (check in Arduino IDE which COM port)
# Windows: 'COM3', 'COM4', etc.
# Linux/Mac: '/dev/ttyUSB0' or '/dev/ttyACM0'
SERIAL_PORT = 'COM3'  # CHANGE THIS TO YOUR PORT
BAUD_RATE = 9600

def connect_to_database():
    """Establish database connection"""
    try:
        conn = mysql.connector.connect(**db_config)
        return conn
    except mysql.connector.Error as err:
        print(f"Database connection error: {err}")
        return None

def log_access(uid, name, status):
    """Log access attempt to database"""
    conn = connect_to_database()
    if not conn:
        return
    
    cursor = conn.cursor()
    
    try:
        # Insert into access_logs
        cursor.execute("""
            INSERT INTO access_logs (uid, name, status) 
            VALUES (%s, %s, %s)
        """, (uid, name, status))
        
        # Update current_status
        if status == 'ENTER':
            cursor.execute("""
                INSERT INTO current_status (uid, name, is_inside) 
                VALUES (%s, %s, TRUE)
                ON DUPLICATE KEY UPDATE 
                is_inside = TRUE, name = VALUES(name)
            """, (uid, name))
        elif status == 'EXIT':
            cursor.execute("""
                UPDATE current_status 
                SET is_inside = FALSE 
                WHERE uid = %s
            """, (uid,))
        elif status == 'DENIED':
            # Just log denied access
            pass
            
        conn.commit()
        print(f"[{datetime.now()}] Logged: {name} - {status}")
        
    except mysql.connector.Error as err:
        print(f"Database error: {err}")
    finally:
        cursor.close()
        conn.close()

def get_user_info(uid):
    """Get user info from database"""
    conn = connect_to_database()
    if not conn:
        return None
    
    cursor = conn.cursor(dictionary=True)
    try:
        cursor.execute("SELECT name FROM users WHERE uid = %s", (uid,))
        result = cursor.fetchone()
        return result['name'] if result else None
    except mysql.connector.Error as err:
        print(f"Error getting user: {err}")
        return None
    finally:
        cursor.close()
        conn.close()

def main():
    """Main function to read serial data and update database"""
    print("RFID Bridge Started...")
    print(f"Connecting to {SERIAL_PORT} at {BAUD_RATE} baud")
    
    # Track current state for each user
    user_status = {}
    
    try:
        # Connect to Arduino
        ser = serial.Serial(SERIAL_PORT, BAUD_RATE, timeout=1)
        time.sleep(2)  # Wait for Arduino to reset
        print("Connected to Arduino!")
        
        while True:
            if ser.in_waiting > 0:
                line = ser.readline().decode('utf-8').strip()
                
                if line.startswith("UID:"):
                    uid = line.split("UID:")[1].strip()
                    print(f"RFID Scanned: {uid}")
                    
                    # Get user name
                    name = get_user_info(uid)
                    
                    if name:
                        # Determine status based on current state
                        current = user_status.get(uid, False)
                        
                        if not current:
                            status = 'ENTER'
                            user_status[uid] = True
                        else:
                            status = 'EXIT'
                            user_status[uid] = False
                            
                        log_access(uid, name, status)
                    else:
                        # Unknown user
                        log_access(uid, "Unknown", "DENIED")
                        print(f"Access DENIED for UID: {uid}")
            
            time.sleep(0.1)  # Small delay to prevent CPU overload
            
    except serial.SerialException as e:
        print(f"Serial port error: {e}")
        print("\nTroubleshooting:")
        print("1. Check if Arduino is connected via USB")
        print("2. Verify the correct COM port (Windows) or /dev/tty* (Linux/Mac)")
        print("3. Close Arduino IDE serial monitor if open")
        print("4. Check in Device Manager (Windows) or ls /dev/tty* (Linux/Mac)")
    except KeyboardInterrupt:
        print("\nRFID Bridge stopped by user")
    finally:
        if 'ser' in locals() and ser.is_open:
            ser.close()
            print("Serial port closed")

if __name__ == "__main__":
    main()