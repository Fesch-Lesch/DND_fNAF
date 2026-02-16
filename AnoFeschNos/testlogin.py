import json
import time
import os
import shutil
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from fpdf import FPDF

# Конфигурация
CONFIG_FILE = "test_data.json"
REPORT_DIR = "test_reports"
FONT_PATH = "times.ttf"  # Файл шрифта с поддержкой кириллицы (Times New Roman)

# Инициализация папок
if os.path.exists(REPORT_DIR):
    shutil.rmtree(REPORT_DIR)
os.makedirs(REPORT_DIR)

# Загрузка конфига
if not os.path.exists(CONFIG_FILE):
    print(f"❌ ОШИБКА: Файл '{CONFIG_FILE}' не найден.")
    exit()

with open(CONFIG_FILE, 'r', encoding='utf-8') as f:
    config = json.load(f)

BASE_URL = config['urls']['login']
LOGOUT_URL = config['urls']['logout']
COMMON_PASSWORD = config['common_password']
USERS = config['users']

# Настройка браузера
options = webdriver.ChromeOptions()
driver = webdriver.Chrome(options=options)
driver.set_window_size(1200, 900)

report_data = []

def run_test(username, password, description, expect_success, filename_prefix):
    try:
        driver.get(BASE_URL)
        WebDriverWait(driver, 3).until(EC.presence_of_element_located((By.ID, "username")))
        
        user_input = driver.find_element(By.ID, "username")
        pass_input = driver.find_element(By.ID, "password")
        submit_btn = driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
        
        user_input.clear()
        if username: user_input.send_keys(username)
        
        pass_input.clear()
        if password: pass_input.send_keys(password)
        
        # Скриншот
        screenshot_path = f"{REPORT_DIR}/{filename_prefix}.png"
        driver.save_screenshot(screenshot_path)
        
        submit_btn.click()
        
        status = "ПРОВАЛ"
        if expect_success:
            try:
                WebDriverWait(driver, 3).until(EC.url_contains("dashboard.php"))
                status = "УСПЕХ"
            except:
                status = "ПРОВАЛ (Нет редиректа)"
        else:
            try:
                # Проверка на сообщение об ошибке (для неверных данных) или отсутствие перехода (для пустых полей)
                if username and password:
                    WebDriverWait(driver, 2).until(EC.presence_of_element_located((By.CLASS_NAME, "alert-error")))
                    status = "УСПЕХ (Ошибка отображена)"
                else:
                    if "login.php" in driver.current_url: status = "УСПЕХ (Блокировка)"
            except:
                status = "ПРОВАЛ (Неожиданный вход)"

        return status, screenshot_path

    except Exception as e:
        print(f"Ошибка в тесте: {e}")
        return "ОШИБКА", None

# ================= ЗАПУСК ТЕСТОВ =================

print(f"🚀 Запуск тестов на {BASE_URL}...\n")

# 1. Негативные сценарии
scenarios = [
    ("1_wrong_pass", "admin", "wrong123", "Неверный пароль"),
    ("2_wrong_login", "fakeuser", "password", "Неверный логин"),
    ("3_empty_pass", "admin", "", "Пустой пароль"),
]

for prefix, u, p, desc in scenarios:
    print(f"Тест: {desc}...")
    res, screen = run_test(u, p, desc, False, prefix)
    display_pass = p if p else "[Пусто]"
    report_data.append({
        "desc": desc,
        "input": f"Логин: '{u}' | Пароль: '{display_pass}'",
        "result": res,
        "img": screen
    })

# 2. Позитивные сценарии (пользователи из JSON)
print("--- Проверка пользователей из JSON ---")
for i, user in enumerate(USERS, 1):
    login = user['login']
    role = user.get('expected_role', 'user') # Получаем роль или дефолтное значение
    password = user.get('password', COMMON_PASSWORD) # Используем пароль из JSON или общий
    
    desc = f"Вход: {login} ({role})"
    print(f"Тест: {desc}...")
    
    res, screen = run_test(login, password, desc, True, f"valid_{i}")
    
    report_data.append({
        "desc": desc,
        "input": f"Логин: '{login}' | Пароль: '********'", # Скрываем пароль в отчете
        "result": res,
        "img": screen
    })
    
    if "УСПЕХ" in res:
        driver.get(LOGOUT_URL)

driver.quit()

# ================= ГЕНЕРАЦИЯ PDF (Русский язык) =================
print("\n📄 Генерация PDF отчета...")

class PDF(FPDF):
    def header(self):
        self.set_font('TimesRus', '', 16)
        self.cell(0, 10, 'Отчет о тестировании системы DnD', 0, 1, 'C')
        self.ln(10)

    def footer(self):
        self.set_y(-15)
        self.set_font('TimesRus', '', 10)
        self.cell(0, 10, f'Страница {self.page_no()}', 0, 0, 'C')

try:
    pdf = PDF()
    pdf.set_auto_page_break(auto=True, margin=20)
    
    # Регистрация шрифта (ОБЯЗАТЕЛЬНО для кириллицы)
    pdf.add_font('TimesRus', '', FONT_PATH, uni=True)
    pdf.set_font("TimesRus", size=14)
    pdf.set_text_color(0, 0, 0)

    for item in report_data:
        pdf.add_page()
        
        # Формирование текста
        text = (
            f"Сценарий: {item['desc']}\n"
            f"Входные данные: {item['input']}\n"
            f"Результат теста: {item['result']}"
        )
        
        pdf.set_left_margin(12.5) 
        pdf.multi_cell(0, 8, text, align='J') 
        
        pdf.ln(5)
        
        # Вставка скриншота
        if item['img'] and os.path.exists(item['img']):
            pdf.image(item['img'], x=15, w=180)
        
    output_file = "otchet_dnd_rus.pdf"
    pdf.output(output_file)
    print(f"✅ Готово! Отчет сохранен как: {output_file}")

except RuntimeError:
    print(f"❌ ОШИБКА: Не найден файл шрифта {FONT_PATH}!")
    print("Пожалуйста, скопируйте файл times.ttf (или arial.ttf) в папку со скриптом.")
except Exception as e:
    print(f"❌ ОШИБКА при создании PDF: {e}")