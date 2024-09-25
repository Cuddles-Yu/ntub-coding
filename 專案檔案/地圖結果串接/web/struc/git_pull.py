import subprocess

def colored_echo(color, title, text):
  print(f"<w style='color:{color};'>[{title}] </w>{text}")

def configure_safe_directory(repo_path):
    try:
        subprocess.run(['git', 'config', '--global', '--add', 'safe.directory', repo_path], check=True)
    except subprocess.CalledProcessError as e:
        colored_echo('red', 'ERROR', e)
        
def git_pull():
    try:
        result = subprocess.run(['git', 'pull', 'origin', 'main'], capture_output=True, text=True)        
        if result.returncode != 0:
            colored_echo('red', 'FAILED', result.stderr)
        else:
            colored_echo('green', 'SUCCESS', result.stdout)         
    except subprocess.CalledProcessError as e:
        colored_echo('red', 'ERROR', e)

if __name__ == "__main__":
    configure_safe_directory("C:/xampp/htdocs/project/ntub-coding")
    git_pull()
