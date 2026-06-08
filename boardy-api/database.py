import aiomysql

DB_CONFIG = {
    # В Docker MySQL будет доступен по имени сервиса "mysql"
    "host": "mysql",
    "port": 3306,
    "user": "boardy",
    "password": "1",
    "db": "boardy_api",
    "charset": "utf8mb4",
}


async def get_db():
    return await aiomysql.connect(
        **DB_CONFIG,
        autocommit=True,
    )


async def db_query(sql, *params):
    conn = await get_db()
    try:
        async with conn.cursor(aiomysql.DictCursor) as cur:
            await cur.execute(sql, params)
            return await cur.fetchall()
    finally:
        conn.close()


async def db_query_one(sql, *params):
    conn = await get_db()
    try:
        async with conn.cursor(aiomysql.DictCursor) as cur:
            await cur.execute(sql, params)
            return await cur.fetchone()
    finally:
        conn.close()


async def db_insert(sql, *params):
    conn = await get_db()
    try:
        async with conn.cursor() as cur:
            await cur.execute(sql, params)
            return cur.lastrowid
    finally:
        conn.close()


async def db_execute(sql, *params):
    conn = await get_db()
    try:
        async with conn.cursor() as cur:
            await cur.execute(sql, params)
            return cur.rowcount
    finally:
        conn.close()
