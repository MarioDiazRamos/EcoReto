"""Prueba HTTP de moderación con sesiones independientes y una SQLite temporal."""
import re
import shutil
import socket
import sqlite3
import subprocess
import tempfile
import time
from contextlib import closing
from http.cookiejar import CookieJar
from pathlib import Path
from urllib.error import HTTPError, URLError
from urllib.parse import urlencode
from urllib.request import HTTPCookieProcessor, build_opener

ROOT = Path(__file__).resolve().parents[1]


class Sesion:
    def __init__(self, base):
        self.base = base
        self.cliente = build_opener(HTTPCookieProcessor(CookieJar()))

    def pagina(self, ruta, datos=None, esperado=200):
        cuerpo = urlencode(datos).encode() if datos is not None else None
        try:
            respuesta = self.cliente.open(self.base + ruta, cuerpo, timeout=5)
        except HTTPError as error:
            respuesta = error
        with respuesta:
            pagina = respuesta.read().decode()
            assert respuesta.status == esperado, (ruta, respuesta.status, esperado)
            return pagina

    def enviar(self, ruta, datos, esperado=200):
        token = re.search(r'name="csrf"\s+value="([^"]+)"', self.pagina(ruta))[1]
        return self.pagina(ruta, {"csrf": token, **datos}, esperado)


def probar(base, raiz):
    autor, moderador = Sesion(base), Sesion(base)
    assert 'name="clave"' in autor.pagina("/moderacion.php")
    autor.pagina("/index.php")
    cuentas = subprocess.check_output(["php", "scripts/crear_usuarios_demo.php"], cwd=raiz).decode()
    for sesion, usuario in [(autor, "rodo_demo"), (moderador, "mod_demo")]:
        clave = re.search(rf"{usuario} .*Clave: (\w+)", cuentas)[1]
        assert "Sesión iniciada" in sesion.enviar("/login.php", {"usuario": usuario, "clave": clave})

    for sesion, reto in [(autor, 1), (autor, 2), (moderador, 1)]:
        sesion.enviar("/participar.php", {"reto_id": reto, "evidencia": f"Evidencia registrada del reto {reto}."})

    ruta = "/moderacion.php"
    decision = {"revisar": 1, "participacion_id": 1, "decision": "aprobada", "motivo": "Verificado"}
    autor.pagina(ruta, decision, 403)
    moderador.pagina(ruta, decision, 403)  # Sin token
    panel = moderador.pagina(ruta)
    assert panel.count('name="revisar"') == 2
    moderador.enviar(ruta, {**decision, "participacion_id": 3}, 409)

    with closing(sqlite3.connect(raiz / "storage/ecoreto.sqlite")) as db:
        # Simular un fallo en el último paso de la aprobación.
        db.execute("CREATE TRIGGER fallo BEFORE INSERT ON puntos BEGIN SELECT RAISE(ABORT, 'prueba'); END")
        moderador.enviar(ruta, decision, 500)
        assert db.execute("SELECT estado FROM participaciones WHERE id=1").fetchone() == ("pendiente",)
        assert db.execute("SELECT COUNT(*) FROM validaciones").fetchone() == (0,)
        db.execute("DROP TRIGGER fallo")

        moderador.enviar(ruta, decision)
        moderador.enviar(ruta, decision, 409)
        moderador.enviar(ruta, {**decision, "participacion_id": 2, "decision": "rechazada"})
        puntos = db.execute("SELECT puntos FROM retos WHERE id=1").fetchone()[0]
        assert db.execute("SELECT participacion_id, valor FROM puntos").fetchall() == [(1, puntos)]
        assert db.execute("SELECT COUNT(*) FROM validaciones").fetchone() == (2,)
        db.execute("UPDATE retos SET puntos=puntos+1 WHERE id=1")
        db.commit()
        assert f"Puntos otorgados: {puntos}</p>" in autor.pagina("/pendiente.php?id=1")
        assert "Puntos otorgados: 0</p>" in autor.pagina("/pendiente.php?id=2")
        assert "Verificado" in autor.pagina("/pendiente.php")
    assert 'name="revisar"' not in moderador.pagina(ruta)


if __name__ == "__main__":
    with tempfile.TemporaryDirectory() as carpeta:
        raiz = Path(carpeta)
        for nombre in ["public", "src", "database", "scripts"]:
            shutil.copytree(ROOT / nombre, raiz / nombre)
        (raiz / "storage").mkdir()
        with socket.socket() as puerto:
            puerto.bind(("127.0.0.1", 0))
            direccion = f"127.0.0.1:{puerto.getsockname()[1]}"
        base = "http://" + direccion
        servidor = subprocess.Popen(["php", "-d", "display_errors=0", "-d",
                                     f'session.save_path="{raiz.as_posix()}"', "-S", direccion, "-t", "public"],
                                    cwd=raiz, stderr=subprocess.DEVNULL)
        try:
            for intento in range(50):
                try:
                    Sesion(base).pagina("/login.php")
                    break
                except URLError:
                    if servidor.poll() is not None or intento == 49:
                        raise
                    time.sleep(0.1)
            probar(base, raiz)
        finally:
            servidor.terminate()
            servidor.wait()
    print("OK: roles, CSRF, aprobación, rechazo, autoría, duplicados, reversión e historial.")
