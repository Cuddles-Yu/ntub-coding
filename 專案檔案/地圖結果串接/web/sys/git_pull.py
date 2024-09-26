import subprocess

def colored_echo(color, title, text):
  print(f"<em style='color:{color};'>[{title}] </em><em>{text}</em>")
        
def git_pull(repo_path):
    try:
        subprocess.run(['git', 'config', '--global', '--add', 'safe.directory', repo_path], check=True)
        result = subprocess.run(['git', 'pull', 'origin', 'main'], capture_output=True, text=True)        
        if result.returncode != 0:
            colored_echo('red', 'FAILED', result.stderr)
        else:
            colored_echo('green', 'SUCCESS', result.stdout)         
    except subprocess.CalledProcessError as e:
        colored_echo('red', 'ERROR', e)

if __name__ == "__main__":
    git_pull("C:/xampp/htdocs/project/ntub-coding")
