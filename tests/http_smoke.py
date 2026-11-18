"""Pruebas HTTP contra php -S ya iniciado. Solo biblioteca estándar de Python."""
import sys
from html.parser import HTMLParser
from urllib.parse import urlencode, urlsplit
from urllib.request import urlopen
from urllib.error import HTTPError

BASE = sys.argv[1].rstrip('/') if len(sys.argv) > 1 else 'http://127.0.0.1:8000'
count = 0

def get(path):
    try:
        with urlopen(BASE + path, timeout=8) as r:
            return r.status, r.read().decode('utf-8')
    except HTTPError as e:
        return e.code, e.read().decode('utf-8')

def check(ok, name):
    global count
    if not ok:
        raise AssertionError(name)
    count += 1
    print('OK:', name)

class Links(HTMLParser):
    def __init__(self):
        super().__init__()
        self.links = set()
    def handle_starttag(self, tag, attrs):
        attrs = dict(attrs)
        if tag == 'a' and attrs.get('href', '').startswith('/'):
            self.links.add(attrs['href'])

status, home = get('/index.php')
check(status == 200 and home.count('<article ') == 6, 'Catálogo HTTP con seis tarjetas')
status, water = get('/index.php?' + urlencode({'q':'agua'}))
check(status == 200 and water.count('<article ') == 1 and 'Cada gota cuenta' in water, 'GET cambia resultados por búsqueda PHP')
status, energy = get('/index.php?' + urlencode({'q':'ENERGIA'}))
unicode_status, _ = get('/index.php?' + urlencode({'q':'á'*100}))
check(status == 200 and energy.count('<article ') == 1 and unicode_status == 200, 'Búsqueda normaliza acentos y acepta consulta UTF-8 larga')
status, waste = get('/index.php?' + urlencode({'category':'Residuos'}))
check(status == 200 and waste.count('<article ') == 2, 'Filtro HTTP por categoría')
status, empty = get('/index.php?' + urlencode({'q':'agua','category':'Residuos'}))
check(status == 200 and '<article ' not in empty and 'No encontramos retos' in empty, 'Criterios combinados muestran estado vacío')
attack = '<script>alert(1)</script>'
status, safe = get('/index.php?' + urlencode({'q':attack}))
check(status == 200 and attack not in safe and '&lt;script&gt;' in safe, 'HTML inyectado queda escapado')
for path in ['/index.php?q%5B%5D=agua', '/index.php?' + urlencode({'q':'a'*161}), '/index.php?category=Otra', '/reto.php?id=-1', '/reto.php?id%5B%5D=1', '/reto.php', '/index.php?q=%FF']:
    status, _ = get(path)
    check(status == 400, 'Entrada inválida devuelve 400: ' + path[:65])
status, missing = get('/reto.php?id=999')
check(status == 404 and 'Volver al catálogo' in missing, 'Reto inexistente devuelve 404 con recuperación')
status, detail = get('/reto.php?id=1')
check(status == 200 and '40' in detail and '/participar.php?id=1' in detail, 'Detalle y enlace al formulario contextual')
status, form = get('/participar.php?id=1')
check(status == 200 and 'value="1" selected' in form and 'no envía ni guarda' in form, 'Formulario contextual rotulado como prototipo')
status, pending = get('/pendiente.php')
check(status == 200 and '0 <span' in pending and 'No se creó ningún registro' in pending, 'Estado pendiente de ejemplo indica cero puntos y ausencia de guardado')
status, ranking = get('/ranking.php')
check(status == 200 and 'Personas de ejemplo' in ranking and 'Grupos de ejemplo' in ranking, 'Ranking inicial sintético por persona y grupo')
visited = set()
queue = ['/index.php', '/ranking.php', '/participar.php', '/pendiente.php']
while queue:
    path = queue.pop()
    if path in visited:
        continue
    visited.add(path)
    status, html = get(path)
    check(status == 200, 'Navegación interna sin enlaces rotos: ' + path)
    parser = Links()
    parser.feed(html)
    queue.extend(parser.links - visited)
status, js = get('/assets/app.js')
check(status == 200 and 'aria-expanded' in js and 'reportValidity' in js, 'JavaScript nativo servido con controles de menú y previsualización')
check('name="viewport"' in home and 'md:grid-cols-2' in home and 'lg:grid-cols-3' in home, 'Marcado incluye viewport y columnas responsivas')
for path in ['/src/retos.php', '/storage/.gitkeep']:
    status, _ = get(path)
    check(status == 404, 'Fuera de raíz pública: ' + path)
print(f'TOTAL: {count} pruebas HTTP correctas; {len(visited)} rutas internas recorridas')
print('LÍMITE: sin motor de navegador; no verifica renderizado, menú ejecutado ni carga visual de CDN.')
