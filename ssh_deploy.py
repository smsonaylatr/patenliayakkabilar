import paramiko
import sys
import io

sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

c = paramiko.SSHClient()
c.set_missing_host_key_policy(paramiko.AutoAddPolicy())
c.connect('45.11.229.125', username='root', password='jqhg3UwQqfgsrVe9QQnh', timeout=10)

PHP = '/opt/plesk/php/8.3/bin/php'
PROJECT = '/var/www/vhosts/patenliayakkabilar.com/httpdocs'

cmd = f'''cd {PROJECT} && {PHP} artisan tinker --execute="
\\$resp = \\Illuminate\\Support\\Facades\\Http::withHeaders([
    'X-Api-Key' => env('POREGO_API_KEY'),
    'X-Api-Secret' => env('POREGO_API_SECRET'),
    'Accept' => 'application/json',
])->timeout(10)->get(env('POREGO_API_URL').'/orders', ['page' => 0, 'size' => 5]);
echo json_encode(\\$resp->json()['content'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
" 2>&1'''

stdin, stdout, stderr = c.exec_command(cmd, timeout=15)
out = stdout.read().decode('utf-8', errors='replace').strip()
err = stderr.read().decode('utf-8', errors='replace').strip()
print(f"OUT:\n{out}")
if err:
    print(f"ERR:\n{err}")

c.close()
