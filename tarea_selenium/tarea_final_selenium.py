import os
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import Select
from webdriver_manager.chrome import ChromeDriverManager
import time

# ---------------------------
# CONFIGURACIÓN CHROMEDRIVER
# ---------------------------
chrome_options = Options()
chrome_options.add_argument("--no-sandbox")
chrome_options.add_argument("--disable-dev-shm-usage")
# chrome_options.add_argument("--headless")  # Descomenta si quieres sin GUI

driver = webdriver.Chrome(
    service=Service(ChromeDriverManager().install()),
    options=chrome_options
)

# ---------------------------
# CARPETA DE CAPTURAS
# ---------------------------
os.makedirs("screenshots", exist_ok=True)

def screenshot(name):
    """Toma captura y la guarda en screenshots/"""
    filepath = f"screenshots/{name}.png"
    driver.save_screenshot(filepath)
    print(f"[📸] Captura guardada: {filepath}")

# -----------------------------------
# RESET DE SESIÓN
# -----------------------------------
def reset_session():
    driver.delete_all_cookies()
    driver.get("http://localhost/clinic_rot/public/login.php")
    time.sleep(1)
    screenshot("login_page")

# -----------------------------------
# FUNCIÓN LOGIN
# -----------------------------------
def login(username, password, name="login_action"):
    driver.get("http://localhost/clinic_rot/public/login.php")
    driver.find_element(By.NAME, "username").clear()
    driver.find_element(By.NAME, "username").send_keys(username)
    driver.find_element(By.NAME, "password").clear()
    driver.find_element(By.NAME, "password").send_keys(password)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
    time.sleep(1)
    screenshot(name)

# -----------------------------------
# CRUD COMPLETO AJUSTADO
# -----------------------------------
def crud_operations():
    reset_session()
    login("admin", "admin123", "login_valid_crud")

    driver.get("http://localhost/clinic_rot/public/rotaciones_list.php")
    time.sleep(1)
    screenshot("rotaciones_list_before")

    # -------- CREAR --------
    driver.find_element(By.LINK_TEXT, "➕ Registrar nueva rotación").click()
    time.sleep(1)
    screenshot("rotaciones_create_form")

    # Seleccionar estudiante y centro
    Select(driver.find_element(By.NAME, "estudiante_id")).select_by_index(1)
    Select(driver.find_element(By.NAME, "centro_id")).select_by_index(1)

    # Completar formulario
    driver.find_element(By.NAME, "area").send_keys("Nuevo Registro")
    driver.find_element(By.NAME, "fecha_inicio").send_keys("2025-12-01")
    driver.find_element(By.NAME, "fecha_fin").send_keys("2025-12-15")
    Select(driver.find_element(By.NAME, "turno")).select_by_visible_text("Mañana")
    Select(driver.find_element(By.NAME, "estado")).select_by_visible_text("Pendiente")
    driver.find_element(By.NAME, "observaciones").send_keys("Registro de prueba automatizado")

    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
    time.sleep(1)
    screenshot("rotaciones_after_create")

    # -------- LEER --------
    rows = driver.find_elements(By.CSS_SELECTOR, "table tbody tr")
    assert len(rows) > 0, "No se encontraron registros después de crear"
    screenshot("rotaciones_after_read")

    # -------- ACTUALIZAR --------
    rows[0].find_element(By.LINK_TEXT, "Editar").click()
    time.sleep(1)
    driver.find_element(By.NAME, "area").clear()
    driver.find_element(By.NAME, "area").send_keys("Registro Actualizado")
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
    time.sleep(1)
    screenshot("rotaciones_after_update")

    # -------- ELIMINAR --------
    rows = driver.find_elements(By.CSS_SELECTOR, "table tbody tr")
    rows[0].find_element(By.LINK_TEXT, "Eliminar").click()
    driver.switch_to.alert.accept()
    time.sleep(1)
    screenshot("rotaciones_after_delete")

# -----------------------------------
# PRUEBAS
# -----------------------------------
def test_login():
    reset_session()
    login("admin", "admin123", "login_valid")
    assert "dashboard" in driver.current_url.lower()

    reset_session()
    login("admin34", "1234", "login_invalid")
    assert "error=1" in driver.current_url.lower()
    assert "usuario o contraseña incorrectos" in driver.page_source.lower()

    reset_session()
    login("", "", "login_empty")
    assert "login.php" in driver.current_url.lower()

def test_crud_operations():
    crud_operations()

def run_tests():
    print("Ejecutando pruebas...")
    test_login()
    test_crud_operations()
    print("✔ Todas las pruebas pasaron correctamente")

# -----------------------------------
# MAIN
# -----------------------------------
if __name__ == "__main__":
    run_tests()
    driver.quit()
