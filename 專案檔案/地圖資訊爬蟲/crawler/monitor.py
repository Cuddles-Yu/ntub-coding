import subprocess
import sys
import time
from module.return_code import ReturnCode

def run_main_program(input_values):
    try:
        process = subprocess.Popen(
            ["python", "main.py"],
            stdin=subprocess.PIPE,
            stdout=subprocess.PIPE,
            stderr=subprocess.PIPE,
            text=True,
            bufsize=1,
            encoding='utf-8',
            errors='replace'
        )
        # 傳送資料
        process.stdin.write(input_values)
        process.stdin.flush()
        # 讀取輸出
        while True:
            output = process.stdout.readline().replace('\n', '')
            if output:
                if output[-3:] == '...':
                    print(f'\r{output}', end='')
                else:
                    if ' | ' in output:
                        print(f'\n{output}', end='')
                    else:
                        print(f'\r{output}', end='\n')
            elif process.poll() is not None:
                break
        stderr_output = process.stderr.read().replace('\n', '')
        if stderr_output:
            print(stderr_output, file=sys.stderr, end='\n')
        return process.returncode
    except Exception as e:
        print(f"執行主程序時發生錯誤: {e}")
        return -1

if __name__ == "__main__":
    max_retries = 1
    retries = 0
    while retries < max_retries:
        return_code = run_main_program('NO\n')
        match return_code:
            case ReturnCode.Success:
                print("主程序成功執行完畢")
                break
            case _:
                print(f"主程序執行失敗，返回代碼: {return_code}")
                retries += 1
                print(f"重新嘗試執行主程序...（第 {retries} 次重試）")
                time.sleep(2)  # 等待一段時間後再重試
    if retries == max_retries:
        print("主程式已多次執行失敗，放棄重試")
