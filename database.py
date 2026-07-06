import psycopg2
import psycopg2.extras
from psycopg2.pool import ThreadedConnectionPool
from contextlib import contextmanager
from typing import Generator

import config
from parser import TagRead

_SCHEMA_SQL = """
CREATE TABLE IF NOT EXISTS devices (
    sn         VARCHAR(50)  PRIMARY KEY,
    ip         VARCHAR(50),
    first_seen TIMESTAMPTZ  DEFAULT NOW(),
    last_seen  TIMESTAMPTZ  DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS tag_reads (
    id          BIGSERIAL    PRIMARY KEY,
    device_sn   VARCHAR(50)  NOT NULL REFERENCES devices(sn),
    epc         VARCHAR(100) NOT NULL,
    rssi        FLOAT        NOT NULL,
    antenna     INTEGER      NOT NULL,
    direction   VARCHAR(10),
    first_time  BIGINT,
    last_time   BIGINT,
    times       INTEGER      DEFAULT 0,
    received_at TIMESTAMPTZ  DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_tag_reads_epc         ON tag_reads(epc);
CREATE INDEX IF NOT EXISTS idx_tag_reads_received_at ON tag_reads(received_at DESC);
"""

_pool: ThreadedConnectionPool | None = None


def init_pool() -> None:
    global _pool
    _pool = ThreadedConnectionPool(
        minconn=1,
        maxconn=5,
        host     = config.DB_HOST,
        port     = config.DB_PORT,
        dbname   = config.DB_NAME,
        user     = config.DB_USER,
        password = config.DB_PASSWORD,
    )
    with _get_conn() as conn:
        with conn.cursor() as cur:
            cur.execute(_SCHEMA_SQL)
        conn.commit()


@contextmanager
def _get_conn() -> Generator:
    conn = _pool.getconn()
    try:
        yield conn
    finally:
        _pool.putconn(conn)


def upsert_device(sn: str, ip: str) -> None:
    sql = """
        INSERT INTO devices (sn, ip, last_seen)
        VALUES (%s, %s, NOW())
        ON CONFLICT (sn) DO UPDATE
            SET ip = EXCLUDED.ip, last_seen = NOW()
    """
    with _get_conn() as conn:
        with conn.cursor() as cur:
            cur.execute(sql, (sn, ip))
        conn.commit()


def insert_tag_reads(tags: list[TagRead]) -> None:
    if not tags:
        return

    sql = """
        INSERT INTO tag_reads
            (device_sn, epc, rssi, antenna, direction, first_time, last_time, times, received_at)
        VALUES %s
    """
    rows = [
        (t.device_sn, t.epc, t.rssi, t.antenna, t.direction,
         t.first_time, t.last_time, t.times, t.received_at)
        for t in tags
    ]
    with _get_conn() as conn:
        with conn.cursor() as cur:
            psycopg2.extras.execute_values(cur, sql, rows)
        conn.commit()
